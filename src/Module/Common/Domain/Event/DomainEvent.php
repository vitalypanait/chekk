<?php

declare(strict_types=1);

namespace App\Module\Common\Domain\Event;

class DomainEvent extends Event
{
    /** @var object */
    private $entity;

    public function __construct(object $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }
}
