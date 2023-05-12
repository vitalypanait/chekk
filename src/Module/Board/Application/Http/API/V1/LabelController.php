<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Model\Label;
use App\Module\Board\Application\Http\API\V1\Request\LabelCreateRequest;
use App\Module\Board\Application\Http\API\V1\Request\LabelUpdateRequest;
use App\Module\Board\Application\UseCase\LabelCreate\LabelCreateCommand;
use App\Module\Board\Application\UseCase\LabelDelete\LabelDeleteCommand;
use App\Module\Board\Application\UseCase\LabelUpdate\LabelUpdateCommand;
use App\Module\Board\Domain\Entity\Label as LabelEntity;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Repository\LabelRepository;
use App\Module\Common\Bus\CommandBus;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LabelController extends AbstractController
{
    public function __construct(
        private readonly BoardRepository $boardRepository,
        private readonly LabelRepository $labelRepository,
        private readonly CommandBus $commandBus
    ) {}

    #[Route(
        '/api/v1/label/',
        methods: ['POST']
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: LabelCreateRequest::class)))]
    #[OA\Tag(name: 'Label')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about label',
        content: new Model(type: Label::class)
    )]
    public function create(LabelCreateRequest $request): Response
    {
        $board = $this->boardRepository->findById($request->getBoardId());

        if ($board === null) {
            return new Response('', 404);
        }

        $command = new LabelCreateCommand($request->getBoardId(), $request->getTitle(), $request->getColor());

        $this->commandBus->execute($command);

        $label = $this->labelRepository->getById($command->getId());

        return $this->json(
            (new Label($label->getId()->toString(), $label->getTitle(), $label->getColor()))->jsonSerialize()
        );
    }

    #[Route(
        '/api/v1/label/',
        methods: ['PUT']
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: LabelUpdateRequest::class)))]
    #[OA\Tag(name: 'Label')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about label',
        content: new Model(type: Label::class)
    )]
    public function update(LabelUpdateRequest $request): Response
    {
        $label = $this->labelRepository->getById($request->getId());

        $this->commandBus->execute(new LabelUpdateCommand($request->getId(), $request->getTitle()));

        $label = $this->labelRepository->getById($label->getId()->toString());

        return $this->json(
            (new Label($label->getId()->toString(), $label->getTitle(), $label->getColor()))->jsonSerialize()
        );
    }

    #[Route(
        '/api/v1/label/{id}',
        methods: ['DELETE']
    )]
    #[OA\Tag(name: 'Label')]
    public function delete(string $id): Response
    {
        $this->commandBus->execute(new LabelDeleteCommand($id));

        return $this->json([]);
    }

    #[Route(
        '/api/v1/label/byBoard/{id}',
        methods: ['GET']
    )]
    #[OA\Tag(name: 'Label')]
    #[OA\Response(
        response: 200,
        description: 'Returns labels by board',
    )]
    public function findByBoard(string $id): Response
    {
        $board = $this->boardRepository->findById($id);

        if ($board === null) {
            return new Response('', 404);
        }

        return $this->json(
            array_map(
                fn(LabelEntity $label) => new Label(
                    $label->getId()->toString(),
                    $label->getTitle(),
                    $label->getColor()
                ),
                $this->labelRepository->findByBoard($board)
            )
        );
    }
}
