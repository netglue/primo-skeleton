<?php
declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\PingHandler;
use Laminas\Diactoros\Response\JsonResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

use function json_decode;

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
