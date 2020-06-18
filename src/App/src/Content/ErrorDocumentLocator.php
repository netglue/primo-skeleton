<?php
declare(strict_types=1);

namespace App\Content;

use App\Exception\InvalidArgument;
use App\Exception\RuntimeError;
use Prismic\Document;

use function is_int;
use function sprintf;

class ErrorDocumentLocator
{
    /** @var SingleDocumentLocator */
    private $defaultError;

    /** @var SingleDocumentLocator[] */
    private $map = [];

    /** @param SingleDocumentLocator[] $statusCodeMap */
    public function __construct(
        SingleDocumentLocator $defaultError,
        array $statusCodeMap
    ) {
        $this->defaultError = $defaultError;
        foreach ($statusCodeMap as $code => $locator) {
            if (! is_int($code)) {
                throw new InvalidArgument('The error document status code mapping must have integer keys');
            }

            if (! $locator instanceof SingleDocumentLocator) {
                throw new InvalidArgument(sprintf(
                    'The error document status code mapping values must all be instances of %s',
                    SingleDocumentLocator::class
                ));
            }

            $this->map[$code] = $locator;
        }
    }

    public function hasCode(int $code) : bool
    {
        return isset($this->map[$code]);
    }

    public function byCode(int $code) : Document
    {
        $locator = $this->map[$code] ?? null;
        $document = $locator ? $locator() : null;
        if (! $document) {
            $document = $this->defaultDocument();
        }

        return $document;
    }

    private function defaultDocument() : Document
    {
        $document = ($this->defaultError)();
        if (! $document) {
            throw new RuntimeError('The default error document locator failed to proved a document');
        }

        return $document;
    }
}
