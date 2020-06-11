<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Content\ErrorDocumentLocator;
use Laminas\Stratigility\Utils;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ErrorResponseGenerator
{
    /** @var TemplateRendererInterface */
    private $renderer;
    /** @var string */
    private $template;
    /** @var ErrorDocumentLocator */
    private $locator;
    /** @var callable */
    private $fallbackResponseGenerator;

    public function __construct(
        TemplateRendererInterface $renderer,
        string $template,
        ErrorDocumentLocator $locator,
        callable $fallbackResponseGenerator
    ) {
        $this->renderer = $renderer;
        $this->template = $template;
        $this->locator = $locator;
        $this->fallbackResponseGenerator = $fallbackResponseGenerator;
    }

    public function __invoke(
        Throwable $e,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface {
        $status = Utils::getStatusCode($e, $response);
        $response = $response->withStatus($status);
        try {
            $response->getBody()->write($this->generateErrorContent($status));

            return $response;
        } catch (Throwable $error) {
            return ($this->fallbackResponseGenerator)($e, $request, $response);
        }
    }

    private function generateErrorContent(int $code = 500) : string
    {
        $document = $this->locator->byCode($code);
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'document',
            $document
        );

        return $this->renderer->render($this->template);
    }
}
