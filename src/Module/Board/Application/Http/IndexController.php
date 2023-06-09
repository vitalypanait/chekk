<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http;

use App\Module\Board\Application\Service\BoardFinder;
use App\Module\Board\Application\Service\BoardsCookieJar;
use App\Module\Board\Application\UseCase\BoardCreate\BoardCreateCommand;
use App\Module\Board\Application\UseCase\BoardReadOnlyCreate\BoardReadOnlyCreateCommand;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Service\ReadOnlyBoardKeeper;
use App\Module\Common\Bus\CommandBus;
use App\Module\Core\Domain\Entity\User;
use App\Module\Core\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly CommandBus                $commandBus,
        private readonly BoardFinder               $boardFinder,
        private readonly ReadOnlyBoardKeeper       $boardKeeper,
        private readonly BoardsCookieJar           $boardsCookieJar,
        private readonly BoardRepository           $boardRepository,
        private readonly UserRepository            $userRepository,
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
    ) {}

    #[Route(
        '/',
        name: 'home'
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
            'title' => 'Boards list',
        ]);
    }

    #[Route(
        '/auth',
        name: 'auth'
    )]
    public function auth(Request $request): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('home');
        }

        if ($request->isMethod('POST')) {
            $email = json_decode($request->getContent(), true)['email'];
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if ($user === null) {
                $user = new User($email);
                $this->userRepository->save($user);
            }

            $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);

            return $this->json([
                'link' => $loginLinkDetails->getUrl()
            ]);
        }

        return $this->render('board.html.twig', ['title' => 'Auth']);
    }

    #[Route('/login', name: 'login')]
    public function login(Request $request): Response
    {
        return $this->redirectToRoute('home');
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
        $board = $this->boardRepository->findById((string)$request->get('id'));

        if ($board !== null && !$board->hasReadOnly()) {
            $this->commandBus->execute(new BoardReadOnlyCreateCommand($board->getId()->toString()));
        }

        $board = $this->boardFinder->findById((string)$request->get('id'));

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
            'title' => $board->getBoard()->getTitle(),
        ], $response);
    }
}
