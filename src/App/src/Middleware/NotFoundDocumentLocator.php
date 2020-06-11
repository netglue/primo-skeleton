<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Content\SingleDocumentLocator;
use Primo\Middleware\PrismicTemplate;
use Prismic\Document;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NotFoundDocumentLocator implements MiddlewareInterface
{
    /** @var SingleDocumentLocator */
    private $documentLocator;
    /** @var string */
    private $templateAttribute;
    /** @var string */
    private $template;

    public function __construct(SingleDocumentLocator $documentLocator, string $template, string $templateAttribute = PrismicTemplate::DEFAULT_TEMPLATE_ATTRIBUTE)
    {
        $this->documentLocator = $documentLocator;
        $this->template = $template;
        $this->templateAttribute = $templateAttribute;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $document = ($this->documentLocator)();
        if (! $document instanceof Document) {
            return $handler->handle($request);
        }

        $request = $request
            ->withAttribute(Document::class, $document)
            ->withAttribute($this->templateAttribute, $this->template);

        return $handler->handle($request)->withStatus(404);
    }
}
