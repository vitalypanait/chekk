<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Repository;

use App\Module\Board\Domain\Entity\BoardId;
use App\Module\Board\Domain\Entity\BoardVisitedHistory;
use App\Module\Board\Domain\Repository\BoardVisitedHistoryRepository;
use App\Module\Core\Domain\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;

class BoardVisitedHistoryImpl extends ServiceEntityRepository implements BoardVisitedHistoryRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardVisitedHistory::class);
    }

    public function save(BoardVisitedHistory $boardVisitedHistory): void
    {
        $this->getEntityManager()->persist($boardVisitedHistory);
    }

    public function findById(string $id): ?BoardVisitedHistory
    {
        return Uuid::isValid($id) ? $this->findOneBy(['id' => $id]) : null;
    }

    public function findByOwner(string $email): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('bvh,bid, b')
            ->from(BoardVisitedHistory::class, 'bvh')
            ->join('bvh.boardId', 'bid')
            ->join('bid.board', 'b')
            ->join('bvh.user', 'user')
            ->andWhere('user.email = :email')
            ->setParameter('email', $email)
            ->orderBy('bvh.visitedAt', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function delete(BoardVisitedHistory $boardVisitedHistory): void
    {
        $this->getEntityManager()->remove($boardVisitedHistory);
    }

    public function findByBoardAndUser(BoardId $boardId, User $user): ?BoardVisitedHistory
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('bvh')
            ->from(BoardVisitedHistory::class, 'bvh')
            ->andWhere('bvh.boardId = :boardId')
            ->andWhere('bvh.user = :user')
            ->setParameter('boardId', $boardId)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
