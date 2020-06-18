<?php
declare(strict_types=1);

namespace AppTest\Unit\Middleware;

use App\Content\ErrorDocumentLocator;
use App\Middleware\ErrorResponseGenerator;
use AppTest\Unit\Framework\TestCase;
use Exception;
use Laminas\Diactoros\Response\TextResponse;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Prismic\Document;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorResponseGeneratorTest extends TestCase
{
    /** @var ErrorDocumentLocator|MockObject */
    private $documentLocator;
    /** @var TemplateRendererInterface|MockObject */
    private $renderer;

    protected function setUp() : void
    {
        parent::setUp();
        $this->documentLocator = $this->createMock(ErrorDocumentLocator::class);
        $this->renderer = $this->createMock(TemplateRendererInterface::class);
    }

    private function renderExpectsDocument(Document $document) : void
    {
        $this->renderer->expects($this->once())
            ->method('addDefaultParam')
            ->with(TemplateRendererInterface::TEMPLATE_ALL, 'document', $document);
    }

    private function rendererWillRenderTemplateWith(string $templateName) : void
    {
        $this->renderer->expects($this->once())
            ->method('render')
            ->with($templateName)
            ->willReturn('RENDERED CONTENT');
    }

    private function responseGenerator(?callable $fallback = null) : ErrorResponseGenerator
    {
        $fallback = $fallback ?: function () : void {
            $this->fail('The fallback response generator was called');
        };

        return new ErrorResponseGenerator(
            $this->renderer,
            'template-name',
            $this->documentLocator,
            $fallback
        );
    }

    /** @return int[][] */
    public function errorCodes() : iterable
    {
        return [
            'Less than 100' => [0, 500],
            '100 Range' => [101, 500],
            '200 Range' => [200, 500],
            '300 Range' => [302, 500],
            '401' => [401, 401],
            '402' => [402, 402],
            '404' => [404, 404],
            '500' => [500, 500],
            'Above 500' => [600, 500],
        ];
    }

    /** @dataProvider errorCodes */
    public function testThatHttpStatusCodeMatchesErrorCodeWithMatchingDocument(int $errorCode, int $expectedHttpCode) : void
    {
        $error = new Exception('Error', $errorCode);
        $request = $this->serverRequest('/foo');
        $response = new TextResponse('Content');
        $document = $this->createMock(Document::class);

        $this->documentLocator
            ->expects($this->once())
            ->method('hasCode')
            ->with($errorCode)
            ->willReturn(true);

        $this->documentLocator
            ->expects($this->once())
            ->method('byCode')
            ->with($errorCode)
            ->willReturn($document);

        $this->renderExpectsDocument($document);
        $this->rendererWillRenderTemplateWith('template-name');

        $generator = $this->responseGenerator();
        $generatedResponse = $generator($error, $request, $response);

        self::assertResponseHasStatus($generatedResponse, $expectedHttpCode);
    }

    /** @dataProvider errorCodes */
    public function testExpectedHttpStatusCodeWhenTheDefaultDocumentIsUsed(int $errorCode, int $expectedHttpCode) : void
    {
        $error = new Exception('Error', $errorCode);
        $request = $this->serverRequest('/foo');
        $response = new TextResponse('Content');
        $document = $this->createMock(Document::class);

        $this->documentLocator
            ->expects($this->once())
            ->method('hasCode')
            ->with($errorCode)
            ->willReturn(false);

        $this->documentLocator
            ->expects($this->once())
            ->method('byCode')
            ->with($expectedHttpCode)
            ->willReturn($document);

        $this->renderExpectsDocument($document);
        $this->rendererWillRenderTemplateWith('template-name');

        $generator = $this->responseGenerator();
        $generatedResponse = $generator($error, $request, $response);

        self::assertResponseHasStatus($generatedResponse, $expectedHttpCode);
    }

    public function testThatTheFallbackGeneratorIsCalledWhenGeneratingErrorContentFails() : void
    {
        $error = new Exception('Error', 0);
        $libraryError = new Exception('Whatever', 123);
        $request = $this->serverRequest('/foo');
        $response = new TextResponse('Content');
        $expectedResponse = new TextResponse('Foo', 500);

        $this->documentLocator
            ->expects($this->once())
            ->method('hasCode')
            ->with(0)
            ->willReturn(false);

        $this->documentLocator
            ->expects($this->once())
            ->method('byCode')
            ->with(500)
            ->willThrowException($libraryError);

        $fallback = function (
            Throwable $receivedError,
            RequestInterface $receivedRequest,
            ResponseInterface $receivedResponse
        ) use (
            $error,
            $request,
            $expectedResponse
        ) : ResponseInterface {
            $this->assertSame($error, $receivedError);
            $this->assertSame($request, $receivedRequest);
            self::assertMessageBodyMatches($receivedResponse, $this->equalTo('Content'));

            return $expectedResponse;
        };

        $generator = $this->responseGenerator($fallback);
        $generatedResponse = $generator($error, $request, $response);

        $this->assertSame($expectedResponse, $generatedResponse);
    }
}
