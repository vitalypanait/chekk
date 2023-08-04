<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardSetPinCode;

use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Board\Infrastructure\Service\PinCodeHasher;
use App\Module\Common\Command\CommandHandler;

class BoardSetPinCodeHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardIdRepository $boardIdRepository,
        private readonly PinCodeHasher     $pinCodeHasher,
    ) {}

    public function __invoke(BoardSetPinCodeCommand $command): void
    {
        $boardId = $this->boardIdRepository->getById($command->getId());
        $pinCode = $this->pinCodeHasher->getHasher()->hash($command->getPinCode());

        foreach ($this->boardIdRepository->findByBoard($boardId->getBoard()) as $boardId) {
            $boardId->setPinCode($pinCode);
        }
    }
}
