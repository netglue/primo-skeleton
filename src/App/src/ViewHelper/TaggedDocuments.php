<?php

declare(strict_types=1);

namespace App\ViewHelper;

use Prismic\ApiClient;
use Prismic\Document;
use Prismic\Predicate;

/**
 * Finds all documents with a specific tag
 *
 * This view helper is stupid. It doesn't limit results to a specific document type, nor does it provide any kind of
 * sorting or order. It would be a big problem if this query returned 1000 documents.
 *
 * It's only here for simplicity's sake and you should write something more specific to your use-case.
 */
class TaggedDocuments
{
    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $client)
    {
        $this->apiClient = $client;
    }

    /** @return Document[] */
    public function __invoke(string $tag): iterable
    {
        $query = $this->apiClient->createQuery()
            ->query(Predicate::hasTag($tag));

        return $this->apiClient->findAll($query)->results();
    }
}
