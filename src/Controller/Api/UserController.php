<?php

namespace App\Controller\Api;

use App\Entity\Follow;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\FollowRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/users', name: 'api_user_')]
class UserController extends AbstractApiController
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/me', name: 'get_me', methods: [Request::METHOD_GET])]
    #[IsGranted(User::ROLE_USER)]
    public function getMe(NormalizerInterface $normalizer): JsonResponse
    {
        return $this->json($normalizer->normalize(
            $this->getUser(),
            null,
            [AbstractNormalizer::GROUPS => User::GROUP_READ]
        ));
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/{username}/follow', name: 'follow', methods: [Request::METHOD_POST])]
    #[IsGranted(User::ROLE_USER)]
    public function follow(
        User                   $user,
        FollowRepository       $followRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $loggedUser = $this->getUser();
        if ($user === $loggedUser) {
            return new JsonResponse(
                'Unable to follow yourself.',
                Response::HTTP_FORBIDDEN
            );
        }

        $follow = $followRepository->findOneByUserAndFollowing($loggedUser, $user);
        if (null !== $follow) {
            return new JsonResponse(
                'Already following this user.',
                Response::HTTP_FORBIDDEN
            );
        }

        $follow = (new Follow())
            ->setUser($loggedUser)
            ->setFollowing($user);

        $entityManager->persist($follow);
        $entityManager->flush();

        $this->userRepository->incrementFollowerCount($user);
        $this->userRepository->incrementFollowingCount($loggedUser);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/{username}/follow', name: 'unfollow', methods: [Request::METHOD_DELETE])]
    #[IsGranted(User::ROLE_USER)]
    public function unfollow(
        User                   $user,
        FollowRepository       $followRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $loggedUser = $this->getUser();
        if ($user === $loggedUser) {
            return new JsonResponse(
                'Unable to unfollow yourself.',
                Response::HTTP_FORBIDDEN
            );
        }

        $follow = $followRepository->findOneByUserAndFollowing($loggedUser, $user);
        if (null === $follow) {
            return new JsonResponse(
                'Unable to unfollow an user you are not following.',
                Response::HTTP_FORBIDDEN
            );
        }

        $entityManager->remove($follow);
        $entityManager->flush();

        $this->userRepository->decrementFollowerCount($user);
        $this->userRepository->decrementFollowingCount($loggedUser);

        return new JsonResponse(null, Response::HTTP_OK);
    }

    #[Route('/{username}/posts', name: 'posts', methods: [Request::METHOD_GET])]
    public function getPosts(
        Request             $request,
        User                $user,
        PostRepository      $postRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        list($firstResult, $maxResults) = self::getPagination($request);

        $posts = $postRepository->findByUser($user, $firstResult, $maxResults);

        $jsonPosts = $serializer->serialize(
            $posts,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => [Post::GROUP_READ, User::GROUP_READ]]
        );

        return new JsonResponse($jsonPosts, Response::HTTP_OK, [], true);
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
