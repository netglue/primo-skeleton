<?php
declare(strict_types=1);

namespace App\Content;

use Primo\Content\Document;
use Prismic\Document\Fragment\StringFragment;
use Prismic\Document\FragmentCollection;

/**
 * Defines a document intended to be presented as a Web Page
 *
 * This class is tightly coupled to *your* definition of `page` type of document in the Prismic API.
 *
 * It is not the canonical way of describing a document/web page and serves as an example or starting point for your
 * own content model.
 */
class WebPage extends Document implements HasWebPageMetaData
{
    public function metaTitle() : string
    {
        $title = $this->nonEmptyStringFragmentOrNull('meta-title');

        return $title ?: 'Untitled Document';
    }

    public function metaDescription() :? string
    {
        return $this->nonEmptyStringFragmentOrNull('meta-description');
    }

    public function robotsMeta() :? string
    {
        return $this->nonEmptyStringFragmentOrNull('robots');
    }

    private function nonEmptyStringFragmentOrNull(string $fragmentName, ?FragmentCollection $collection = null) :? string
    {
        $collection = $collection ?? $this->data()->content();
        $fragment = $collection->get($fragmentName);
        if ($fragment instanceof StringFragment && ! $fragment->isEmpty()) {
            return (string) $fragment;
        }

        return null;
    }
}
