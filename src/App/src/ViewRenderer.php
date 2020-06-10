<?php
declare(strict_types=1);

namespace App;

use App\ViewHelper\LinkResolver;
use App\ViewHelper\RepositoryInformation;
use Laminas\View\Renderer\PhpRenderer;
use Prismic\Document;
use Prismic\Link;

/**
 * This class is used purely for type hinting in view scripts to aid IDE auto completion
 *
 * @method LinkResolver linkResolver(Document|Link $link = null)
 * @method RepositoryInformation repositoryInformation()
 * @method string sliceZoneRenderer(Document $document, string $sliceZoneFragmentName)
 */
final class ViewRenderer extends PhpRenderer
{
}
