<?php

namespace App\Controller\Api\Post;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class LikePost extends AbstractController
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
    public function __invoke(Post $post): JsonResponse
    {
        /** @var User|null $user */
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
}
