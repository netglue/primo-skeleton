<?php
declare(strict_types=1);

namespace AppTest\Unit\Framework;

use Helmich\Psr7Assert\Psr7Assertions;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    use Psr7Assertions;
}
