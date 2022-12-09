<?php

namespace App\Controller\Api;

use App\Dto\UserChangePasswordDto;
use App\Dto\UserUpdateDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractApiController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {}

    #[Route('/api/register', name: 'app_user_register', methods: [Request::METHOD_POST])]
    public function register(
        Request $request,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        /** @var User $user */
        $user = $serializer->deserialize(
            $request->getContent(),
            User::class,
            JsonEncoder::FORMAT
        );

        $errors = $this->formatValidationErrors($validator->validate(
            $user,
            null,
            ['Default', 'user:create']
        ));

        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => 'validation_failed',
                'errors' => $errors
            ], Response::HTTP_FORBIDDEN);
        }

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        );
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Route('/api/update-password', methods: [Request::METHOD_PATCH])]
    #[IsGranted(User::ROLE_USER)]
    public function changePassword(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        /** @var UserChangePasswordDto $passwordData */
        $passwordData = $serializer->deserialize(
            $request->getContent(),
            UserChangePasswordDto::class,
            JsonEncoder::FORMAT
        );
//        $this->denyAccessUnlessGranted('view', $post);

        $errors = $this->formatValidationErrors($validator->validate($passwordData));
        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => 'validation_failed',
                'errors' => $errors
            ], Response::HTTP_FORBIDDEN);
        }

        /** @var User $user */
        $user = $this->getUser();

        if (!$passwordHasher->isPasswordValid($user, $passwordData->currentPlainPassword)) {
            return new JsonResponse([
                'message' => 'incorrect_password',
                'errors' => 'The current password is not correct.'
            ], Response::HTTP_FORBIDDEN);
        }

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $passwordData->newPlainPassword
        );
        $user->setPassword($hashedPassword);

        $this->entityManager->flush();

        return new JsonResponse();
    }

    #[Route('/api/update-account', methods: [Request::METHOD_PATCH])]
    #[IsGranted(User::ROLE_USER)]
    public function updateUser(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse
    {
        /** @var UserUpdateDto $userData */
        $userData = $serializer->deserialize(
            $request->getContent(),
            UserUpdateDto::class,
            JsonEncoder::FORMAT
        );

        /** @var User $user */
        $user = $this->getUser();
        $user
            ->setEmail($userData->email)
            ->setUsername($userData->username)
            ->setBio($userData->bio);

        $errors = $this->formatValidationErrors($validator->validate(
            $user,
            null,
            ['Default', 'user:update']
        ));

        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => 'validation_failed',
                'errors' => $errors
            ], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->flush();

        return new JsonResponse();
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/users/{username}', methods: [Request::METHOD_GET])]
    public function getByUsername(User $user, NormalizerInterface $normalizer): JsonResponse
    {
        return $this->json($normalizer->normalize(
            $user,
            null,
            [AbstractNormalizer::GROUPS => 'user:read']
        ));
    }
}
