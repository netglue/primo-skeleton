<?php

declare(strict_types=1);

namespace App\ViewHelper;

use Prismic\ApiClient;
use Prismic\Document;
use Prismic\Predicate;
use Prismic\ResultSet;
use Webmozart\Assert\Assert;

class RelatedDocuments
{
    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $client)
    {
        $this->apiClient = $client;
    }

    /**
     * @param string[] $matchingTags
     * @param string[] $matchingTypes
     *
     * @return Document[]
     */
    public function __invoke(
        Document $relatedTo,
        int $limit = 20,
        iterable $matchingTags = [],
        iterable $matchingTypes = [],
        int $relevanceThreshold = 10
    ): ResultSet {
        $predicates = [Predicate::similar($relatedTo->id(), $relevanceThreshold)];
        if ($matchingTags !== []) {
            Assert::allString($matchingTags, 'The matchingTags argument can only contain strings');
            $predicates[] = Predicate::any('document.tags', $matchingTags);
        }

        if ($matchingTypes !== []) {
            Assert::allString($matchingTypes, 'The matchingTypes argument can only contain strings');
            $predicates[] = Predicate::any('document.type', $matchingTypes);
        }

        $query = $this->apiClient->createQuery()
            ->query(...$predicates)
            ->resultsPerPage($limit);

        return $this->apiClient->query($query);
    }
}
