<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardCreate;

use Ramsey\Uuid\UuidInterface;

class BoardCreateCommand
{
    public function __construct(
        private ?UuidInterface $id = null
    ) {}

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }
}
