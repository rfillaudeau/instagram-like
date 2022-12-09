<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SecurityController extends AbstractApiController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/login', name: 'app_login', methods: ['POST'])]
    public function login(NormalizerInterface $normalizer): JsonResponse
    {
        /** @var null|User $user */
        $user = $this->getUser();
        if (null === $this->getUser()) {
            return $this->json([
                'error' => 'Invalid credentials'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($normalizer->normalize($user, null, [AbstractNormalizer::GROUPS => 'user:read']));
    }

    #[Route(path: '/sign-out', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
