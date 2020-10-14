<?php

declare(strict_types=1);

namespace App\Middleware;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Laminas\Diactoros\Response;
use Mezzio\Router\RouteResult;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_intersect;
use function count;
use function explode;
use function in_array;
use function md5;
use function sprintf;

class CacheMiddleware implements MiddlewareInterface
{
    public const CACHE_HEADER_NAME = 'X-Primo-Cache';

    /** @var CacheItemPoolInterface */
    private $cache;

    /** @var bool */
    private $enabled;

    /** @var string[] */
    private $unCacheAbleRouteNames;

    /** @var string[] */
    private $cacheableMethods = [
        RequestMethod::METHOD_GET,
        RequestMethod::METHOD_HEAD,
    ];

    /**
     * @param string[] $unCacheAbleRouteNames
     */
    public function __construct(CacheItemPoolInterface $cache, array $unCacheAbleRouteNames, bool $enabled = true)
    {
        $this->cache = $cache;
        $this->enabled = $enabled;
        $this->unCacheAbleRouteNames = $unCacheAbleRouteNames;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cachedResponse = $this->getCachedResponse($request);
        if ($cachedResponse) {
            return $cachedResponse->withHeader(self::CACHE_HEADER_NAME, 'HIT');
        }

        $response = $handler->handle($request);

        if (! $this->isCacheAbleRequest($request)) {
            return $response->withHeader(self::CACHE_HEADER_NAME, 'BYPASS');
        }

        if ($this->isCacheAbleResponse($response)) {
            $this->cacheResponse($request, $response);
        }

        return $response->withHeader(self::CACHE_HEADER_NAME, 'MISS');
    }

    private function cacheItem(ServerRequestInterface $request): CacheItemInterface
    {
        $key = sprintf('http-%s', md5((string) $request->getUri()));

        return $this->cache->getItem($key);
    }

    private function getCachedResponse(ServerRequestInterface $request): ?ResponseInterface
    {
        $item = $this->cacheItem($request);
        if (! $item->isHit()) {
            return null;
        }

        $response = new Response();
        $data = $item->get();
        $response = $response->withStatus($data['status']);
        foreach ($data['headers'] as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        $response->getBody()->write($data['body']);

        return $response;
    }

    private function isCacheAbleRequest(ServerRequestInterface $request): bool
    {
        if (! $this->enabled) {
            return false;
        }

        if (! in_array($request->getMethod(), $this->cacheableMethods)) {
            return false;
        }

        $routeResult = $request->getAttribute(RouteResult::class);

        return $routeResult instanceof RouteResult && $this->isCacheAbleRoute($routeResult);
    }

    private function isTemporaryRedirect(int $status): bool
    {
        return $status === 302 || $status === 303 || $status === 307;
    }

    private function isCacheAbleResponse(ResponseInterface $response): bool
    {
        // Don't cache server-side error responses
        $status = $response->getStatusCode();
        if ($status >= 500 || $status < 200) {
            return false;
        }

        // Don't cache temporary redirects
        if ($this->isTemporaryRedirect($status)) {
            return false;
        }

        // Only cache 404|410 errors in the 400 range
        if ($status >= 400 && $status !== 404 && $status !== 410) {
            return false;
        }

        // Finally only cache responses where cache-control headers do not forbid it.
        $cacheControl = $response->getHeader('Cache-Control');
        $abortTokens  = ['private', 'no-cache', 'no-store'];

        return count(array_intersect($abortTokens, $cacheControl)) === 0;
    }

    private function isCacheAbleRoute(RouteResult $routeResult): bool
    {
        $name = $routeResult->getMatchedRouteName();
        if (! $name) {
            return true;
        }

        return ! in_array($name, $this->unCacheAbleRouteNames, true);
    }

    private function cacheResponse(ServerRequestInterface $request, ResponseInterface $response): void
    {
        $item = $this->cacheItem($request);
        $cacheControl = $response->getHeader('Cache-Control');
        foreach ($cacheControl as $value) {
            $parts = explode('=', $value);
            if (count($parts) === 2 && $parts[0] === 'max-age') {
                $item->expiresAfter((int) $parts[1]);
                break;
            }
        }

        $item->set([
            'headers' => $response->getHeaders(),
            'body'    => (string) $response->getBody(),
            'status'  => $response->getStatusCode(),
        ]);
        $this->cache->save($item);
    }
}
