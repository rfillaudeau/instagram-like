<?php

namespace App\Controller\Api;

use App\Dto\UserUpdatePasswordDto;
use App\Dto\UserDto;
use App\Entity\User;
use App\Service\ImageResizer;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted(User::ROLE_USER)]
class UserSettingsController extends AbstractApiController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    )
    {}

    #[Route('/api/update-password', methods: [Request::METHOD_PATCH])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        /** @var UserUpdatePasswordDto $passwordData */
        $passwordData = $this->serializer->deserialize(
            $request->getContent(),
            UserUpdatePasswordDto::class,
            JsonEncoder::FORMAT
        );

        $errors = $this->validator->validate($passwordData);
        if (count($errors) > 0) {
            throw new ValidationFailedException($passwordData, $errors);
        }

        $user = $this->getUser();

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
        /** @var UserDto $userDto */
        $userDto = $this->serializer->deserialize(
            $request->getContent(),
            UserDto::class,
            JsonEncoder::FORMAT
        );

        $errors = $this->validator->validate($userDto);
        if (count($errors) > 0) {
            throw new ValidationFailedException($userDto, $errors);
        }

        // Update the user after validating the data in order to avoid messing up the session data
        $user = $this->getUser();

        $previousEmail = $user->getEmail();
        $previousUsername = $user->getUsername();

        $user
            ->setEmail($userDto->email)
            ->setUsername($userDto->username)
            ->setBio($userDto->bio);

        // TODO: Try to move this validation in UserDto
        // Validate the user a second time to trigger the UniqueEntity validation
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            // Fix to avoid being logged out if the validation fails
            $user
                ->setEmail($previousEmail)
                ->setUsername($previousUsername);

            throw new ValidationFailedException($user, $errors);
        }

        $this->entityManager->flush();

        return $this->json($user, Response::HTTP_OK, [], [
            AbstractNormalizer::GROUPS => User::GROUP_READ
        ]);
    }

    #[Route('/api/update-avatar', methods: [Request::METHOD_POST])]
    public function updateAvatar(
        Request $request,
        ImageResizer $imageResizer
    ): JsonResponse
    {
        $userDto = new UserDto();
        $userDto->avatar = $request->files->get('avatar');

        $errors = $this->validator->validate($userDto, null, UserDto::GROUP_UPDATE_AVATAR);
        if (count($errors) > 0) {
            throw new ValidationFailedException($userDto, $errors);
        }

        $newFilename = sprintf(
            '%s_%d.%s',
            uniqid(),
            (new DateTime())->getTimestamp(),
            $userDto->avatar->guessExtension()
        );

        try {
            $finalFile = $userDto->avatar->move(
                $this->getParameter('avatars_directory'),
                $newFilename
            );

            $imageResizer->resizeUserAvatar($finalFile->getPathname());
        } catch (FileException $e) {
            return new JsonResponse([
                'message' => 'Unable to save the file',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = $this->getUser();
        $previousAvatarFilename = $user->getAvatarFilename();

        $user->setAvatarFilename($newFilename);

        $this->entityManager->flush();

        if (null !== $previousAvatarFilename) {
            $filesystem = new Filesystem();

            try {
                $filesystem->remove(sprintf('%s/%s',
                    $this->getParameter('avatars_directory'),
                    $previousAvatarFilename
                ));
            } catch (IOException $exception) {
                $this->logger->error($exception->getMessage(), [
                    'trace' => $exception->getTraceAsString()
                ]);
            }
        }

        return $this->json($user, Response::HTTP_OK, [], [
            AbstractNormalizer::GROUPS => User::GROUP_READ
        ]);
    }
}
