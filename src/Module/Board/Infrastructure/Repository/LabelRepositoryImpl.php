<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Entity\Label;
use App\Module\Board\Domain\Entity\Task;
use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Board\Domain\Repository\LabelRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;

class LabelRepositoryImpl extends ServiceEntityRepository implements LabelRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Label::class);
    }

    public function save(Label $label): void
    {
        $this->getEntityManager()->persist($label);
    }

    public function delete(Label $label): void
    {
        $this->getEntityManager()->remove($label);
    }

    public function getById(string $id): Label
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('l')
            ->from(Label::class, 'l')
            ->andWhere('l.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByBoard(Board $board): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('l')
            ->from(Label::class, 'l')
            ->andWhere('l.board = :board')
            ->setParameter('board', $board)
            ->orderBy('l.createdAt', 'asc')
            ->getQuery()
            ->getResult();
    }
}
