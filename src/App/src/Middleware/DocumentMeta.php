<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Content\HasWebPageMetaData;
use Laminas\View\Helper\HeadMeta;
use Laminas\View\Helper\HeadTitle;
use Prismic\Document;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DocumentMeta implements MiddlewareInterface
{
    /** @var HeadTitle */
    private $headTitleHelper;
    /** @var HeadMeta */
    private $metaHelper;

    public function __construct(HeadTitle $headTitleHelper, HeadMeta $metaHelper)
    {
        $this->headTitleHelper = $headTitleHelper;
        $this->metaHelper = $metaHelper;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $document = $request->getAttribute(Document::class);

        if (! $document instanceof HasWebPageMetaData) {
            return $handler->handle($request);
        }

        $this->headTitleHelper->getContainer()->set($document->metaTitle());
        $this->metaHelper->setName('description', $document->metaDescription());
        $robots = $document->robotsMeta();
        if ($robots) {
            $this->metaHelper->setName('robots', $robots);
        }

        return $handler->handle($request);
    }
}
