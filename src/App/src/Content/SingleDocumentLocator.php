<?php
declare(strict_types=1);

namespace App\Content;

use Closure;
use Prismic\ApiClient;
use Prismic\Document;
use Prismic\Predicate;

class SingleDocumentLocator
{
    /** @var ApiClient */
    private $apiClient;

    /** @var Closure */
    private $finder;

    private function __construct(ApiClient $apiClient, Closure $finder)
    {
        $this->apiClient = $apiClient;
        $this->finder = $finder;
    }

    public function __invoke() :? Document
    {
        return ($this->finder)($this->apiClient);
    }

    public static function withPredicates(ApiClient $client, Predicate ...$predicates) : self
    {
        return new static(
            $client,
            static function (ApiClient $client) use ($predicates) :? Document {
                return $client->queryFirst(
                    $client->createQuery()->query(...$predicates)
                );
            }
        );
    }

    public static function withBookmarkName(ApiClient $client, string $bookmark) : self
    {
        return new static(
            $client,
            static function (ApiClient $client) use ($bookmark) :? Document {
                return $client->findByBookmark($bookmark);
            }
        );
    }

    public static function withUid(ApiClient $client, string $type, string $uid) : self
    {
        return new static(
            $client,
            static function (ApiClient $client) use ($type, $uid) :? Document {
                return $client->findByUid($type, $uid);
            }
        );
    }

    public static function withType(ApiClient $client, string $type) : self
    {
        return new static(
            $client,
            static function (ApiClient $client) use ($type) :? Document {
                return $client->queryFirst(
                    $client->createQuery()->query(
                        Predicate::at('document.type', $type)
                    )
                );
            }
        );
    }
}
