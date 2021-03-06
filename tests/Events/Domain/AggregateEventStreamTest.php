<?php

namespace C201\Ddd\Tests\Events\Domain;

use C201\Ddd\Events\Domain\AggregateEventStream;
use C201\Ddd\Tests\Identity\Domain\AbstractAggregateTestProxyId;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-06
 */
class AggregateEventStreamTest extends TestCase
{
    public function testAggregateIdReturnsAggregateIdPassedToConstructor(): void
    {
        $id = AbstractAggregateTestProxyId::next();
        $stream = new AggregateEventStream($id, uniqid(), Collection::make());
        $this->assertSame($id, $stream->aggregateId());
    }

    public function testAggregateTypeReturnsAggregateTypePassedToConstructor(): void
    {
        $type = uniqid();
        $stream = new AggregateEventStream(AbstractAggregateTestProxyId::next(), $type, Collection::make());
        $this->assertEquals($type, $stream->aggregateType());
    }

    public function testEventseReturnsEventsCollectionPassedToConstructor(): void
    {
        $collection = Collection::make();
        $stream = new AggregateEventStream(AbstractAggregateTestProxyId::next(), uniqid(), $collection);
        $this->assertSame($collection, $stream->events());
    }
}
