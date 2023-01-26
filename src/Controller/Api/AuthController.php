<?php

namespace App\Controller\Api;

use App\Dto\LoginDto;
use App\Dto\UserDto;
use App\Entity\AccessToken;
use App\Entity\User;
use App\Repository\AccessTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/api/auth')]
class AuthController extends AbstractApiController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route(path: '/token', name: 'app_login', methods: [Request::METHOD_POST])]
    public function token(
        Request                     $request,
        SerializerInterface         $serializer,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository              $userRepository,
        EntityManagerInterface      $entityManager
    ): JsonResponse
    {
        /** @var LoginDto $loginDto */
        $loginDto = $serializer->deserialize(
            $request->getContent(),
            LoginDto::class,
            JsonEncoder::FORMAT
        );

        $user = $userRepository->findOneByEmail($loginDto->email);
        if (null === $user || !$passwordHasher->isPasswordValid($user, $loginDto->password)) {
            return $this->json([
                'error' => 'Invalid credentials'
            ], Response::HTTP_BAD_REQUEST);
        }

        $accessToken = new AccessToken($user);

        $entityManager->persist($accessToken);
        $entityManager->flush();

        return $this->json($accessToken, Response::HTTP_OK, [], [
            AbstractNormalizer::GROUPS => AccessToken::GROUP_READ
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route(path: '/revoke', name: 'app_logout', methods: [Request::METHOD_POST])]
    public function revoke(
        Request                $request,
        AccessTokenRepository  $accessTokenRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $authorization = $request->headers->get('authorization');
        if (null === $authorization) {
            throw new AccessDeniedHttpException('Authorization not found.');
        }

        $accessToken = $accessTokenRepository->findOneByToken(
            str_replace('Bearer ', '', $authorization)
        );
        if (null === $accessToken) {
            throw new AccessDeniedHttpException('Access Token not found.');
        }

        $entityManager->remove($accessToken);
        $entityManager->flush();

        return new JsonResponse();
    }

    #[Route('/register', name: 'app_user_register', methods: [Request::METHOD_POST])]
    public function register(
        Request                     $request,
        ValidatorInterface          $validator,
        SerializerInterface         $serializer,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $entityManager
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
}
