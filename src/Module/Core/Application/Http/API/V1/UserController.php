<?php

declare(strict_types=1);

namespace App\Module\Core\Application\Http\API\V1;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
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
}