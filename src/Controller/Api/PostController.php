<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Security\PostVoter;
use App\Service\ImageResizer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/posts', name: 'api_post_')]
class PostController extends AbstractApiController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly PostRepository $postRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ImageResizer $imageResizer,
        private readonly string $postsDirectory,
        private readonly LoggerInterface $logger
    )
    {}

    #[Route('/feed', name: 'feed', methods: [Request::METHOD_GET])]
    #[IsGranted(User::ROLE_USER)]
    public function feed(Request $request): JsonResponse
    {
        list($firstResult, $maxResults) = self::getPagination($request);

        $posts = $this->postRepository->findByFollowing($this->getUser(), $firstResult, $maxResults);

        return new JsonResponse($this->serializePosts($posts), Response::HTTP_OK, [], true);
    }

    #[Route('/discover', name: 'discover', methods: [Request::METHOD_GET])]
    public function discover(Request $request): JsonResponse
    {
        list($firstResult, $maxResults) = self::getPagination($request);

        $posts = $this->postRepository->findByLatest($firstResult, $maxResults);

        return new JsonResponse($this->serializePosts($posts), Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'read', requirements: ['id' => '\d+'], methods: [Request::METHOD_GET])]
    public function read(Post $post): JsonResponse
    {
        return new JsonResponse($this->serializePosts($post), Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'create', methods: [Request::METHOD_POST])]
    #[IsGranted(User::ROLE_USER)]
    public function create(Request $request, UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUser();

        $post = (new Post())
            ->setUser($user)
            ->setDescription($request->get('description', ''))
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $file = $this->handleNewPostPicture($request->files->get('picture'));

        $post->setPictureFilename(null !== $file ? $file->getFilename() : '');

        $errors = $this->formatValidationErrors($this->validator->validate($post));
        if (count($errors) > 0) {
            if (null !== $file) {
                $filesystem = new Filesystem();
                $filesystem->remove($file->getPathname());
            }

            return new JsonResponse([
                'message' => 'validation_failed',
                'errors' => $errors
            ], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $userRepository->incrementPostCount($user);

        // Refresh the user postCount
        $this->entityManager->refresh($user);

        return new JsonResponse($this->serializePosts($post), Response::HTTP_CREATED, [], true);
    }

    /**
     * Using POST method instead of PUT/PATCH to be able to receive files
     */
    #[Route('/{id}', name: 'update', requirements: ['id' => '\d+'], methods: [Request::METHOD_POST])]
    #[IsGranted(User::ROLE_USER)]
    public function update(Request $request, Post $post): JsonResponse
    {
        $this->denyAccessUnlessGranted(PostVoter::UPDATE, $post);

        $previousFilename = $post->getPictureFilename();

        $post->setDescription($request->get('description', ''));

        $file = $this->handleNewPostPicture($request->files->get('picture'));
        if (null !== $file) {
            $post->setPictureFilename($file->getFilename());
        }

        $errors = $this->formatValidationErrors($this->validator->validate($post));
        if (count($errors) > 0) {
            if (null !== $file) {
                $filesystem = new Filesystem();
                $filesystem->remove($file->getPathname());
            }

            return new JsonResponse([
                'message' => 'validation_failed',
                'errors' => $errors
            ], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->flush();

        if (null !== $file) {
            $this->removePostFile($previousFilename);
        }

        return new JsonResponse($this->serializePosts($post), Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: [Request::METHOD_DELETE])]
    #[IsGranted(User::ROLE_USER)]
    public function delete(Post $post, UserRepository $userRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted(PostVoter::UPDATE, $post);

        $pictureFilename = $post->getPictureFilename();

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        $userRepository->decrementPostCount($this->getUser());

        $this->removePostFile($pictureFilename);

        return new JsonResponse();
    }

    private function handleNewPostPicture(?UploadedFile $picture): ?File
    {
        if (null === $picture) {
            return null;
        }

        $newFilename = sprintf(
            '%s-%d.%s',
            uniqid(),
            (new \DateTime())->getTimestamp(),
            $picture->guessExtension()
        );

        try {
            $finalFile = $picture->move(
                $this->postsDirectory,
                $newFilename
            );
        } catch (FileException $e) {
            // TODO: Log the error

            return null;
        }

        $this->imageResizer->resizePostPicture($finalFile->getPathname());

        return $finalFile;
    }

    private function removePostFile(?string $filename): void
    {
        if (null === $filename) {
            return;
        }

        $filesystem = new Filesystem();

        try {
            $filesystem->remove(sprintf(
                '%s/%s',
                $this->postsDirectory,
                $filename
            ));
        } catch (IOException $exception) {
            $this->logger->error($exception->getMessage(), [
                'trace' => $exception->getTraceAsString()
            ]);
        }
    }

    private function serializePosts(array|Post $posts): string
    {
        return $this->serializer->serialize(
            $posts,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => [Post::GROUP_READ, User::GROUP_READ]]
        );
    }
}
