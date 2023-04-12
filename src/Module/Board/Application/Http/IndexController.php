<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http;

use App\Module\Board\Application\UseCase\BoardCreate\BoardCreateCommand;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Common\Bus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly BoardRepository $boardRepository
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
    )]
    public function board(Request $request): Response
    {
        $board = $this->boardRepository->findById((string) $request->get('id'));

        if ($board === null) {
            return new Response('', 404);
        }

        return $this->render('board.html.twig', [
            'board' => $board->getTitle(),
        ]);
    }
}