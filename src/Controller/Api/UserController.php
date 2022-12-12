<?php

namespace App\Controller\Api;

use App\Entity\Follow;
use App\Entity\User;
use App\Repository\FollowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/users', name: 'api_user_')]
class UserController extends AbstractApiController
{
    #[Route('/{username}/follow', name: 'follow', methods: [Request::METHOD_POST])]
    #[IsGranted(User::ROLE_USER)]
    public function follow(
        User $user,
        FollowRepository $followRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $loggedUser = $this->getUser();

        if ($user === $loggedUser) {
            return new JsonResponse('Unable to follow yourself.', Response::HTTP_FORBIDDEN);
        }

        $follow = $followRepository->findOneByUserAndFollowing($loggedUser, $user);
        if (null !== $follow) {
            return new JsonResponse('Already following this user.', Response::HTTP_FORBIDDEN);
        }

        $follow = (new Follow())
            ->setUser($loggedUser)
            ->setFollowing($user);

        $entityManager->persist($follow);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{username}', name: 'get_by_username', methods: [Request::METHOD_GET])]
    public function getByUsername(User $user, NormalizerInterface $normalizer): JsonResponse
    {
        return $this->json($normalizer->normalize(
            $user,
            null,
            [AbstractNormalizer::GROUPS => User::GROUP_READ]
        ));
    }
}
