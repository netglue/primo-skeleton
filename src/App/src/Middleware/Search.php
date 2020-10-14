<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\RuntimeError;
use App\SearchService;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_numeric;
use function is_string;

class Search implements RequestHandlerInterface
{
    public const TERM_PARAM = 'term';

    /** @var TemplateRendererInterface */
    private $renderer;
    /** @var SearchService */
    private $searchService;
    /** @var int */
    private $perPage;

    public function __construct(
        TemplateRendererInterface $renderer,
        SearchService $searchService,
        int $perPage = 10
    ) {
        $this->renderer = $renderer;
        $this->searchService = $searchService;
        $this->perPage = $perPage;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        $term = (string) ($query[self::TERM_PARAM] ?? '');
        $page = $this->pageNumber($this->assertRouteResult($request));

        $template = $this->assertTemplate($request);

        $resultSet = $this->searchService->query($term, '*', $page, $this->perPage);

        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'searchTerm',
            $term,
        );

        return new HtmlResponse(
            $this->renderer->render($template, [
                'searchTerm' => $term,
                'page' => $page,
                'results' => $resultSet,
            ])
        );
    }

    private function assertRouteResult(ServerRequestInterface $request): RouteResult
    {
        $result = $request->getAttribute(RouteResult::class);
        if (! $result instanceof RouteResult) {
            throw new RuntimeError('Routing has not occurred, or no route was matched', 500);
        }

        return $result;
    }

    private function pageNumber(RouteResult $routeResult): int
    {
        $params = $routeResult->getMatchedParams();
        $page = is_numeric($params['page']) ? $params['page'] : 1;

        return (int) $page;
    }

    private function assertTemplate(ServerRequestInterface $request): string
    {
        $template = $request->getAttribute('template');
        if (! is_string($template) || empty($template)) {
            throw new RuntimeError('No template has been defined for this route');
        }

        return $template;
    }
}
