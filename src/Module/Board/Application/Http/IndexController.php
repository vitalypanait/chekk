<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http;

use App\Module\Board\Application\Service\BoardFinder;
use App\Module\Board\Application\UseCase\BoardCreate\BoardCreateCommand;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Service\ReadOnlyBoardKeeper;
use App\Module\Common\Bus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly CommandBus  $commandBus,
        private readonly BoardFinder $boardFinder,
        private readonly ReadOnlyBoardKeeper $boardKeeper
    ) {}

    #[Route(
        '/',
    )]
    public function index(): Response
    {
        $command = new BoardCreateCommand();

        $this->commandBus->execute($command);

        return new RedirectResponse('/' . $command->getId()->toString());
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

        return $this->render('board.html.twig', [
            'board' => $board->getBoard()->getTitle(),
        ]);
    }
}