<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardRemovePinCode;

use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Common\Command\CommandHandler;

class BoardRemovePinCodeHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardIdRepository $boardIdRepository,
    ) {}

    public function __invoke(BoardRemovePinCodeCommand $command): void
    {
        $boardId = $this->boardIdRepository->getById($command->getId());

        foreach ($this->boardIdRepository->findByBoard($boardId->getBoard()) as $boardId) {
            $boardId->removePinCode();
        }
    }
}
