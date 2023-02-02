<?php

namespace App\Controller\Api\Follow;

use App\Entity\Follow;
use App\Entity\User;
use App\Repository\FollowRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class DeleteFollow extends AbstractController
{
    public function __construct(private readonly FollowRepository $followRepository)
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(User $user): ?Follow
    {
        $follow = $this->followRepository->findOneByUserAndFollowing($this->getUser(), $user);
        if (null === $follow) {
            throw new NotFoundHttpException('Follow not found.');
        }

        return $follow;
    }
}
