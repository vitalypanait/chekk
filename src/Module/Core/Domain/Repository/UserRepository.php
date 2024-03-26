<?php

declare(strict_types=1);

namespace App\Module\Core\Domain\Repository;

use App\Module\Core\Domain\Entity\User;

interface UserRepository
{
    public function save(User $user): void;

    public function getById(string $id): User;

    public function getByEmail(string $email): User;

    public function findByEmail(string $email): ?User;
}
