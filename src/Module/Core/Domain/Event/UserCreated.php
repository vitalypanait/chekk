<?php

declare(strict_types=1);

namespace App\Module\Core\Domain\Event;

use App\Module\Common\Domain\Event\DomainEvent;
use App\Module\Core\Domain\Entity\User;

class UserCreated extends DomainEvent
{
    protected string $id = 'user_created';

    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
