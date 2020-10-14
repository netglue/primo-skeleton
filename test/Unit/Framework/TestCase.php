<?php

declare(strict_types=1);

namespace AppTest\Unit\Framework;

use Helmich\Psr7Assert\Psr7Assertions;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\ServerRequestInterface;

class TestCase extends PHPUnitTestCase
{
    use Psr7Assertions;

    protected function serverRequest(string $path, string $method = 'GET'): ServerRequestInterface
    {
        return new ServerRequest([], [], $path, $method);
    }
}
