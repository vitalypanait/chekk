<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardTitleUpdate;

use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Common\Command\CommandHandler;

class BoardUpdateHandler implements CommandHandler
{
    public function __construct(private readonly BoardRepository $boardRepository) {}

    public function __invoke(BoardUpdateCommand $command): void
    {
        $board = $this->boardRepository->getById($command->getId());

        if ($command->getTitle() !== null) {
            $board->updateTitle($command->getTitle());
        }

        if ($command->getDisplay() !== null) {
            $board->updateDisplay($command->getDisplay());
        }

        if ($command->getThemeColor() !== null) {
            $themeColor = $command->getThemeColor();

            if (mb_strlen($themeColor) === 7) {
                $themeColor = mb_substr($themeColor, 1);
            }

            $board->updateThemeColor($themeColor);
        }
    }
}
