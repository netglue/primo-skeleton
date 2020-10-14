<?php

declare(strict_types=1);

namespace AppTest\Unit\Content\Container;

use App\Content\Container\SingleDocumentLocatorAbstractFactory;
use App\Exception\ConfigurationError;
use AppTest\Unit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Prismic\ApiClient;
use Prismic\Predicate;
use Psr\Container\ContainerInterface;

class SingleDocumentLocatorAbstractFactoryTest extends TestCase
{
    /** @var MockObject|ContainerInterface */
    private $container;
    /** @var SingleDocumentLocatorAbstractFactory */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory = new SingleDocumentLocatorAbstractFactory();
    }

    /** @param mixed[] $config */
    private function containerHasConfig(array $config): void
    {
        $client = $this->createMock(ApiClient::class);
        $client->expects(self::never())->method(self::anything());
        $this->container->expects(self::atLeast(2))
            ->method('get')
            ->willReturnMap([
                [ApiClient::class, $client],
                ['config', $config],
            ]);
    }

    public function testThatAnUnknownDocumentStringWillCauseAnError(): void
    {
        $this->containerHasConfig([]);
        $this->expectException(ConfigurationError::class);
        $this->expectExceptionMessage('This factory has been called with the id whatever but no configuration can be found for this identifier');
        ($this->factory)($this->container, 'whatever');
    }

    /** @return mixed[] */
    public function validConfigurationsProvider(): iterable
    {
        return [
            'Bookmark' => [
                ['bookmark' => 'whatever'],
            ],
            'Uid' => [
                ['uid' => 'some-id', 'type' => 'some-type'],
            ],
            'Type' => [
                ['type' => 'some-type'],
            ],
            'Predicates' => [
                [
                    'predicates' => [
                        Predicate::at('document.type', 'error'),
                        Predicate::at('my.error.errorCode', 404),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param mixed[] $config
     *
     * @dataProvider validConfigurationsProvider
     */
    public function testGivenValidConfigurationALocatorWillBeReturned(array $config): void
    {
        $this->containerHasConfig([
            'primo' => [
                'documents' => ['mine' => $config],
            ],
        ]);

        ($this->factory)($this->container, 'mine');
        $this->addToAssertionCount(1);
    }

    public function testBadConfigurationWillCauseAnError(): void
    {
        $this->containerHasConfig([
            'primo' => [
                'documents' => ['mine' => ['something-is-wrong']],
            ],
        ]);

        $this->expectException(ConfigurationError::class);
        $this->expectExceptionMessage('The single document locator for the document with id "mine" cannot be resolved because');

        ($this->factory)($this->container, 'mine');
    }
}
