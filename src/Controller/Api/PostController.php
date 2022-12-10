<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\ImageResizer;
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
        private readonly PostRepository $postRepository
    )
    {}

    #[Route('/feed', name: 'feed', methods: [Request::METHOD_GET])]
    #[IsGranted(User::ROLE_USER)]
    public function feed(Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        if ($page <= 0) {
            $page = 1;
        }

        $maxResults = 2;
        $firstResult = ($page - 1) * $maxResults;
        dump($firstResult);

        $posts = $this->postRepository->findByFollowing($this->getUser(), $firstResult, $maxResults);

        $jsonPosts = $this->serializer->serialize(
            $posts,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => [Post::GROUP_READ, User::GROUP_READ]]
        );

        return new JsonResponse($jsonPosts, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'read', requirements: ['page' => '\d+'], methods: [Request::METHOD_GET])]
    public function read(Post $post): JsonResponse
    {
        $jsonPost = $this->serializer->serialize(
            $post,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => [Post::GROUP_READ, User::GROUP_READ]]
        );

        return new JsonResponse($jsonPost, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'create', methods: [Request::METHOD_POST])]
    #[IsGranted(User::ROLE_USER)]
    public function create(
        Request $request,
        ImageResizer $imageResizer,
        UserRepository $userRepository
    ): JsonResponse
    {
        /** @var UploadedFile $picture */
        $picture = $request->files->get('picture');

        $user = $this->getUser();

        $post = (new Post())
            ->setUser($user)
            ->setDescription($request->get('description', ''))
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        /** @var File $finalFile */
        $finalFile = null;
        if (null !== $picture) {
            $newFilename = sprintf('%s.%s', uniqid(), $picture->guessExtension());

            try {
                $finalFile = $picture->move(
                    $this->getParameter('posts_directory'),
                    $newFilename
                );

                $imageResizer->resizePostPicture($finalFile->getPathname());
            } catch (FileException $e) {
                return new JsonResponse([
                    'message' => 'Unable to save the file',
                    'error' => $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $post->setPictureFilename($newFilename);
        }

        $errors = $this->formatValidationErrors($this->validator->validate($post));
        if (count($errors) > 0) {
            if (null !== $finalFile) {
                $filesystem = new Filesystem();
                $filesystem->remove($finalFile->getPathname());
            }

            return new JsonResponse([
                'message' => 'validation_failed',
                'errors' => $errors
            ], Response::HTTP_FORBIDDEN);
        }

        $this->postRepository->save($post, true);

        $userRepository->incrementPostCount($user);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', requirements: ['page' => '\d+'], methods: [Request::METHOD_PUT])]
    #[IsGranted(User::ROLE_USER)]
    public function update(Post $post): JsonResponse
    {
        return new JsonResponse();
    }

    #[Route('/{id}', name: 'delete', requirements: ['page' => '\d+'], methods: [Request::METHOD_DELETE])]
    #[IsGranted(User::ROLE_USER)]
    public function delete()
    {

    }

    public function list()
    {

    }
}
