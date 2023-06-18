<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardTakeOwnership;

class BoardTakeOwnershipCommand
{
    public function __construct(
        private string $id,
        private string $ownerId
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }
}
