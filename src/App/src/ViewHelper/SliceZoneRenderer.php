<?php
declare(strict_types=1);

namespace App\ViewHelper;

use Laminas\View\Helper\Partial;
use Prismic\Document;
use Prismic\Document\Fragment\Slice;
use Prismic\Document\FragmentCollection;

class SliceZoneRenderer
{
    /** @var Partial */
    private $partial;
    /** @var string[] */
    private $templateMap;

    /** @param string[] $templateMap */
    public function __construct(Partial $partial, array $templateMap)
    {
        $this->partial = $partial;
        $this->templateMap = $templateMap;
    }

    public function __invoke(Document $document, string $sliceFragmentName) : string
    {
        $data = $document->data()->content();

        $slices = $data->get($sliceFragmentName);
        if (! $slices instanceof FragmentCollection || $slices->isEmpty()) {
            return '';
        }

        $buffer = '';

        foreach ($slices as $slice) {
            if (! $slice instanceof Slice) {
                continue;
            }

            $buffer .= $this->renderSlice($slice);
        }

        return $buffer;
    }

    private function renderSlice(Slice $slice) : string
    {
        $template = $this->templateMap[$slice->type()] ?? null;
        if (! $template) {
            return '';
        }

        return (string) ($this->partial)($template, ['slice' => $slice]);
    }
}
