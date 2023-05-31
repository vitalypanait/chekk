<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http;

use App\Module\Board\Application\Service\BoardFinder;
use App\Module\Board\Application\Service\BoardsCookieJar;
use App\Module\Board\Application\UseCase\BoardCreate\BoardCreateCommand;
use App\Module\Board\Domain\Service\ReadOnlyBoardKeeper;
use App\Module\Common\Bus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly CommandBus  $commandBus,
        private readonly BoardFinder $boardFinder,
        private readonly ReadOnlyBoardKeeper $boardKeeper,
        private readonly BoardsCookieJar $boardsCookieJar
    ) {}

    #[Route(
        '/',
    )]
    public function index(): Response
    {
        $boardIds = $this->boardsCookieJar->all();

        if (empty($boardIds)) {
            $command = new BoardCreateCommand();

            $this->commandBus->execute($command);

            return $this->redirectToRoute('board.index', ['id' => $command->getId()->toString()]);
        }

        return $this->render('board.html.twig', [
            'board' => 'Boards list',
        ]);
    }

    #[Route(
        '/create',
    )]
    public function create(): Response
    {
        $command = new BoardCreateCommand();

        $this->commandBus->execute($command);

        return $this->redirectToRoute('board.index', ['id' => $command->getId()->toString()]);
    }

    #[Route(
        '/{id}',
        methods: ['GET'],
        name: 'board.index'
    )]
    public function board(Request $request): Response
    {
        $board = $this->boardFinder->findById((string) $request->get('id'));

        if ($board === null) {
            return new Response('', 404);
        }

        if ($board->isReadOnly()) {
            $this->boardKeeper->addBoard($board->getBoard());
        } else {
            $this->boardKeeper->removeBoard($board->getBoard());
        }

        $response = new Response();

        $this->boardsCookieJar->addBoard(
            $board->isReadOnly()
                ? $board->getBoard()->getReadOnlyId()->toString()
                : $board->getBoard()->getId()->toString(),
            $response
        );

        return $this->render('board.html.twig', [
            'board' => $board->getBoard()->getTitle(),
        ], $response);
    }
}
