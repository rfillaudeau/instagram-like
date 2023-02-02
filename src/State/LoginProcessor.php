<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\LoginDto;
use App\Entity\AccessToken;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class LoginProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface      $entityManager,
    )
    {
    }

    /**
     * @param mixed|LoginDto $data
     * @throws NonUniqueResultException
     */
    public function process(
        mixed     $data,
        Operation $operation,
        array     $uriVariables = [],
        array     $context = []
    ): AccessToken
    {
        $user = $this->userRepository->findOneByEmail($data->email);
        if (null === $user || !$this->passwordHasher->isPasswordValid($user, $data->password)) {
            throw new BadRequestHttpException('Invalid credentials');
        }

        $accessToken = (new AccessToken())
            ->setUser($user);

        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();

        return $accessToken;
    }
}
