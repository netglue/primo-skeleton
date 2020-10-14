<?php

declare(strict_types=1);

namespace App\Content;

interface HasWebPageMetaData
{
    /**
     * Return a string to be used for the HTML title tag
     */
    public function metaTitle(): string;

    /**
     * Return a string or null for the meta description
     */
    public function metaDescription(): ?string;

    /**
     * Directives for search engine robots for the document/page
     */
    public function robotsMeta(): ?string;
}
