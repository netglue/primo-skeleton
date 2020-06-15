<?php
declare(strict_types=1);

namespace App\ViewHelper;

use App\Content\LinkList as Links;
use Laminas\Escaper\Escaper;
use Prismic\LinkResolver;

use function array_map;
use function count;
use function implode;
use function sprintf;

class LinkList
{
    /** @var LinkResolver */
    private $resolver;
    /** @var Escaper */
    private $escaper;

    public function __construct(Escaper $escaper, LinkResolver $resolver)
    {
        $this->escaper = $escaper;
        $this->resolver = $resolver;
    }

    public function __invoke() : self
    {
        return $this;
    }

    public function renderToMarkup(Links $links) : string
    {
        $list = [];
        foreach ($links as $item) {
            $url = $this->resolver->resolve($item->get('url'));
            $anchor = $this->escaper->escapeHtml((string) $item->get('anchor'));
            if (! $url) {
                continue;
            }

            $list[] = ['url' => $url, 'anchor' => $anchor];
        }

        if (! count($list)) {
            return '';
        }

        return sprintf(
            '<ul>%s</ul>',
            implode('', array_map(static function (array $item) : string {
                return sprintf('<li><a href="%s">%s</a></li>', $item['url'], $item['anchor']);
            }, $list))
        );
    }
}
