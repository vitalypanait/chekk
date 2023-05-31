<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Entity\Task;
use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Board\Domain\Repository\TaskLabelRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;

class TaskRepositoryImpl extends ServiceEntityRepository implements TaskRepository
{
    private CommentRepository $commentRepository;
    private TaskLabelRepository $taskLabelRepository;

    public function __construct(
        ManagerRegistry $registry,
        CommentRepository $commentRepository,
        TaskLabelRepository $taskLabelRepository
    ) {
        parent::__construct($registry, Task::class);

        $this->commentRepository = $commentRepository;
        $this->taskLabelRepository = $taskLabelRepository;
    }

    public function save(Task $task): void
    {
        $this->getEntityManager()->persist($task);
    }

    public function delete(Task $task): void
    {
        foreach ($this->commentRepository->findByTaskIds([$task->getId()]) as $comment) {
            $this->commentRepository->delete($comment);
        }

        foreach ($this->taskLabelRepository->findByTask($task) as $taskLabel) {
            $this->taskLabelRepository->delete($taskLabel);
        }

        $this->getEntityManager()->remove($task);
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
            ->where('t.board = :board')
            ->setParameter('board', $board)
            ->orderBy('t.position', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function findActiveByBoard(Board $board): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->andWhere('t.board = :board')
            ->andWhere('t.archivedAt IS NULL')
            ->setParameter('board', $board)
            ->orderBy('t.position', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function findArchivedByBoard(Board $board): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->andWhere('t.board = :board')
            ->andWhere('t.archivedAt IS NOT NULL')
            ->setParameter('board', $board)
            ->orderBy('t.archivedAt', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function getMaxPosition(Board $board): int
    {
        $result = $this->getEntityManager()->createQueryBuilder()
            ->select('MAX(t.position) as count')
            ->from(Task::class, 't')
            ->andWhere('t.board = :board')
            ->setParameter('board', $board)
            ->getQuery()
            ->getScalarResult();

        return (int) $result[0]['count'];
    }

    public function findTasksForUpdatePositions(string $boardId, array $taskIds): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->join('t.board', 'b')
            ->andWhere('b.id = :boardId')
            ->andWhere('t.id IN (:ids)')
            ->setParameter('boardId', $boardId)
            ->setParameter('ids', $taskIds)
            ->getQuery()
            ->getResult();
    }
}
