<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Entity\Task;
use App\Module\Board\Domain\Repository\TaskRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;

class TaskRepositoryImpl extends ServiceEntityRepository implements TaskRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $task): void
    {
        $this->getEntityManager()->persist($task);
    }

    public function findById(string $id): ?Task
    {
        return Uuid::isValid($id) ? $this->findOneBy(['id' => $id]) : null;
    }

    public function getById(string $id): Task
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByBoard(Board $board): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->andWhere('t.board = :board')
            ->setParameter('board', $board)
            ->getQuery()
            ->getResult();
    }
}