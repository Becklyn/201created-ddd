<?php

namespace C201\Ddd\Tests\Commands\Infrastructure;

use C201\Ddd\Commands\Infrastructure\SimpleBus\SimpleBusCommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SimpleBus\SymfonyBridge\Bus\CommandBus;

class SimpleBusCommandBusTest extends TestCase
{
    use ProphecyTrait;

    public function testDispatchCallsHandleOnBase()
    {
        $command = new \stdClass();
        /** @var ObjectProphecy|CommandBus $base */
        $base = $this->prophesize(CommandBus::class);
        $base->handle($command)->shouldBeCalledTimes(1);
        $fixture = new SimpleBusCommandBus($base->reveal());
        $fixture->dispatch($command);
    }
}
