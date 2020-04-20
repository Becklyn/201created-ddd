<?php

namespace C201\Ddd\Events\Domain;

use C201\Ddd\Identity\Domain\AggregateId;
use Tightenco\Collect\Support\Collection;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-11-20
 */
class AggregateEventStream
{
    private AggregateId $aggregateId;

    private string $aggregateType;

    /**
     * @var Collection|DomainEvent[]
     */
    private Collection $events;

    public function __construct(AggregateId $aggregateId, string $aggregateType, Collection $events)
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateType = $aggregateType;
        $this->events = $events;
    }

    public function aggregateId(): AggregateId
    {
        return $this->aggregateId;
    }

    public function aggregateType(): string
    {
        return $this->aggregateType;
    }

    /**
     * @return Collection|DomainEvent[]
     */
    public function events(): Collection
    {
        return $this->events;
    }
}
