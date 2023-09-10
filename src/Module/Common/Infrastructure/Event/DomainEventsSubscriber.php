<?php

declare(strict_types=1);

namespace App\Module\Common\Infrastructure\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DomainEventsSubscriber implements EventSubscriber
{
    /** @var object[] */
    private array $entities;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->entities        = [];
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
            Events::preFlush,
            Events::postFlush,
        ];
    }

    private function canContainEvents(object $entity): bool
    {
        return method_exists($entity, 'releaseEvents');
    }

    private function addEventContainableEntity(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($this->canContainEvents($entity)) {
            $this->entities[spl_object_hash($entity)] = $entity;
        }
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->addEventContainableEntity($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->addEventContainableEntity($args);
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $this->addEventContainableEntity($args);
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        foreach ($args->getEntityManager()->getUnitOfWork()->getIdentityMap() as $entities) {
            foreach ($entities as $entity) {
                $oid = spl_object_hash($entity);

                if ($this->canContainEvents($entity) && ! isset($this->entities[$oid])) {
                    $this->entities[spl_object_hash($entity)] = $entity;
                }
            }
        }
    }

    public function postFlush(): void
    {
        foreach ($this->entities as $entity) {
            foreach ($entity->releaseEvents() as $event) {
                $this->eventDispatcher->dispatch($event);
            }
        }

        $this->entities = [];
    }
}
