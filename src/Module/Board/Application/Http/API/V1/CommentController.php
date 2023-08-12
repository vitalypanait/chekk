<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Model\Comment;
use App\Module\Board\Application\Http\API\V1\Request\CommentCreateRequest;
use App\Module\Board\Application\Http\API\V1\Request\CommentUpdateRequest;
use App\Module\Board\Application\UseCase\CommentCreate\CommentCreateCommand;
use App\Module\Board\Application\UseCase\CommentDelete\CommentDeleteCommand;
use App\Module\Board\Application\UseCase\CommentUpdate\CommentUpdateCommand;
use App\Module\Board\Domain\Entity\BoardId;
use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Bus\CommandBus;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly CommandBus $commandBus,
        private readonly CommentRepository $commentRepository
    ) {}

    #[Route(
        '/api/v1/board/{id}/comment/',
        methods: ['POST']
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CommentCreateRequest::class)))]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about comment',
        content: new Model(type: Comment::class)
    )]
    #[IsGranted('edit', 'boardId')]
    public function create(BoardId $boardId, CommentCreateRequest $request): Response
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
        '/api/v1/board/{id}/comment/{commentId}',
        methods: ['DELETE']
    )]
    #[OA\Tag(name: 'Board')]
    #[IsGranted('edit', 'boardId')]
    public function delete(BoardId $boardId, string $commentId): Response
    {
        $comment = $this->commentRepository->findById($commentId);

        if ($comment === null) {
            return new Response('', 404);
        }

        $command = new CommentDeleteCommand($commentId);

        $this->commandBus->execute($command);

        return $this->json([]);
    }

    #[Route(
        '/api/v1/board/{id}/comment/{commentId}',
        methods: ['PUT']
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID of the comment',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', example: '839cf68e-4062-4259-addc-09ce5644ee52')
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CommentUpdateRequest::class)))]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about comment',
        content: new Model(type: Comment::class)
    )]
    #[IsGranted('edit', 'boardId')]
    public function update(BoardId $boardId, string $commentId, CommentUpdateRequest $request): Response
    {
        $comment = $this->commentRepository->findById($commentId);

        if ($comment === null) {
            return new Response('', 404);
        }

        $this->commandBus->execute(
            new CommentUpdateCommand($commentId, $request->getContent())
        );

        $comment = $this->commentRepository->getById($comment->getId()->toString());

        return $this->json(
            (new Comment($comment->getId()->toString(), $comment->getContent()))->jsonSerialize()
        );
    }
}
