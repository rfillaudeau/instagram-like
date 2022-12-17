<?php

namespace App\Controller\Api;

use App\Dto\UserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractApiController
{
    #[Route(path: '/api/login', name: 'app_login', methods: [Request::METHOD_POST])]
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        if (null === $this->getUser()) {
            return $this->json([
                'error' => 'Invalid credentials'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($user, Response::HTTP_OK, [], [
            AbstractNormalizer::GROUPS => User::GROUP_READ
        ]);
    }

    #[Route('/api/register', name: 'app_user_register', methods: [Request::METHOD_POST])]
    public function register(
        Request $request,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        /** @var UserDto $userDto */
        $userDto = $serializer->deserialize(
            $request->getContent(),
            UserDto::class,
            JsonEncoder::FORMAT
        );

        $errors = $validator->validate(
            $userDto,
            null,
            [UserDto::GROUP_DEFAULT, UserDto::GROUP_CREATE]
        );

        if (count($errors) > 0) {
            throw new ValidationFailedException($userDto, $errors);
        }

        $user = (new User())
            ->setEmail($userDto->email)
            ->setUsername($userDto->username);

        // TODO: Try to move this validation in UserDto
        // Validate the user a second time to trigger the UniqueEntity validation
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new ValidationFailedException($user, $errors);
        }

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $userDto->plainPassword
        );
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, Response::HTTP_CREATED, [], [
            AbstractNormalizer::GROUPS => User::GROUP_READ
        ]);
    }

    #[Route(path: '/sign-out', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
