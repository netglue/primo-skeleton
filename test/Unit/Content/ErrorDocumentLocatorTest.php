<?php
declare(strict_types=1);

namespace AppTest\Unit\Content;

use App\Content\ErrorDocumentLocator;
use App\Content\SingleDocumentLocator;
use App\Exception\InvalidArgument;
use App\Exception\RuntimeError;
use AppTest\Unit\Framework\TestCase;
use Prismic\Document;

class ErrorDocumentLocatorTest extends TestCase
{
    public function testConstructorThrowsExceptionWhenTheMapContainsInvalidEntries() : void
    {
        $default = $this->createMock(SingleDocumentLocator::class);
        $map = [0 => 'Not Valid'];
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('The error document status code mapping values must all be instances of');

        new ErrorDocumentLocator($default, $map);
    }

    public function testConstructorThrowsExceptionWhenTheMapContainsNonIntegerKeys() : void
    {
        $default = $this->createMock(SingleDocumentLocator::class);
        $map = ['whoops' => $default];
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('The error document status code mapping must have integer keys');

        new ErrorDocumentLocator($default, $map);
    }

    public function testThatAnExceptionIsThrownWhenTheDefaultDocumentLocatorFailsToProvideADocument() : void
    {
        $default = $this->createMock(SingleDocumentLocator::class);
        $default->expects($this->once())
            ->method('__invoke')
            ->willReturn(null);

        $locator = new ErrorDocumentLocator($default, []);
        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessage('The default error document locator failed to proved a document');

        $locator->byCode(123);
    }

    public function testTheDefaultDocumentIsReturnedWhenThereIsNoMatchForASpecificErrorCode() : void
    {
        $doc = $this->createMock(Document::class);
        $default = $this->createMock(SingleDocumentLocator::class);
        $default->expects($this->once())
            ->method('__invoke')
            ->willReturn($doc);
        $locator = new ErrorDocumentLocator($default, []);
        $this->assertSame($doc, $locator->byCode(999));
    }

    public function testThatASpecificDocumentIsReturnedWhenItMatchesTheGivenErrorCode() : void
    {
        $doc = $this->createMock(Document::class);
        $default = $this->createMock(SingleDocumentLocator::class);
        $default->expects($this->never())->method('__invoke');
        $specific = $this->createMock(SingleDocumentLocator::class);
        $specific->expects($this->once())
            ->method('__invoke')
            ->willReturn($doc);

        $locator = new ErrorDocumentLocator($default, [123 => $specific]);
        $this->assertSame($doc, $locator->byCode(123));
    }

    public function testThatTheDefaultDocumentIsUsedWhenAMatchingSpecificLocatorFailsToYieldADocument() : void
    {
        $doc = $this->createMock(Document::class);
        $default = $this->createMock(SingleDocumentLocator::class);
        $default->expects($this->once())
            ->method('__invoke')
            ->willReturn($doc);
        $specific = $this->createMock(SingleDocumentLocator::class);
        $specific->expects($this->once())
            ->method('__invoke')
            ->willReturn(null);

        $locator = new ErrorDocumentLocator($default, [123 => $specific]);
        $this->assertSame($doc, $locator->byCode(123));
    }
}
