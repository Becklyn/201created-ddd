<?php

namespace C201\Ddd\Tests\Commands\Application;

use C201\Ddd\Events\Domain\EventProvider;
use C201\Ddd\Events\Domain\EventRegistry;
use Ramsey\Uuid\Uuid;
use C201\Ddd\Transactions\Application\TransactionManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class CommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy|CommandHandlerTestTryExecutor
     */
    private $tryExecutor;

    /**
     * @var ObjectProphecy|CommandHandlerTestCatchExecutor
     */
    private $catchExecutor;

    /**
     * @var ObjectProphecy|TransactionManager
     */
    private $transactionManager;

    /**
     * @var ObjectProphecy|EventRegistry
     */
    private $eventRegistry;

    /**
     * @var CommandHandlerTestDouble
     */
    private $fixture;

    protected function setUp(): void
    {
        $this->tryExecutor = $this->prophesize(CommandHandlerTestTryExecutor::class);
        $this->catchExecutor = $this->prophesize(CommandHandlerTestCatchExecutor::class);
        $this->transactionManager = $this->prophesize(TransactionManager::class);
        $this->eventRegistry = $this->prophesize(EventRegistry::class);
        $this->fixture = new CommandHandlerTestDouble($this->tryExecutor->reveal(), $this->catchExecutor->reveal());
        $this->fixture->setTransactionManager($this->transactionManager->reveal());
        $this->fixture->setEventRegistry($this->eventRegistry->reveal());
    }

    public function testTryIsExecutedForArgument()
    {
        $argument = $this->givenAnArgument();

        $this->whenCommandWithArgumentIsHandled($argument);

        $this->thenTryIsExecutedForArgument($argument);
    }

    private function givenAnArgument()
    {
        return Uuid::uuid4();
    }

    private function whenCommandWithArgumentIsHandled($argument)
    {
        $this->fixture->handle(new CommandHandlerTestCommand($argument));
    }

    private function thenTryIsExecutedForArgument($argument)
    {
        $this->tryExecutor->execute($argument)->shouldHaveBeenCalledtimes(1);
    }

    public function testEventsAreDequeuedAndRegisteredIfTryReturnsEventProvider()
    {
        $argument = $this->givenAnArgument();
        $eventProvider = $this->givenTryReturnsEventProvider($argument);

        $this->whenCommandWithArgumentIsHandled($argument);

        $this->thenEventsAreDequeuedAndRegistered($eventProvider);
    }

    private function givenTryReturnsEventProvider($argument)
    {
        $eventProvider = $this->prophesize(EventProvider::class)->reveal();
        $this->tryExecutor->execute($argument)->willReturn($eventProvider);
        return $eventProvider;
    }

    private function thenEventsAreDequeuedAndRegistered($eventProvider)
    {
        $this->eventRegistry->dequeueProviderAndRegister($eventProvider)->shouldHaveBeenCalledTimes(1);
    }

    public function testEventsAreNotDequeuedAndRegisteredIfTryReturnsNull()
    {
        $argument = $this->givenAnArgument();
        $this->givenTryReturnsNull($argument);

        $this->whenCommandWithArgumentIsHandled($argument);

        $this->thenEventsAreNotDequeuedAndRegistered();
    }

    private function givenTryReturnsNull($argument)
    {
        $this->tryExecutor->execute($argument)->willReturn(null);
    }

    private function thenEventsAreNotDequeuedAndRegistered()
    {
        $this->eventRegistry->dequeueProviderAndRegister(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function testTransactionIsCommitted()
    {
        $argument = $this->givenAnArgument();

        $this->whenCommandWithArgumentIsHandled($argument);

        $this->thenTransactionIsCommitted();
    }

    private function thenTransactionIsCommitted()
    {
        $this->transactionManager->commit()->shouldHaveBeenCalledTimes(1);
    }

    public function testCallingCodeExpectsExceptionIfTryThrowsException()
    {
        $argument = $this->givenAnArgument();
        $exception = $this->givenTryThrowsException($argument);
        $this->givenCatchSimplyPassesExceptionThrough($exception);

        $this->thenCallingCodeExpectsException($exception);

        $this->whenCommandWithArgumentIsHandled($argument);
    }

    private function givenTryThrowsException($argument)
    {
        $exception = new \Exception('An Exception');
        $this->tryExecutor->execute($argument)->willThrow($exception);
        return $exception;
    }

    private function givenCatchSimplyPassesExceptionThrough($exception)
    {
        $this->catchExecutor->execute($exception, Argument::any())->willReturn($exception);
    }

    private function thenCallingCodeExpectsException($exception)
    {
        $this->expectExceptionObject($exception);
    }

    public function testNoEventsAreDequeuedAndRegisteredIfTryThrowsException()
    {
        $argument = $this->givenAnArgument();
        $exception = $this->givenTryThrowsException($argument);
        $this->givenCatchSimplyPassesExceptionThrough($exception);

        try {
            $this->whenCommandWithArgumentIsHandled($argument);
        } catch (\Exception $e) {
        }

        $this->thenEventsAreNotDequeuedAndRegistered();
    }

    public function testTransactionIsRolledBackIfTryThrowsException()
    {
        $argument = $this->givenAnArgument();
        $exception = $this->givenTryThrowsException($argument);
        $this->givenCatchSimplyPassesExceptionThrough($exception);

        try {
            $this->whenCommandWithArgumentIsHandled($argument);
        } catch (\Exception $e) {
        }

        $this->thenTransactionIsRolledBack();
    }

    private function thenTransactionIsRolledBack()
    {
        $this->transactionManager->rollback()->shouldHaveBeenCalledTimes(1);
    }

    public function testCatchIsExecutedForExceptionIfTryThrowsException()
    {
        $argument = $this->givenAnArgument();
        $exception = $this->givenTryThrowsException($argument);
        $this->givenCatchSimplyPassesExceptionThrough($exception);

        try {
            $this->whenCommandWithArgumentIsHandled($argument);
        } catch (\Exception $e) {
        }

        $this->thenCatchIsExecutedForException($exception);
    }

    private function thenCatchIsExecutedForException($exception)
    {
        $this->catchExecutor->execute($exception, Argument::any())->shouldHaveBeenCalledTimes(1);
    }

    public function testCallingCodeExpectsNewExceptionIfCatchThrowsNewException()
    {
        $argument = $this->givenAnArgument();
        $exception = $this->givenTryThrowsException($argument);
        $newException = $this->givenCatchThrowsNewException($exception);
        $this->assertNotSame($exception, $newException);

        $this->thenCallingCodeExpectsException($newException);

        $this->whenCommandWithArgumentIsHandled($argument);
    }

    private function givenCatchThrowsNewException($exception)
    {
        $newException = new \Exception('Neeeew exception');
        $this->catchExecutor->execute($exception, Argument::any())->willThrow($newException);
        return $newException;
    }

    public function testCallingCodeExpectsNewExceptionIfCatchReturnsNewException()
    {
        $argument = $this->givenAnArgument();
        $exception = $this->givenTryThrowsException($argument);
        $newException = $this->givenCatchReturnsNewException($exception);
        $this->assertNotSame($exception, $newException);

        $this->thenCallingCodeExpectsException($newException);

        $this->whenCommandWithArgumentIsHandled($argument);
    }

    private function givenCatchReturnsNewException($exception)
    {
        $newException = new \Exception('Neeeew exception');
        $this->catchExecutor->execute($exception, Argument::any())->willReturn($newException);
        return $newException;
    }
}
