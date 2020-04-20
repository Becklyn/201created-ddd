<?php

namespace C201\Ddd\Events\Domain;

use C201\Ddd\Events\Application\EventBus;
use Ramsey\Uuid\Uuid;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-07-23
 */
trait DomainEventTestTrait
{
    /**
     * @var EventRegistry|ObjectProphecy
     */
    protected $eventRegistry;

    /**
     * @var ObjectProphecy|EventBus
     */
    protected $eventBus;

    protected function initDomainEventTestTrait(): void
    {
        $this->eventRegistry = $this->prophesize(EventRegistry::class);
        $this->eventBus = $this->prophesize(EventBus::class);
    }

    protected function givenAnEventId(): EventId
    {
        return EventId::fromString(Uuid::uuid4());
    }

    protected function givenARaisedTs(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }

    /**
     * @param EventProvider|Argument $eventProvider
     */
    protected function thenEventRegistryShouldDequeueAndRegister($eventProvider): void
    {
        $this->eventRegistry->dequeueProviderAndRegister($eventProvider)->shouldBeCalled();
    }

    protected function thenEventShouldBeDispatched($event): void
    {
        $this->eventBus->dispatch($event)->shouldBeCalled();
    }

    protected function thenEventShouldBeDispatchedTimes($event, int $times): void
    {
        $this->eventBus->dispatch($event)->shouldBeCalledTimes($times);
    }

    protected function thenEventShouldNotBeDispatched($event): void
    {
        $this->eventBus->dispatch($event)->shouldNotBeCalled();
    }

    protected function thenNoEventsShouldBeDispatched(): void
    {
        $this->eventBus->dispatch(Argument::any())->shouldNotBeCalled();
    }

    protected function givenEventRegistryDequeuesAndRegisters(EventProvider $eventProvider): void
    {
        $this->eventRegistry->dequeueProviderAndRegister($eventProvider);
    }

    protected function thenEventRegistryShouldNotDequeueAndRegisterAnything(): void
    {
        $this->eventRegistry->dequeueProviderAndRegister(Argument::any())->shouldNotBeCalled();
    }

    protected function givenEventRegistryThrowsExceptionOnDequeueAndRegister(EventProvider $eventProvider): \Exception
    {
        $exception = new \Exception();
        $this->eventRegistry->dequeueProviderAndRegister($eventProvider)->willThrow($exception);
        return $exception;
    }
}
