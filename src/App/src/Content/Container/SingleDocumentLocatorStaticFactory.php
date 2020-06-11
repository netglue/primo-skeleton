<?php
declare(strict_types=1);

namespace App\Content\Container;

use App\Container\StaticFactoryContainerAssertion;
use App\Content\SingleDocumentLocator;
use App\Exception\ConfigurationError;
use Prismic\ApiClient;
use Psr\Container\ContainerInterface;

use function is_array;
use function sprintf;

class SingleDocumentLocatorStaticFactory
{
    use StaticFactoryContainerAssertion;

    /** @var string */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __invoke(ContainerInterface $container) : SingleDocumentLocator
    {
        $client = $container->get(ApiClient::class);
        $config = $container->get('config')['primo']['documents'];

        $options = $config[$this->id] ?? null;
        if (! is_array($options)) {
            throw ConfigurationError::withMessage(sprintf(
                'This factory has been called with the id %1$s but no configuration can be found for this identifier ' .
                'in config.primo.documents.%1$s',
                $this->id
            ));
        }

        $bookmark = $options['bookmark'] ?? null;
        if ($bookmark) {
            return SingleDocumentLocator::withBookmarkName($client, $bookmark);
        }

        $uid = $options['uid'] ?? null;
        $type = $options['type'] ?? null;
        if ($uid && $type) {
            return SingleDocumentLocator::withUid($client, $type, $uid);
        }

        if ($type) {
            return SingleDocumentLocator::withType($client, $type);
        }

        $predicates = $options['predicates'] ?? null;
        if (is_array($predicates) && $predicates !== []) {
            return SingleDocumentLocator::withPredicates($client, ...$predicates);
        }

        throw ConfigurationError::withMessage(sprintf(
            'The single document locator for the document with id "%s" cannot be resolved because there is ' .
            'insufficient configuration.',
            $this->id
        ));
    }

    /** @param mixed[] $args */
    public static function __callStatic(string $id, array $args) : SingleDocumentLocator
    {
        $container = self::assertContainer(__METHOD__, $args);

        return (new static($id))($container);
    }
}
