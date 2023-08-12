<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\CommentUpdate;

use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Common\Command\CommandHandler;

class CommentUpdateHandler implements CommandHandler
{
    public function __construct(
        private readonly CommentRepository $commentRepository
    ) {}

    public function __invoke(CommentUpdateCommand $command): void
    {
        $comment = $this->commentRepository->getById($command->getId());

        $comment->updateContent($command->getContent());
    }
}
