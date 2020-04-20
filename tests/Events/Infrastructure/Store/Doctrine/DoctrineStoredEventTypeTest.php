<?php

namespace C201\Ddd\Tests\Events\Infrastructure\Store\Doctrine;

use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventType;
use PHPUnit\Framework\TestCase;

class DoctrineStoredEventTypeTest extends TestCase
{
    public function testGettersReturnArgumentsPassedToConstructor(): void
    {
        $id = uniqid();
        $name = uniqid();
        $eventType = new DoctrineStoredEventType($id, $name);
        $this->assertSame($id, $eventType->id());
        $this->assertSame($name, $eventType->name());
    }
}
