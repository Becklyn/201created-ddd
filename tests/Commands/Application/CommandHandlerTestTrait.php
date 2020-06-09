<?php

namespace C201\Ddd\Tests\Commands\Application;

use C201\Ddd\Commands\Application\CommandHandler;
use C201\Ddd\Events\Domain\EventRegistry;
use C201\Ddd\Transactions\Application\TransactionManager;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-06-27
 */
trait CommandHandlerTestTrait
{
    /**
     * @var CommandHandler
     */
    protected $fixture;

    protected function commandHandlerPostSetUp()
    {
        $this->fixture->setTransactionManager($this->prophesize(TransactionManager::class)->reveal());
        $this->fixture->setEventRegistry($this->prophesize(EventRegistry::class)->reveal());
    }
}
