<?php

namespace C201\Ddd\Tests\Events\Infrastructure\Store\Doctrine;

use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventAggregate;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventAggregateType;
use PHPUnit\Framework\TestCase;

class DoctrineStoredEventAggregateTest extends TestCase
{
    public function testGettersReturnArgumentsPassedToConstructor(): void
    {
        $id = uniqid();
        $type = new DoctrineStoredEventAggregateType(uniqid(), uniqid());
        $version = random_int(1, 1000);
        $aggregate = new DoctrineStoredEventAggregate($id, $type, $version);
        $this->assertEquals($id, $aggregate->id());
        $this->assertSame($type, $aggregate->aggregateType());
        $this->assertEquals($version, $aggregate->version());
    }

    public function testIncrementVersion(): void
    {
        $startingVersion = random_int(1, 1000);
        $aggregate = new DoctrineStoredEventAggregate(uniqid(), new DoctrineStoredEventAggregateType(uniqid(), uniqid()), $startingVersion);
        $this->assertEquals($startingVersion, $aggregate->version());
        $aggregate->incrementVersion();
        $this->assertEquals($startingVersion + 1, $aggregate->version());
    }
}
