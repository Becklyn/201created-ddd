<?php

namespace C201\Ddd\Commands\Application;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-07-28
 */
trait CommandBusTestTrait
{
    /** @var ObjectProphecy|CommandBus */
    protected ObjectProphecy $commandbus;

    protected function initCommandBusTestTrait(): void
    {
        $this->commandbus = $this->prophesize(CommandBus::class);
    }

    protected function thenCommandBusShouldDispatch($command): void
    {
        $this->commandbus->dispatch($command)->shouldBeCalled();
    }

    protected function thenCommandBusShouldNotDispatch($command): void
    {
        $this->commandbus->dispatch($command)->shouldNotBeCalled();
    }

    protected function thenCommandBusShouldNotDispatchAnyCommands(): void
    {
        $this->thenCommandBusShouldNotDispatch(Argument::any());
    }
}
