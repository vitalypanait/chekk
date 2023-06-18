<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardTakeOwnership;

use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Common\Command\CommandHandler;
use App\Module\Core\Domain\Repository\UserRepository;

class BoardTakeOwnershipHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardRepository $boardRepository,
        private readonly UserRepository $userRepository
    ) {}

    public function __invoke(BoardTakeOwnershipCommand $command): void
    {
        $board = $this->boardRepository->getById($command->getId());

        $board->takeOwnership(
            $this->userRepository->getById($command->getOwnerId())
        );
    }
}
