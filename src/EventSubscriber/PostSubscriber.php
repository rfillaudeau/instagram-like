<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Post;
use App\Repository\UserRepository;
use App\Service\ImageResizer;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class PostSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ImageResizer           $imageResizer,
        private string                 $postsDirectory,
        private EntityManagerInterface $entityManager,
        private UserRepository         $userRepository,
        private LoggerInterface        $logger,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['handlePicture', EventPriorities::PRE_WRITE],
                ['handleUserPostCount', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function handlePicture(ViewEvent $event): void
    {
        $post = $event->getControllerResult();
        if (!($post instanceof Post) || null === $post->getBase64Picture()) {
            return;
        }

        switch ($event->getRequest()->getMethod()) {
            case Request::METHOD_POST:
            case Request::METHOD_PUT:
            case Request::METHOD_PATCH:
                $newFile = $this->saveAndResizeNewPostPicture($post->getBase64Picture());
                if (null !== $newFile) {
                    // Remove the old file
                    $this->removePostFile($post->getPictureFilename());

                    $post->setPictureFilename($newFile->getFilename());
                }
                break;

            case Request::METHOD_DELETE:
                $this->removePostFile($post->getPictureFilename());
                break;
        }
    }

    private function saveAndResizeNewPostPicture(?File $picture): ?File
    {
        if (null === $picture) {
            return null;
        }

        $newFilename = sprintf(
            '%s-%d.%s',
            uniqid(),
            (new DateTime())->getTimestamp(),
            $picture->guessExtension()
        );

        try {
            $finalFile = $picture->move(
                $this->postsDirectory,
                $newFilename
            );
        } catch (FileException $exception) {
            $this->logger->error($exception->getMessage(), [
                'trace' => $exception->getTraceAsString()
            ]);

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

    public function handleUserPostCount(ViewEvent $event)
    {
        $post = $event->getControllerResult();
        if (!($post instanceof Post)) {
            return;
        }

        switch ($event->getRequest()->getMethod()) {
            case Request::METHOD_POST:
                $this->userRepository->incrementPostCount($post->getUser());
                break;

            case Request::METHOD_DELETE:
                $this->userRepository->decrementPostCount($post->getUser());
                break;
        }

        // Refresh the user postCount
        $this->entityManager->refresh($post->getUser());
    }
}
