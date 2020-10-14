<?php

declare(strict_types=1);

namespace App;

use Prismic\ApiClient;
use Prismic\Predicate;
use Prismic\ResultSet;

class SearchService
{
    /** @var ApiClient */
    private $apiClient;
    /** @var Predicate[] */
    private $defaultPredicates;

    public function __construct(ApiClient $client, Predicate ...$defaultPredicates)
    {
        $this->apiClient = $client;
        $this->defaultPredicates = $defaultPredicates;
    }

    /**
     * Perform a FullText Search
     */
    public function query(string $terms, string $language = '*', int $page = 1, int $perPage = 10): ResultSet
    {
        $predicates = $this->defaultPredicates;
        $predicates[] = Predicate::fulltext('document', $terms);

        $query = $this->apiClient->createQuery()
            ->resultsPerPage($perPage)
            ->page($page)
            ->lang($language)
            ->query(...$predicates);

        return $this->apiClient->query($query);
    }
}
