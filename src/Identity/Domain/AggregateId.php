<?php

namespace C201\Ddd\Identity\Domain;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-02
 */
interface AggregateId
{
    public function asString(): string;

    public function equals(AggregateId $other): bool;
}
