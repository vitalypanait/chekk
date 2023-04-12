<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\CommentCreate;

use App\Module\Board\Domain\Entity\Comment;
use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Command\CommandHandler;

class CommentCreateHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly CommentRepository $commentRepository
    ) {}

    public function __invoke(CommentCreateCommand $command): void
    {
        $task = $this->taskRepository->getById($command->getTaskId());

        $comment = new Comment($task, $command->getContext());

        $this->commentRepository->save($comment);
    }
}