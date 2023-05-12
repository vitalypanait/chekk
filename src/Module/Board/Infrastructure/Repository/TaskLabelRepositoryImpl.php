<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Entity\Label;
use App\Module\Board\Domain\Entity\Task;
use App\Module\Board\Domain\Entity\TaskLabel;
use App\Module\Board\Domain\Repository\TaskLabelRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskLabelRepositoryImpl extends ServiceEntityRepository implements TaskLabelRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskLabel::class);
    }

    public function save(TaskLabel $taskLabel): void
    {
        $this->getEntityManager()->persist($taskLabel);
    }

    public function delete(TaskLabel $taskLabel): void
    {
        $this->getEntityManager()->remove($taskLabel);
    }

    public function getById(string $id): TaskLabel
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from(TaskLabel::class, 't')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByBoard(Board $board): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('tl, t, l')
            ->from(TaskLabel::class, 'tl')
            ->join('tl.task', 't')
            ->join('tl.label', 'l')
            ->andWhere('t.board = :board')
            ->setParameter('board', $board)
            ->orderBy('tl.createdAt', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function findByTask(Task $task): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('tl')
            ->from(TaskLabel::class, 'tl')
            ->andWhere('tl.task = :task')
            ->setParameter('task', $task)
            ->getQuery()
            ->getResult();
    }

    public function findByLabel(Label $label): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('tl')
            ->from(TaskLabel::class, 'tl')
            ->andWhere('tl.label = :label')
            ->setParameter('label', $label)
            ->getQuery()
            ->getResult();
    }
}
