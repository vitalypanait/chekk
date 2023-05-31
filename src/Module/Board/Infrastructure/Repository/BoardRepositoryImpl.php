<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;

class BoardRepositoryImpl extends ServiceEntityRepository implements BoardRepository
{
    private TaskRepository $taskRepository;

    public function __construct(ManagerRegistry $registry, TaskRepository $taskRepository)
    {
        parent::__construct($registry, Board::class);

        $this->taskRepository = $taskRepository;
    }

    public function save(Board $board): void
    {
        $this->getEntityManager()->persist($board);
    }

    public function findById(string $id): ?Board
    {
        return Uuid::isValid($id) ? $this->findOneBy(['id' => $id]) : null;
    }

    public function findReadOnlyById(string $id): ?Board
    {
        return Uuid::isValid($id) ? $this->findOneBy(['readOnlyId' => $id]) : null;
    }

    public function getById(string $id): Board
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('b')
            ->from(Board::class, 'b')
            ->andWhere('b.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByIds(array $ids): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('b')
            ->from(Board::class, 'b')
            ->andWhere('b.id IN (:ids) OR b.readOnlyId IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    public function delete(Board $board): void
    {
        foreach ($this->taskRepository->findByBoard($board) as $task) {
            $this->taskRepository->delete($task);
        }

        $this->getEntityManager()->remove($board);
    }
}
