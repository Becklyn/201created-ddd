<?php

namespace C201\Ddd\Tests\Events\Infrastructure\Store\Doctrine;

use C201\Ddd\Events\Domain\DomainEvent;
use C201\Ddd\Events\Domain\DomainEventTestTrait;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineEventStore;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEvent;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventAggregate;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventAggregateRepository;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventType;
use C201\Ddd\Events\Infrastructure\Store\Doctrine\DoctrineStoredEventTypeRepository;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Serializer\SerializerInterface;

class DoctrineEventStoreTest extends TestCase
{
    use ProphecyTrait;
    use DomainEventTestTrait;

    /**
     * @var ObjectProphecy|EntityManagerInterface
     */
    private $em;

    /**
     * @var ObjectProphecy|ObjectRepository
     */
    private $repository;

    /**
     * @var ObjectProphecy|DoctrineStoredEventAggregateRepository
     */
    private $aggregateRepository;

    /**
     * @var ObjectProphecy|DoctrineStoredEventTypeRepository
     */
    private $eventTypeRepository;

    /**
     * @var ObjectProphecy|SerializerInterface
     */
    private $serializer;

    private DoctrineEventStore $fixture;

    protected function setUp(): void
    {
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->repository = $this->prophesize(ObjectRepository::class);
        $this->em->getRepository(DoctrineStoredEvent::class)->willReturn($this->repository->reveal());
        $this->aggregateRepository = $this->prophesize(DoctrineStoredEventAggregateRepository::class);
        $this->eventTypeRepository = $this->prophesize(DoctrineStoredEventTypeRepository::class);
        $this->serializer = $this->prophesize(SerializerInterface::class);

        $this->fixture = new DoctrineEventStore(
            $this->em->reveal(),
            $this->aggregateRepository->reveal(),
            $this->eventTypeRepository->reveal(),
            $this->serializer->reveal(),
            true
        );
    }

    public function testAppendPersistsStoredEvent(): void
    {
        $eventProphecy = $this->prophesize(DomainEvent::class);
        $eventProphecy->id()->willReturn($this->givenAnEventId());
        $eventProphecy->raisedTs()->willReturn($this->givenARaisedTs());
        $event = $eventProphecy->reveal();

        $aggregate = $this->prophesize(DoctrineStoredEventAggregate::class);
        $incrementedVersion = rand(1, 1000);
        $aggregate->incrementVersion()->will(function() use ($incrementedVersion) {
            $this->version()->willReturn($incrementedVersion);
            return $this;
        });
        $this->aggregateRepository->findOneOrCreate($event)->willReturn($aggregate->reveal());

        $eventType = new DoctrineStoredEventType('foo', 'bar');
        $this->eventTypeRepository->findOneOrCreate($event)->willReturn($eventType);

        $serializedEvent = uniqid();
        $this->serializer->serialize($event, 'json')->willReturn($serializedEvent);

        $aggregate->incrementVersion()->shouldBeCalledTimes(1);
        $this->em->persist(Argument::that(function (DoctrineStoredEvent $storedEvent) use ($event, $aggregate, $incrementedVersion, $serializedEvent, $eventType) {
            return $storedEvent->eventId() === $event->id()->asString() &&
                $storedEvent->raisedTs() === $event->raisedTs() &&
                $storedEvent->aggregate() === $aggregate->reveal() &&
                $storedEvent->eventType() === $eventType &&
                $storedEvent->version() === $incrementedVersion &&
                $storedEvent->data() === $serializedEvent;
        }))->shouldBeCalledTimes(1);

        $this->fixture->append($event);
    }

    public function testAppendDoesNothingIfStoreIsDisabled(): void
    {
        $this->fixture = new DoctrineEventStore(
            $this->em->reveal(),
            $this->aggregateRepository->reveal(),
            $this->eventTypeRepository->reveal(),
            $this->serializer->reveal(),
            false
        );

        $this->em->persist(Argument::any())->shouldNotBeCalled();
    }

    public function testClearFreshlyCreatedClearsAggregateAndEventTypeRepositories(): void
    {
        $this->aggregateRepository->clearFreshlyCreated()->shouldBeCalled();
        $this->eventTypeRepository->clearFreshlyCreated()->shouldBeCalled();
        $this->fixture->clearFreshlyCreated();
    }

    public function testClearFreshlyCreatedDoesNotClearAggregateAndEventTypeRepositoriesIfStoreIsDisabled(): void
    {
        $this->fixture = new DoctrineEventStore(
            $this->em->reveal(),
            $this->aggregateRepository->reveal(),
            $this->eventTypeRepository->reveal(),
            $this->serializer->reveal(),
            false
        );

        $this->aggregateRepository->clearFreshlyCreated()->shouldNotBeCalled();
        $this->eventTypeRepository->clearFreshlyCreated()->shouldNotBeCalled();
        $this->fixture->clearFreshlyCreated();
    }
}
