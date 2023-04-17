<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Repository\BoardRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;

class BoardRepositoryImpl extends ServiceEntityRepository implements BoardRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Board::class);
    }

    public function save(Board $board): void
    {
        $this->getEntityManager()->persist($board);
    }

    public function findById(string $id): ?Board
    {
        return Uuid::isValid($id) ? $this->findOneBy(['id' => $id]) : null;
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
}
