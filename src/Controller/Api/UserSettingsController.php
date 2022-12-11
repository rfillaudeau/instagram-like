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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted(User::ROLE_USER)]
class UserSettingsController extends AbstractApiController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager
    )
    {}

    #[Route('/api/update-password', methods: [Request::METHOD_PATCH])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        /** @var UserChangePasswordDto $passwordData */
        $passwordData = $this->serializer->deserialize(
            $request->getContent(),
            UserChangePasswordDto::class,
            JsonEncoder::FORMAT
        );

        $errors = $this->formatValidationErrors($this->validator->validate($passwordData));
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
    public function updateUser(Request $request): JsonResponse
    {
        /** @var UserUpdateDto $userData */
        $userData = $this->serializer->deserialize(
            $request->getContent(),
            UserUpdateDto::class,
            JsonEncoder::FORMAT
        );

        $user = $this->getUser();
        $user
            ->setEmail($userData->email)
            ->setUsername($userData->username)
            ->setBio($userData->bio);

        $errors = $this->formatValidationErrors($this->validator->validate(
            $user,
            null,
            [User::GROUP_DEFAULT, User::GROUP_UPDATE]
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
}
