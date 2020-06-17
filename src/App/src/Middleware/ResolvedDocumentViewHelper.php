<?php
declare(strict_types=1);

namespace App\Middleware;

use App\ViewHelper\ResolvedDocument;
use Prismic\Document;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResolvedDocumentViewHelper implements MiddlewareInterface
{
    /** @var ResolvedDocument */
    private $helper;

    public function __construct(ResolvedDocument $helper)
    {
        $this->helper = $helper;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $document = $request->getAttribute(Document::class);
        if ($document instanceof Document) {
            $this->helper->set($document);
        }

        return $handler->handle($request);
    }
}
