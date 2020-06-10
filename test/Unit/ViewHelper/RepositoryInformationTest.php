<?php
declare(strict_types=1);

namespace AppTest\Unit\ViewHelper;

use App\ViewHelper\RepositoryInformation;
use AppTest\Unit\Framework\TestCase;
use Laminas\Diactoros\Uri;

class RepositoryInformationTest extends TestCase
{
    /** @return mixed[] */
    public function urlProvider() : iterable
    {
        return [
            'https://example.prismic.io/api/v2' => [
                'https://example.prismic.io/api/v2',
                'example.prismic.io',
            ],
            'https://example.cdn.prismic.io/api/v2' => [
                'https://example.cdn.prismic.io/api/v2',
                'example.prismic.io',
            ],
        ];
    }

    /** @dataProvider urlProvider */
    public function testHelper(string $input, string $expectedHost) : void
    {
        $helper = new RepositoryInformation(new Uri($input));
        $this->assertSame($input, $helper()->url());
        $this->assertSame($expectedHost, $helper()->host());
    }
}
