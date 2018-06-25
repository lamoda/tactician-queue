<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Tests\Middleware;

use Lamoda\QueueBundle\Factory\PublisherFactory;
use Lamoda\TacticianQueue\Middleware\QueuePublishMiddleware;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

final class QueuePublishMiddlewareTestMiddlewareTest extends TestCase
{
    /**
     * @var PublisherFactory | PHPUnit_Framework_MockObject_MockObject
     */
    private $publisherFactory;

    /**
     * @var QueuePublishMiddleware
     */
    private $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publisherFactory = $this->createMock(PublisherFactory::class);

        $this->middleware = new QueuePublishMiddleware(
            $this->publisherFactory
        );
    }

    public function testHandling()
    {
        $nextCalled = false;
        $command = new \stdClass();
        $next = function ($passedCommand) use (&$nextCalled, $command) {
            $nextCalled = true;
            $this->assertEquals($command, $passedCommand);
        };

        $this->publisherFactory->expects($this->once())->method('releaseAll');

        $this->middleware->execute($command, $next);

        $this->assertEquals(true, $nextCalled);
    }
}
