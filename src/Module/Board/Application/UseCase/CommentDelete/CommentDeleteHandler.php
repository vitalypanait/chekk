<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\CommentDelete;

use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Common\Command\CommandHandler;

class CommentDeleteHandler implements CommandHandler
{
    public function __construct(
        private readonly CommentRepository $commentRepository
    ) {}

    public function __invoke(CommentDeleteCommand $command): void
    {
        $task = $this->commentRepository->getById($command->getId());

        $this->commentRepository->delete($task);
    }
}
