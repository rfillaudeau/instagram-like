<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractApiController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/login', name: 'app_login', methods: [Request::METHOD_POST])]
    public function login(NormalizerInterface $normalizer): JsonResponse
    {
        /** @var null|User $user */
        $user = $this->getUser();
        if (null === $this->getUser()) {
            return $this->json([
                'error' => 'Invalid credentials'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(
            $normalizer->normalize($user, null, [AbstractNormalizer::GROUPS => User::GROUP_READ])
        );
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
        /** @var User $user */
        $user = $serializer->deserialize(
            $request->getContent(),
            User::class,
            JsonEncoder::FORMAT
        );

        $errors = $this->formatValidationErrors($validator->validate(
            $user,
            null,
            [User::GROUP_DEFAULT, User::GROUP_CREATE]
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

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Route(path: '/sign-out', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
