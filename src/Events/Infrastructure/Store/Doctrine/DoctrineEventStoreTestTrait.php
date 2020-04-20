<?php

namespace C201\Ddd\Events\Infrastructure\Store\Doctrine;

use C201\Ddd\Tests\Events\Infrastructure\Store\Doctrine\DoctrineEventStoreTestAggregateId;
use Ramsey\Uuid\Uuid;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-08-28
 */
trait DoctrineEventStoreTestTrait
{
    public function givenAnAggregateId(): DoctrineEventStoreTestAggregateId
    {
        return DoctrineEventStoreTestAggregateId::fromString(Uuid::uuid4());
    }
}
