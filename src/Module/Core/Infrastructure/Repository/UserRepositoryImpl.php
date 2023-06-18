<?php

declare(strict_types=1);

namespace App\Module\Core\Infrastructure\Repository;

use App\Module\Core\Domain\Entity\User;
use App\Module\Core\Domain\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepositoryImpl extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getById(string $id): User
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function getByEmail(string $email): User
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleResult();
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
