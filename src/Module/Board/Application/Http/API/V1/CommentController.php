<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Model\Comment;
use App\Module\Board\Application\Http\API\V1\Request\CommentCreateRequest;
use App\Module\Board\Application\UseCase\CommentCreate\CommentCreateCommand;
use App\Module\Board\Application\UseCase\CommentDelete\CommentDeleteCommand;
use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Bus\CommandBus;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly CommandBus $commandBus,
        private readonly CommentRepository $commentRepository
    ) {}

    #[Route(
        '/api/v1/comment/',
        methods: ['POST']
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CommentCreateRequest::class)))]
    #[OA\Tag(name: 'Comment')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about comment',
        content: new Model(type: Comment::class)
    )]
    public function create(CommentCreateRequest $request): Response
    {
        $task = $this->taskRepository->findById($request->getTaskId());

        if ($task === null) {
            return new Response('', 404);
        }

        $command = new CommentCreateCommand($request->getTaskId(), $request->getContent());

        $this->commandBus->execute($command);

        $comment = $this->commentRepository->getById($command->getId());

        return $this->json((new Comment($comment->getId()->toString(), $comment->getContent()))->jsonSerialize());
    }

    #[Route(
        '/api/v1/comment/{id}',
        methods: ['DELETE']
    )]
    #[OA\Tag(name: 'Comment')]
    public function delete(string $id): Response
    {
        $comment = $this->commentRepository->findById($id);

        if ($comment === null) {
            return new Response('', 404);
        }

        $command = new CommentDeleteCommand($id);

        $this->commandBus->execute($command);

        return $this->json([]);
    }
}
