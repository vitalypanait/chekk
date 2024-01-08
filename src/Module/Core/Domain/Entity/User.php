<?php

declare(strict_types=1);

namespace App\Module\Core\Domain\Entity;

use App\Module\Common\Domain\Event\EventProducer;
use App\Module\Common\Traits\Timestamped;
use App\Module\Core\Domain\Event\UserCreated;
use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use EventProducer;
    use Timestamped;

    private const ROLE_USER = 'ROLE_USER';

    private UuidInterface $id;

    private string $email;

    private array $roles;
    private string $password;

    public function __construct(string $email)
    {
        $this->id = Uuid::uuid4();
        $this->email = $email;
        $this->roles = [];
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();

        $this->raiseEvent(new UserCreated($this));
    }

    public function setPassword(UserPasswordHasherInterface $passwordHasher, string $password): void
    {
        $this->password = $passwordHasher->hashPassword($this, $password);
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return [self::ROLE_USER];
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}