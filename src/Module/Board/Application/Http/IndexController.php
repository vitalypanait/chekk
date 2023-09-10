<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http;

use App\Module\Board\Application\Service\BoardAccessManagerInterface;
use App\Module\Board\Application\Service\BoardsCookieJar;
use App\Module\Board\Application\UseCase\BoardCreate\BoardCreateCommand;
use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Common\Bus\CommandBus;
use App\Module\Core\Domain\Entity\User;
use App\Module\Core\Domain\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly CommandBus                $commandBus,
        private readonly BoardIdRepository         $boardIdRepository,
        private readonly BoardsCookieJar           $boardsCookieJar,
        private readonly UserRepository            $userRepository,
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
        private readonly BoardAccessManagerInterface $boardAccessManager,
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

            $this->notificator->send(
                $user->getUserIdentifier(),
                'Auth link',
                $loginLinkDetails->getUrl()
            );

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

    #[Route('/logout', name: 'logout')]
    public function logout(Security $security): Response
    {
        $response = new RedirectResponse($this->generateUrl('home'), 302);

        $security->logout(false);

        $this->boardAccessManager->clear();

        $this->boardsCookieJar->clear($response);

        return $response;
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
        $boardId = $this->boardIdRepository->findById((string) $request->get('id'));

        if ($boardId === null) {
            return new Response('', 404);
        }

        $response = new Response();

        return $this->render('board.html.twig', [
            'title' => $boardId->getBoard()->getTitle(),
        ], $response);
    }
}
