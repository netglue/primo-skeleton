<?php
declare(strict_types=1);

namespace App\ViewHelper;

use Prismic\Document;

class ResolvedDocument
{
    /** @var Document|null */
    private $document;

    public function set(Document $document) : void
    {
        $this->document = $document;
    }

    public function __invoke() :? Document
    {
        return $this->document;
    }
}
