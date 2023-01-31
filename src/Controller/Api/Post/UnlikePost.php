<?php

namespace App\Controller\Api\Post;

use App\Entity\Post;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UnlikePost extends AbstractController
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
