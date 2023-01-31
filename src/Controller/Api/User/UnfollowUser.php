<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Repository\FollowRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsController]
class UnfollowUser extends AbstractController
{
    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly FollowRepository       $followRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(User $user): JsonResponse
    {
        /** @var User $loggedUser */
        $loggedUser = $this->getUser();
        if ($user === $loggedUser) {
            throw new HttpException(
                Response::HTTP_FORBIDDEN,
                'Unable to unfollow yourself.'
            );
        }

        $follow = $this->followRepository->findOneByUserAndFollowing($loggedUser, $user);
        if (null === $follow) {
            throw new HttpException(
                Response::HTTP_FORBIDDEN,
                'Unable to unfollow an user you are not following.'
            );
        }

        $this->entityManager->remove($follow);
        $this->entityManager->flush();

        $this->userRepository->decrementFollowerCount($user);
        $this->userRepository->decrementFollowingCount($loggedUser);

        return new JsonResponse(null, Response::HTTP_OK);
    }
}
