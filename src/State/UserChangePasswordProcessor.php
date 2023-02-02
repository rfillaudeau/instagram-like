<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\UserChangePasswordDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final readonly class UserChangePasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface   $passwordHasher,
        private EntityManagerInterface        $entityManager,
        private UserRepository                $userRepository,
        private AuthorizationCheckerInterface $authorizationChecker,
    )
    {
    }

    /**
     * @param mixed|UserChangePasswordDto $data
     * @throws NonUniqueResultException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        $user = $this->userRepository->findOneById($uriVariables['id']);
        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        if (!$this->authorizationChecker->isGranted(UserVoter::UPDATE_PASSWORD, $user)) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $data->currentPlainPassword)) {
            throw new AccessDeniedHttpException('Invalid current password.');
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data->newPlainPassword
        );
        $user->setPassword($hashedPassword);

        $this->entityManager->flush();

        return $user;
    }
}
