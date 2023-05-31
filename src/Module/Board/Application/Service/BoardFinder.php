<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Service;

use App\Module\Board\Application\DTO\Board;
use App\Module\Board\Domain\Repository\BoardRepository;

class BoardFinder
{
    public function __construct(
        private readonly BoardRepository $boardRepository
    ) {}

    public function findById(string $id): ?Board
    {
        $readOnly = false;
        $board = $this->boardRepository->findById($id);

        if ($board === null) {
            $board = $this->boardRepository->findReadOnlyById($id);
            $readOnly = true;
        }

        return $board === null ? null : new Board($board, $readOnly);
    }
}