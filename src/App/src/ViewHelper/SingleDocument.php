<?php

declare(strict_types=1);

namespace App\ViewHelper;

use App\Content\SingleDocumentLocator;
use Prismic\Document;

class SingleDocument
{
    /** @var SingleDocumentLocator */
    private $locator;

    public function __construct(SingleDocumentLocator $locator)
    {
        $this->locator = $locator;
    }

    public function __invoke(): Document
    {
        return ($this->locator)();
    }
}
