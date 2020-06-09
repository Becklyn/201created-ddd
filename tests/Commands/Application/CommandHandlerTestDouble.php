<?php

namespace C201\Ddd\Tests\Commands\Application;

use C201\Ddd\Commands\Application\CommandHandler;
use C201\Ddd\Events\Domain\EventProvider;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-06-27
 */
class CommandHandlerTestDouble extends CommandHandler
{
    /**
     * @var CommandHandlerTestTryExecutor
     */
    private $tryExecutor;

    /**
     * @var CommandHandlerTestCatchExecutor
     */
    private $catchExecutor;

    public function __construct(CommandHandlerTestTryExecutor $tryExecutor, CommandHandlerTestCatchExecutor $catchExecutor)
    {
        $this->tryExecutor = $tryExecutor;
        $this->catchExecutor = $catchExecutor;
    }

    public function handle(CommandHandlerTestCommand $command): void
    {
        $this->handleCommand($command);
    }

    /**
     * @param CommandHandlerTestCommand $command
     */
    protected function execute($command): ?EventProvider
    {
        return $this->tryExecutor->execute($command->getArgument());
    }

    protected function postRollback(\Throwable $e, $command): \Throwable
    {
        return $this->catchExecutor->execute($e, $command);
    }
}
