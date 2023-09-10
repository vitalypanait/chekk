<?php

declare(strict_types=1);

namespace App\Module\Common\Domain\Event;

class Event
{
    protected string $id = '';

    public function getId(): string
    {
        return ! empty($this->id) ? $this->id : get_class($this);
    }
}
