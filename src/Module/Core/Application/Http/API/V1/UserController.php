<?php

declare(strict_types=1);

namespace App\Module\Core\Application\Http\API\V1;

use App\Module\Common\Application\Notificator\EmailNotificatorInterface;
use App\Module\Core\Application\Http\API\V1\Request\SignInRequest;
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
        '/api/v1/sign-in',
        name: 'signIn',
        methods: ['POST'],
    )]
    #[OA\Tag(name: 'User')]
    public function sighIn(SignInRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->getEmail());
        $code = $this->passwordGenerator->generate();

        if (null === $user) {
            $user = new User($request->getEmail());
        }

        if (!$this->isUpdatePasswordAvailable($user)) {
            return $this->json([
                'error' => [
                    [
                        'name' => 'code',
                        'message' => 'declined_update_code',
                        'context' => [
                            'updatedAt' => $user->getPasswordUpdatedAt()->format(\DateTimeInterface::ATOM)
                        ],
                    ]
                ]
            ]);
        }

        $user->setPassword($this->passwordHasher, $code);
        $this->userRepository->save($user);

        $this->emailNotificator->sendHtml(
            $request->getEmail(),
            'Auth to chekk',
            'emails/auth.html.twig',
            ['code' => $code]
        );

        return $this->json(['email' => $user->getUserIdentifier()]);
    }

    private function isUpdatePasswordAvailable(User $user): bool
    {
        if (null === $user->getPasswordUpdatedAt()) {
            return true;
        }

        return (new \DateTime())->getTimestamp() - $user->getPasswordUpdatedAt()->getTimestamp() > 30;
    }
}
