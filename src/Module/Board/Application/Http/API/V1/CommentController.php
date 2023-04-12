<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Request\CommentCreateRequest;
use App\Module\Board\Application\UseCase\CommentCreate\CommentCreateCommand;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Bus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly CommandBus     $commandBus
    ) {}

    #[Route(
        '/api/v1/comment/',
        methods: ['POST']
    )]
    public function create(CommentCreateRequest $request): Response
    {
        $board = $this->taskRepository->findById($request->getTaskId());

        if ($board === null) {
            return new Response('', 404);
        }

        $this->commandBus->execute(new CommentCreateCommand($request->getTaskId(), $request->getContent()));

        return $this->json([]);
    }
}