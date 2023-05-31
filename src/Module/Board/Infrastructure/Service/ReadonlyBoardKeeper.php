<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Service;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Service\ReadOnlyBoardKeeper as ReadOnlyBoardKeeperInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ReadonlyBoardKeeper implements ReadOnlyBoardKeeperInterface
{
    private const BOARDS_KEY = 'read_only_boards';

    public function __construct(
        private readonly RequestStack $requestStack
    ) {}

    public function addBoard(Board $board): void
    {
        $boards = $this->requestStack->getSession()->get(self::BOARDS_KEY);

        if ($boards === null) {
            $boards = [$board->getReadOnlyId()->toString()];
        } else {
            if (!in_array($board->getReadOnlyId()->toString(), $boards)) {
                $boards[] = $board->getReadOnlyId()->toString();
            }
        }

        $this->requestStack->getSession()->set(self::BOARDS_KEY, $boards);
    }

    public function exists(Board $board): bool
    {
        $boards = $this->requestStack->getSession()->get(self::BOARDS_KEY);

        return $boards !== null && in_array($board->getReadOnlyId()->toString(), $boards);
    }

    public function removeBoard(Board $board): void
    {
        $this->requestStack->getSession()->set(
            self::BOARDS_KEY,
            array_filter(
                (array) $this->requestStack->getSession()->get(self::BOARDS_KEY),
                fn($boardId) => $boardId !== $board->getReadOnlyId()->toString()
            )
        );
    }
}
