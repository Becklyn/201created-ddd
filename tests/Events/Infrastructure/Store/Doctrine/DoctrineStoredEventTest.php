<?php

namespace C201\Ddd\Tests\Events\Infrastructure\Store\Doctrine;

use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEvent;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventAggregate;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventAggregateType;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventType;
use PHPUnit\Framework\TestCase;

class DoctrineStoredEventTest extends TestCase
{
    public function testGettersReturnArgumentsPassedToConstructor(): void
    {
        $eventId = uniqid();
        $aggregate = new DoctrineStoredEventAggregate(uniqid(), new DoctrineStoredEventAggregateType(uniqid(), uniqid()));
        $version = rand(1, 1000);
        $eventType = new DoctrineStoredEventType(uniqid(), uniqid());
        $raisedTs = new \DateTimeImmutable();
        $data = uniqid();
        $storedEvent = new DoctrineStoredEvent($eventId, $aggregate, $version, $eventType, $raisedTs, $data);
        $this->assertSame($eventId, $storedEvent->eventId());
        $this->assertSame($aggregate, $storedEvent->aggregate());
        $this->assertSame($version, $storedEvent->version());
        $this->assertSame($eventType, $storedEvent->eventType());
        $this->assertSame($raisedTs, $storedEvent->raisedTs());
        $this->assertSame($data, $storedEvent->data());
    }
}
