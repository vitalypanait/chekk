<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Entity\BoardId;
use App\Module\Board\Domain\Repository\BoardIdRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;

class BoardIdRepositoryImpl extends ServiceEntityRepository implements BoardIdRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardId::class);
    }

    public function save(BoardId $boardId): void
    {
        $this->getEntityManager()->persist($boardId);
    }

    public function findById(string $id): ?BoardId
    {
        return Uuid::isValid($id) ? $this->findOneBy(['id' => $id]) : null;
    }

    public function getById(string $id): BoardId
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('bid')
            ->from(BoardId::class, 'bid')
            ->andWhere('bid.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByBoard(Board $board): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('bid')
            ->from(BoardId::class, 'bid')
            ->andWhere('bid.board = :board')
            ->setParameter('board', $board)
            ->getQuery()
            ->getResult();
    }

    public function getReadonlyByBoard(Board $board): BoardId
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('bid')
            ->from(BoardId::class, 'bid')
            ->andWhere('bid.board = :board')
            ->andWhere('bid.readOnly = true')
            ->setParameter('board', $board)
            ->getQuery()
            ->getSingleResult();
    }

    public function delete(BoardId $boardId): void
    {
        $this->getEntityManager()->remove($boardId);
    }

    public function findByIds(array $ids): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('bid, b')
            ->from(BoardId::class, 'bid')
            ->join('bid.board', 'b')
            ->andWhere('bid.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    public function findByOwner(string $email): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('bid, b')
            ->from(BoardId::class, 'bid')
            ->join('bid.board', 'b')
            ->join('b.owner', 'owner')
            ->andWhere('bid.readOnly = false')
            ->andWhere('owner.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
    }
}
