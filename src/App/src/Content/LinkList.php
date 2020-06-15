<?php
declare(strict_types=1);

namespace App\Content;

use IteratorAggregate;
use Primo\Content\Document;
use Prismic\Document\Fragment\Collection;
use Prismic\Document\FragmentCollection;

class LinkList extends Document implements IteratorAggregate
{
    /** @return FragmentCollection[] */
    public function getIterator() : iterable
    {
        return $this->getList();
    }

    /** @return FragmentCollection[] */
    public function getList() : FragmentCollection
    {
        $links = $this->get('links');
        if (! $links instanceof FragmentCollection) {
            return Collection::new([]);
        }

        return $links->filter(static function (FragmentCollection $item) : bool {
            return (! $item->get('anchor')->isEmpty()) && (! $item->get('url')->isEmpty());
        });
    }

    public function isEmpty() : bool
    {
        return $this->getList()->isEmpty();
    }
}
