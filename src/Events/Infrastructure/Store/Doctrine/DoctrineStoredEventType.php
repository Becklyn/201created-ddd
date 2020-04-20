<?php

namespace C201\Ddd\Events\Infrastructure\Store\Doctrine;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-08-21
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="event_store_event_types",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="uniq_event_type_name", columns={"name"}),
 *     }
 * )
 *
 */
class DoctrineStoredEventType
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $id;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
