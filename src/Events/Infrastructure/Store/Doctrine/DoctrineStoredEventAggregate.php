<?php

namespace C201\Ddd\Events\Infrastructure\Store\Doctrine;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-08-21
 *
 * @ORM\Entity
 * @ORM\Table(name="event_store_aggregates")
 */
class DoctrineStoredEventAggregate
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventAggregateType")
     * @ORM\JoinColumn(name="aggregate_type_id", referencedColumnName="id")
     */
    private DoctrineStoredEventAggregateType $aggregateType;

    /**
     * @ORM\Column(name="version", type="integer", nullable=false)
     */
    private int $version;

    public function __construct(string $id, DoctrineStoredEventAggregateType $aggregateType, int $version = 0)
    {
        $this->id = $id;
        $this->aggregateType = $aggregateType;
        $this->version = $version;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function aggregateType(): DoctrineStoredEventAggregateType
    {
        return $this->aggregateType;
    }

    public function version(): int
    {
        return $this->version;
    }

    public function incrementVersion(): self
    {
        $this->version++;
        return $this;
    }
}
