<?php

declare(strict_types=1);

namespace App\Module\Core\Application\Http\API\V1;

use App\Module\Core\Application\Http\API\V1\Request\SignUpRequest;
use App\Module\Core\Domain\Entity\User;
use App\Module\Core\Domain\Repository\UserRepository;
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
        private readonly UserRepository              $userRepository
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
        $user->setPassword($this->passwordHasher, $request->getPassword());

        $this->userRepository->save($user, true);

        return new Response($user->getUserIdentifier());
    }
}
