<?php
declare(strict_types=1);

namespace App\ViewHelper\Container;

use App\Container\StaticFactoryContainerAssertion;
use App\Content\SingleDocumentLocator;
use App\Exception\ConfigurationError;
use App\ViewHelper\SingleDocument;
use Psr\Container\ContainerInterface;

use function sprintf;

class SingleDocumentStaticFactory
{
    use StaticFactoryContainerAssertion;

    /** @var string */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __invoke(ContainerInterface $container) : SingleDocument
    {
        $locator = $container->get($this->id);
        if (! $locator instanceof SingleDocumentLocator) {
            throw new ConfigurationError(sprintf(
                'The service identifier %s does not resolve to a %s',
                $this->id,
                SingleDocumentLocator::class
            ));
        }

        return new SingleDocument($locator);
    }

    /** @param mixed[] $args */
    public static function __callStatic(string $id, array $args) : SingleDocument
    {
        $container = self::assertContainer(__METHOD__, $args);

        return (new static($id))($container);
    }
}
