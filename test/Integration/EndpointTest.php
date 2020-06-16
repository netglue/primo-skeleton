<?php
declare(strict_types=1);

namespace AppTest\Integration;

use AppTest\Integration\Framework\TestCase;
use Laminas\Diactoros\ServerRequestFactory;
use Prismic\ApiClient;
use Prismic\Document;
use Prismic\LinkResolver;
use Prismic\Predicate;

use function uniqid;

class EndpointTest extends TestCase
{
    /** @return mixed[] */
    public function pageProvider() : iterable
    {
        $container = $this->getContainer();
        $apiClient = $container->get(ApiClient::class);
        $query = $apiClient->createQuery()->query(Predicate::at('document.type', 'page'));
        $pages = $apiClient->query($query);
        foreach ($pages as $page) {
            yield $page->id() => [$page];
        }
    }

    public function testHomePageIsAccessible() : void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $response = $this->handle($request);
        self::assertResponseHasStatus($response, 200);
    }

    /** @dataProvider pageProvider */
    public function testThatAllPagesAreRoutable(Document $document) : void
    {
        $this->getApplication();
        $container = $this->getContainer();
        $linkResolver = $container->get(LinkResolver::class);

        $url = $linkResolver->resolve($document->asLink());
        $this->assertNotNull($url, 'No url could be constructed for the given document');

        $request = (new ServerRequestFactory())->createServerRequest('GET', $url);
        $response = $this->handle($request);
        self::assertResponseHasStatus($response, 200);
    }

    public function testNotFound() : void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/' . uniqid('not_found', false));
        $response = $this->handle($request);
        self::assertResponseHasStatus($response, 404);
    }

    public function testException() : void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/exceptional');
        $response = $this->handle($request);
        self::assertResponseHasStatus($response, 500);
    }
}
