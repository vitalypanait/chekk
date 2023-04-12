<?php

declare(strict_types=1);

namespace App\Module\Common\Traits;

use DateTimeInterface;

trait Timestamped
{
    protected DateTimeInterface $createdAt;

    protected DateTimeInterface $updatedAt;

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }
}