<?php

declare(strict_types=1);

namespace App\Module\Core\Domain\Repository;

use App\Module\Core\Domain\Entity\User;

interface UserRepository
{
    public function save(User $user): void;
}
