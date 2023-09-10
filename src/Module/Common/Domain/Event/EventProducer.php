<?php

declare(strict_types=1);

namespace App\Module\Common\Domain\Event;

trait EventProducer
{
    /** @var object[] */
    private array $queuedEvents = [];

    public function releaseEvents(): array
    {
        $events             = $this->queuedEvents;
        $this->queuedEvents = [];

        return $events;
    }

    private function raiseEvent(object $event): void
    {
        $this->queuedEvents[] = $event;
    }
}
