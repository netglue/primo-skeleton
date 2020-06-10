<?php
declare(strict_types=1);

namespace App\ViewHelper;

use App\Exception\InvalidArgument;
use Prismic\Document;
use Prismic\Link;
use Prismic\LinkResolver as CmsLinkResolver;

use function get_class;
use function gettype;
use function is_object;
use function sprintf;

class LinkResolver implements CmsLinkResolver
{
    /** @var CmsLinkResolver */
    private $resolver;

    public function __construct(CmsLinkResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param Link|Document|null $arg
     *
     * @return CmsLinkResolver|string|null
     */
    public function __invoke($arg = null)
    {
        if (! $arg) {
            return $this->resolver;
        }

        $link = $arg instanceof Document ? $arg->asLink() : null;
        $link = $arg instanceof Link ? $arg : $link;

        if ($link) {
            return $this->resolve($link);
        }

        throw new InvalidArgument(sprintf(
            '%s expects either a %s instance or a %s instance, or, no arguments at all. Received %s',
            __METHOD__,
            Document::class,
            Link::class,
            is_object($arg) ? get_class($arg) : gettype($arg)
        ));
    }

    public function resolve(Link $link) :? string
    {
        return $this->resolver->resolve($link);
    }
}
