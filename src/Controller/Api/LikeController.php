<?php

namespace App\Controller\Api;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LikeController extends AbstractApiController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LikeRepository         $likeRepository,
        private readonly PostRepository         $postRepository,
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route(
        '/api/posts/{id}/like',
        name: 'api_post_like',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_POST])
    ]
    #[IsGranted(User::ROLE_USER)]
    public function like(Post $post): JsonResponse
    {
        $user = $this->getUser();

        if (null !== $this->likeRepository->findOneByUserAndPost($user, $post)) {
            return new JsonResponse(null, Response::HTTP_OK);
        }

        $like = (new Like())
            ->setPost($post)
            ->setUser($user);

        $this->entityManager->persist($like);
        $this->entityManager->flush();

        $this->postRepository->incrementLikeCount($post);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route(
        '/api/posts/{id}/like',
        name: 'api_post_unlike',
        requirements: ['id' => '\d+'],
        methods: [Request::METHOD_DELETE])
    ]
    #[IsGranted(User::ROLE_USER)]
    public function unlike(Post $post): JsonResponse
    {
        $like = $this->likeRepository->findOneByUserAndPost($this->getUser(), $post);
        if (null === $like) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($like);
        $this->entityManager->flush();

        $this->postRepository->decrementLikeCount($post);

        return new JsonResponse(null, Response::HTTP_OK);
    }
}
