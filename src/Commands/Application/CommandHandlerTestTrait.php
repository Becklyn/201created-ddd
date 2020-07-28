<?php

namespace C201\Ddd\Commands\Application;

use C201\Ddd\Commands\Application\CommandHandler;
use C201\Ddd\Events\Domain\DomainEventTestTrait;
use C201\Ddd\Events\Domain\EventRegistry;
use C201\Ddd\Transactions\Application\TransactionManager;
use C201\Ddd\Transactions\Application\TransactionManagerTestTrait;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-06-27
 */
trait CommandHandlerTestTrait
{
    use TransactionManagerTestTrait;
    use DomainEventTestTrait;

    /**
     * @var CommandHandler
     */
    protected $fixture;

    protected function commandHandlerPostSetUp()
    {
        $this->fixture->setTransactionManager($this->transactionManager->reveal());
        $this->fixture->setEventRegistry($this->eventRegistry->reveal());
    }
}
