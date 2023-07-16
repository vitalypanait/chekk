<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Model\Label;
use App\Module\Board\Application\Http\API\V1\Model\TaskLabel;
use App\Module\Board\Application\Http\API\V1\Request\TaskLabelCreateRequest;
use App\Module\Board\Application\UseCase\TaskLabelCreate\TaskLabelCreateCommand;
use App\Module\Board\Application\UseCase\TaskLabelDelete\TaskLabelDeleteCommand;
use App\Module\Board\Domain\Repository\LabelRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Bus\CommandBus;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskLabelController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly LabelRepository $labelRepository,
        private readonly CommandBus $commandBus,
    ) {}

    #[Route(
        '/api/v1/task-label/',
        methods: ['POST']
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: TaskLabelCreateRequest::class)))]
    #[OA\Tag(name: 'TaskLabel')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about task',
        content: new Model(type: TaskLabel::class)
    )]
    public function create(TaskLabelCreateRequest $request): Response
    {
        $task = $this->taskRepository->findById($request->getTaskId());

        if ($task === null) {
            return new Response('', 404);
        }

        $label = $this->labelRepository->getById($request->getLabelId());

        $command = new TaskLabelCreateCommand($request->getTaskId(), $request->getLabelId());

        $this->commandBus->execute($command);

        return $this->json(
            (
                new TaskLabel(
                    $command->getId(),
                    $task->getId()->toString(),
                    new Label(
                        $label->getId()->toString(),
                        $label->getTitle(),
                        $label->getColor()
                    )
                )
            )->jsonSerialize()
        );
    }

    #[Route(
        '/api/v1/task-label/{id}',
        methods: ['DELETE']
    )]
    #[OA\Tag(name: 'TaskLabel')]
    public function delete(string $id): Response
    {
        $this->commandBus->execute(new TaskLabelDeleteCommand($id));

        return $this->json([]);
    }
}
