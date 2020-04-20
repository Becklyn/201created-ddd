<?php

namespace C201\Ddd\Tests\Events\Infrastructure\Bus\SimpleBus;

use C201\Ddd\Events\Domain\DomainEventTestTrait;
use C201\Ddd\Events\Infrastructure\Bus\SimpleBus\SimpleBusEventBus;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use SimpleBus\SymfonyBridge\Bus\EventBus;
use C201\Ddd\Tests\Events\Infrastructure\Store\Doctrine\DoctrineEventStoreTestEvent;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineEventStoreTestTrait;

class SimpleBusEventBusTest extends TestCase
{
    use ProphecyTrait;
    use DomainEventTestTrait;
    use DoctrineEventStoreTestTrait;

    public function testDispatchPassesEventToSimpleBus(): void
    {
        $simpleBus = $this->prophesize(EventBus::class);
        $event = new DoctrineEventStoreTestEvent($this->givenAnEventId(), $this->givenARaisedTs(), $this->givenAnAggregateId());
        $simpleBus->handle($event)->shouldBeCalledTimes(1);
        $bus = new SimpleBusEventBus($simpleBus->reveal());
        $bus->dispatch($event);
    }
}
