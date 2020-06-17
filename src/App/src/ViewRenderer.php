<?php
declare(strict_types=1);

namespace App;

use App\ViewHelper\LinkList;
use App\ViewHelper\LinkResolver;
use App\ViewHelper\RepositoryInformation;
use Laminas\View\Renderer\PhpRenderer;
use Prismic\Document;
use Prismic\Link;
use Prismic\ResultSet;

/**
 * This class is used purely for type hinting in view scripts to aid IDE auto completion
 *
 * @method LinkList linkList()
 * @method LinkResolver linkResolver(Document|Link $link = null)
 * @method ResultSet relatedDocuments(Document $document, int $limit = 20, iterable $matchingTags = [], iterable $matchingTypes = [], int $relevanceThreshold = 10)
 * @method RepositoryInformation repositoryInformation()
 * @method Document|null resolvedDocument()
 * @method string sliceZoneRenderer(Document $document, string $sliceZoneFragmentName)
 * @method Document[] taggedDocuments(string $tag)
 */
final class ViewRenderer extends PhpRenderer
{
}
