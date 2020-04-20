<?php

namespace C201\Ddd\Tests\Events\Infrastructure\Store\Doctrine;

use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventAggregateType;
use PHPUnit\Framework\TestCase;

class DoctrineStoredEventAggregateTypeTest extends TestCase
{
    public function testGettersReturnArgumentsPassedToConstructor(): void
    {
        $id = uniqid();
        $name = uniqid();
        $aggregateType = new DoctrineStoredEventAggregateType($id, $name);
        $this->assertSame($id, $aggregateType->id());
        $this->assertSame($name, $aggregateType->name());
    }
}
