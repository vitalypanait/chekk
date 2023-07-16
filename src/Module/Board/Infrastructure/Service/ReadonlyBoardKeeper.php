<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Service;

use App\Module\Board\Domain\Entity\BoardId;
use App\Module\Board\Domain\Service\ReadOnlyBoardKeeper as ReadOnlyBoardKeeperInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ReadonlyBoardKeeper implements ReadOnlyBoardKeeperInterface
{
    private const BOARDS_KEY = 'read_only_boards';

    public function __construct(
        private readonly RequestStack $requestStack
    ) {}

    public function keep(BoardId $boardId): void
    {
        $boards = $this->requestStack->getSession()->get(self::BOARDS_KEY);

        if ($boardId->isReadOnly()) {
            if ($boards === null) {
                $boards = [$boardId->getId()->toString()];
            } else {
                if (!in_array($boardId->getId()->toString(), $boards)) {
                    $boards[] = $boardId->getId()->toString();
                }
            }
        } else {
            $boards = array_filter(
                (array) $this->requestStack->getSession()->get(self::BOARDS_KEY),
                fn($id) => $id !== $boardId->getId()->toString()
            );
        }

        $this->requestStack->getSession()->set(self::BOARDS_KEY, $boards);
    }

    public function exists(BoardId $boardId): bool
    {
        $boards = $this->requestStack->getSession()->get(self::BOARDS_KEY);

        return $boards !== null && in_array($boardId->getId()->toString(), $boards);
    }
}
