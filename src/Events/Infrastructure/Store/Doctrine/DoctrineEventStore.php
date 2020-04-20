<?php

namespace C201\Ddd\Events\Infrastructure\Store\Doctrine;

use C201\Ddd\Events\Domain\AggregateEventStream;
use C201\Ddd\Events\Domain\DomainEvent;
use C201\Ddd\Events\Domain\EventStore;
use C201\Ddd\Identity\Domain\AggregateId;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Tightenco\Collect\Support\Collection;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-08-21
 */
class DoctrineEventStore implements EventStore
{
    private EntityManagerInterface $em;

    private ObjectRepository $repository;

    private DoctrineStoredEventAggregateRepository $aggregateRepository;

    private DoctrineStoredEventTypeRepository $eventTypeRepository;

    private SerializerInterface $serializer;

    public function __construct(
        EntityManagerInterface $em,
        DoctrineStoredEventAggregateRepository $aggregateRepository,
        DoctrineStoredEventTypeRepository $eventTypeRepository,
        SerializerInterface $serializer
    ) {
        $this->em = $em;
        $this->repository = $em->getRepository(DoctrineStoredEvent::class);
        $this->aggregateRepository = $aggregateRepository;
        $this->eventTypeRepository = $eventTypeRepository;
        $this->serializer = $serializer;
    }

    public function append(DomainEvent $event): void
    {
        $aggregate = $this->aggregateRepository->findOneOrCreate($event);
        $eventType = $this->eventTypeRepository->findOneOrCreate($event);

        $data = $this->serializer->serialize($event, 'json');

        $aggregate->incrementVersion();
        $storedEvent = new DoctrineStoredEvent($event->id()->asString(), $aggregate, $aggregate->version(), $eventType, $event->raisedTs(), $data);
        $this->em->persist($storedEvent);
    }

    public function getAggregateStream(AggregateId $aggregateId, string $aggregateType): AggregateEventStream
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
            ->from(DoctrineStoredEvent::class, 'e')
            ->join(DoctrineStoredEventAggregate::class, 'a', 'WITH', 'e.aggregate = a.id')
            ->join(DoctrineStoredEventAggregateType::class, 'at', 'WITH', 'a.aggregateType = at.id')
            ->andWhere('a.id = :aggregateId')
            ->andWhere('at.name = :aggregateType')
            ->setParameter('aggregateId', $aggregateId->asString())
            ->setParameter('aggregateType', $aggregateType)
            ->addOrderBy('e.raisedTs', 'ASC')
            ->addOrderBy('e.version', 'ASC');

        $storedEvents = Collection::make($qb->getQuery()->execute())
            ->map(fn(DoctrineStoredEvent $storedEvent) => $this->serializer->deserialize($storedEvent->data(), $storedEvent->eventType()->name(), 'json'));

        return new AggregateEventStream($aggregateId, $aggregateType, $storedEvents);
    }

    public function clearFreshlyCreated(): void
    {
        $this->aggregateRepository->clearFreshlyCreated();
        $this->eventTypeRepository->clearFreshlyCreated();
    }
}
