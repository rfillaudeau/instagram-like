<?php

namespace App\Controller\Api\Like;

use App\Entity\Like;
use App\Entity\Post;
use App\Repository\LikeRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class DeleteLike extends AbstractController
{
    public function __construct(private readonly LikeRepository $likeRepository)
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(Post $post): ?Like
    {
        $like = $this->likeRepository->findOneByUserAndPost($this->getUser(), $post);
        if (null === $like) {
            throw new NotFoundHttpException('Like not found.');
        }

        return $like;
    }
}
