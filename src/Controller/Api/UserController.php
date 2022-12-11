<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/users', name: 'api_user_')]
class UserController extends AbstractApiController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/users/{username}', name: 'get_by_username', methods: [Request::METHOD_GET])]
    public function getByUsername(User $user, NormalizerInterface $normalizer): JsonResponse
    {
        return $this->json($normalizer->normalize(
            $user,
            null,
            [AbstractNormalizer::GROUPS => User::GROUP_READ]
        ));
    }
}
