<?php

namespace App\Controller\Api;

use App\Dto\UserChangePasswordDto;
use App\Dto\UserUpdateDto;
use App\Entity\User;
use App\Form\User\UserUpdateAvatarType;
use App\Repository\UserRepository;
use App\Service\ImageResizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository
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

    #[Route('/api/update-avatar', methods: [Request::METHOD_POST])]
    public function updateAvatar(
        Request $request,
        ImageResizer $imageResizer
    ): JsonResponse
    {
//        $data = json_decode($request->getContent(), true);
        $data = [
            'avatar' => $request->files->get('avatar'),
        ];
        $form = $this->createForm(UserUpdateAvatarType::class);
        $form->submit($data);

        if ($form->isValid()) {
            /** @var UploadedFile $avatar */
            $avatar = $form->get('avatar')->getData();

            $newFilename = sprintf(
                '%s_%d.%s',
                uniqid(),
                (new \DateTime())->getTimestamp(),
                $avatar->guessExtension()
            );

            try {
                $finalFile = $avatar->move(
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

            $this->userRepository->save($user, true);

            if (null !== $previousAvatarFilename) {
                $filesystem = new Filesystem();

                try {
                    $filesystem->remove(sprintf('%s%s%s',
                        $this->getParameter('avatars_directory'),
                        DIRECTORY_SEPARATOR,
                        $previousAvatarFilename
                    ));
                } catch (IOException $exception) {
                    // TODO: log this message
                    dump($exception->getMessage());
                }
            }

            return new JsonResponse([
                'avatarFilename' => $user->getAvatarFilename()
            ]);
        }

        return self::getFormErrorsResponse($form);
    }

    private static function getErrorsFromForm(FormInterface $form): array
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = self::getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }

    private static function getFormErrorsResponse(FormInterface $form): JsonResponse
    {
        return new JsonResponse([
            'message' => 'validation_failed',
            'errors' => self::getErrorsFromForm($form)
        ], Response::HTTP_FORBIDDEN);
    }
}
