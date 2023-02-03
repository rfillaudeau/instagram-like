<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use App\Service\ImageResizer;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ImageResizer    $imageResizer,
        private string          $avatarsDirectory,
        private LoggerInterface $logger,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['handleAvatar', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function handleAvatar(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        if (
            !($user instanceof User)
            || null === $user->getBase64Avatar()
            || Request::METHOD_PUT !== $event->getRequest()->getMethod()
        ) {
            return;
        }

        $newFile = $this->saveAndResizeNewAvatar($user->getBase64Avatar());
        if (null !== $newFile) {
            // Remove the old file
            $this->removeAvatarFile($user->getAvatarFilename());

            $user->setAvatarFilename($newFile->getFilename());
        }
    }

    // TODO: Refacto > Too similar to post upload
    private function saveAndResizeNewAvatar(?File $avatar): ?File
    {
        if (null === $avatar) {
            return null;
        }

        $newFilename = sprintf(
            '%s-%d.%s',
            uniqid(),
            (new DateTime())->getTimestamp(),
            $avatar->guessExtension()
        );

        try {
            $finalFile = $avatar->move(
                $this->avatarsDirectory,
                $newFilename
            );
        } catch (FileException $exception) {
            $this->logger->error($exception->getMessage(), [
                'trace' => $exception->getTraceAsString()
            ]);

            return null;
        }

        $this->imageResizer->resizeUserAvatar($finalFile->getPathname());

        return $finalFile;
    }

    private function removeAvatarFile(?string $filename): void
    {
        if (null === $filename) {
            return;
        }

        $filesystem = new Filesystem();

        try {
            $filesystem->remove(sprintf(
                '%s/%s',
                $this->avatarsDirectory,
                $filename
            ));
        } catch (IOException $exception) {
            $this->logger->error($exception->getMessage(), [
                'trace' => $exception->getTraceAsString()
            ]);
        }
    }
}
