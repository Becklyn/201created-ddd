<?php

namespace C201\Ddd\Identity\Domain;

use Webmozart\Assert\Assert;
use Ramsey\Uuid\Uuid;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-03
 */
abstract class AbstractAggregateId implements AggregateId
{
    protected string $id;

    protected function __construct(string $id)
    {
        Assert::uuid($id);
        $this->id = $id;
    }

    public static function fromString(string $id): self
    {
        return new static($id);
    }

    public static function next(): self
    {
        return new static(Uuid::uuid4());
    }

    public function asString(): string
    {
        return $this->id;
    }

    public function equals(AggregateId $other): bool
    {
        return $this->id === $other->asString() && get_class($this) === get_class($other);
    }
}
