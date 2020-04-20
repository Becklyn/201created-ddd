<?php

namespace C201\Ddd\Events\Infrastructure\Store\Doctrine;

use C201\Ddd\Events\Domain\DomainEvent;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Tightenco\Collect\Support\Collection;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2019-08-21
 */
class DoctrineStoredEventAggregateRepository
{
    private EntityManagerInterface $em;

    private ObjectRepository $repository;

    private DoctrineStoredEventAggregateTypeRepository $aggregateTypeRepository;

    /**
     * @var DoctrineStoredEventAggregate[]|Collection
     */
    private $freshlyCreated;

    public function __construct(EntityManagerInterface $em, DoctrineStoredEventAggregateTypeRepository $aggregateTypeRepository)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(DoctrineStoredEventAggregate::class);
        $this->aggregateTypeRepository = $aggregateTypeRepository;
        $this->freshlyCreated = Collection::make();
    }

    public function findOneOrCreate(DomainEvent $event): DoctrineStoredEventAggregate
    {
        $aggregateType = $this->aggregateTypeRepository->findOneOrCreate($event);

        $freshlyCreatedMatch = $this->freshlyCreated
            ->filter(static function (DoctrineStoredEventAggregate $element) use ($event, $aggregateType) {
                return $element->id() === $event->aggregateId()->asString() && $element->aggregateType()->id() === $aggregateType->id();
            });
        if ($freshlyCreatedMatch->count() > 1) {
            throw new \Exception("Found more than one aggregate with id '{$event->aggregateId()->asString()}}' for type '{$aggregateType->name()}'");
        }

        if ($freshlyCreatedMatch->count() === 1) {
            return $freshlyCreatedMatch->first();
        }

        $aggregate = $this->repository->findOneBy(['id' => $event->aggregateId()->asString(), 'aggregateType' => $aggregateType->id()]);
        if ($aggregate === null) {
            $aggregate = new DoctrineStoredEventAggregate($event->aggregateId()->asString(), $aggregateType, 0);
            $this->em->persist($aggregate);
            $this->freshlyCreated->push($aggregate);
        }

        return $aggregate;
    }

    public function clearFreshlyCreated(): void
    {
        $this->freshlyCreated = Collection::make();
        $this->aggregateTypeRepository->clearFreshlyCreated();
    }
}
