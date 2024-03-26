<?php

declare(strict_types=1);

namespace App\Module\Core\Application\Http\API\V1;

use App\Module\Common\Application\Notificator\EmailNotificatorInterface;
use App\Module\Core\Application\Http\API\V1\Request\SignInRequest;
use App\Module\Core\Application\Http\API\V1\Request\SignUpRequest;
use App\Module\Core\Domain\Entity\User;
use App\Module\Core\Domain\Repository\UserRepository;
use App\Module\Core\Domain\Service\PasswordGeneratorInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository              $userRepository,
        private readonly PasswordGeneratorInterface  $passwordGenerator,
        private readonly EmailNotificatorInterface  $emailNotificator
    ) {}

    /**
     * Get info about user
     */
    #[Route(
        '/api/v1/user',
        methods: ['GET']
    )]
    #[OA\Tag(name: 'User')]
    public function get(): JsonResponse
    {
        return $this->json(['authorized' => $this->isGranted('IS_AUTHENTICATED_FULLY')]);
    }

    #[Route(
        '/api/v1/sign-up',
        name: 'signUp',
        methods: ['POST'],
    )]
    #[OA\Tag(name: 'User')]
    public function sighUp(SignUpRequest $request): Response
    {
        $user = new User($request->getEmail());
        $code = $this->passwordGenerator->generate();

        $user->setPassword($this->passwordHasher, $code);

        $this->userRepository->save($user);

        $this->emailNotificator->sendHtml(
            $request->getEmail(),
            'Auth to chekk',
            'emails/auth.html.twig',
            ['code' => $code]
        );

        return new Response($user->getUserIdentifier());
    }

    #[Route(
        '/api/v1/sign-in',
        name: 'signIn',
        methods: ['POST'],
    )]
    #[OA\Tag(name: 'User')]
    public function sighIn(SignInRequest $request): Response
    {
        $user = $this->userRepository->findByEmail($request->getEmail());
        $code = $this->passwordGenerator->generate();

        if (null === $user) {
            $user = new User($request->getEmail());
            $user->setPassword($this->passwordHasher, $code);
        }

        $this->userRepository->save($user);

        $this->emailNotificator->sendHtml(
            $request->getEmail(),
            'Auth to chekk',
            'emails/auth.html.twig',
            ['code' => $code]
        );

        return new Response($user->getUserIdentifier());
    }
}
