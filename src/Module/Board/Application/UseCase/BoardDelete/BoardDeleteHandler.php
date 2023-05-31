<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardDelete;

use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Common\Command\CommandHandler;

class BoardDeleteHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardRepository $boardRepository
    ) {}

    public function __invoke(BoardDeleteCommand $command): void
    {
        $task = $this->boardRepository->getById($command->getId());

        $this->boardRepository->delete($task);
    }
}
