<?php

namespace C201\Ddd\Commands\Infrastructure\SimpleBus;

use C201\Ddd\Commands\Application\CommandBus as CommandBusInterface;
use SimpleBus\SymfonyBridge\Bus\CommandBus;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-06-05
 */
class SimpleBusCommandBus implements CommandBusInterface
{
    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function dispatch($command): void
    {
        $this->commandBus->handle($command);
    }
}
