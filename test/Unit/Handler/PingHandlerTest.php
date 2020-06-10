<?php
declare(strict_types=1);

namespace AppTest\Unit\Handler;

use App\Handler\PingHandler;
use AppTest\Unit\Framework\TestCase;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class PingHandlerTest extends TestCase
{
    public function testResponse() : void
    {
        $pingHandler = new PingHandler();
        $response = $pingHandler->handle(
            $this->createMock(ServerRequestInterface::class)
        );

        $json = json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertTrue(isset($json->ack));
    }
}
