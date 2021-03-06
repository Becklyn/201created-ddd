<?php

namespace C201\Ddd\Tests\Events\Domain;

use C201\Ddd\Events\Domain\EventId;
use Ramsey\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class EventIdTest extends TestCase
{
    public function testFromStringAcceptsUuid()
    {
        $uuid = Uuid::uuid4();
        $id = EventId::fromString($uuid);
        $this->assertEquals($uuid, $id->asString());
    }

    public function testFromStringThrowsExceptionForNonUuid()
    {
        $notUuid = 'foo';
        $this->expectException(\Exception::class);
        EventId::fromString($notUuid);
    }
}
