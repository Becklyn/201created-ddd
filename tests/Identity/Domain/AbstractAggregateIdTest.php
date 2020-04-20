<?php
namespace C201\Ddd\Tests\Identity\Domain;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-06
 */
class AbstractAggregateIdTest extends TestCase
{
    public function testFromStringReturnsInstanceWithPassedUuidAsId(): void
    {
        $uuid = Uuid::uuid4();
        $id = AbstractAggregateIdTestProxy::fromString($uuid);
        $this->assertEquals($uuid, $id->asString());
    }

    public function testFromStringThrowsExceptionIfNonUuidStringIsPassed(): void
    {
        $this->expectException(\Exception::class);
        AbstractAggregateIdTestProxy::fromString('foo');
    }

    public function testNextReturnsInstanceWithUuidAsId(): void
    {
        $id = AbstractAggregateIdTestProxy::next();
        Assert::uuid($id->asString());
        $this->assertTrue(true);
    }

    public function testEqualsReturnsTrueIfOtherHasSameIdAndIsOfSameClass(): void
    {
        $id = AbstractAggregateIdTestProxy::next();
        $id2 = AbstractAggregateIdTestProxy::fromString($id->asString());
        $this->assertTrue($id->equals($id2));
    }

    public function testEqualsReturnsFalseIfOtherHasOtherIdAndIsOfSameClass(): void
    {
        $id = AbstractAggregateIdTestProxy::next();
        $id2 = AbstractAggregateIdTestProxy::next();
        $this->assertFalse($id->equals($id2));
    }

    public function testEqualsReturnsFalseIfOtherHasSameIdAndIsOfDifferentClass(): void
    {
        $id = AbstractAggregateIdTestProxy::next();
        $id2 = AbstractAggregateIdTestProxy2::fromString($id->asString());
        $this->assertFalse($id->equals($id2));
    }
}
