<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Repository;

use App\Module\Board\Domain\Entity\Comment;
use App\Module\Board\Domain\Repository\CommentRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepositoryImpl extends ServiceEntityRepository implements CommentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $comment): void
    {
        $this->getEntityManager()->persist($comment);
    }

    public function findByTaskIds(array $taskIds): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from(Comment::class, 'c')
            ->andWhere('IDENTITY(c.task) IN (:taskIds)')
            ->setParameter('taskIds', $taskIds)
            ->getQuery()
            ->getResult();
    }

    public function getById(string $id): Comment
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from(Comment::class, 'c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }
}
