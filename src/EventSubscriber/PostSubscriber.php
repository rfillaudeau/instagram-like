<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ImageResizer;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class PostSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security               $security,
        private ImageResizer           $imageResizer,
        private string                 $postsDirectory,
        private EntityManagerInterface $entityManager,
        private UserRepository         $userRepository,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['setUser', EventPriorities::PRE_VALIDATE],
                ['handlePicture', EventPriorities::PRE_WRITE],
                ['afterWrite', EventPriorities::POST_WRITE],
            ],
        ];
    }

    public function setUser(ViewEvent $event): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();
        $post = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!($post instanceof Post) || Request::METHOD_POST !== $method || null === $user) {
            return;
        }

        $post->setUser($user);
    }

    public function handlePicture(ViewEvent $event): void
    {
        $post = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!($post instanceof Post) || Request::METHOD_POST !== $method || null === $post->getBase64Picture()) {
            return;
        }

        $file = $this->handleNewPostPicture($post->getBase64Picture());
        $post->setPictureFilename($file->getFilename());
    }

    private function handleNewPostPicture(File $picture): File
    {
        $newFilename = sprintf(
            '%s-%d.%s',
            uniqid(),
            (new DateTime())->getTimestamp(),
            $picture->guessExtension()
        );

        $finalFile = $picture->move(
            $this->postsDirectory,
            $newFilename
        );

        $this->imageResizer->resizePostPicture($finalFile->getPathname());

        return $finalFile;
    }

    public function afterWrite(ViewEvent $event)
    {
        /** @var User|null $user */
        $user = $this->security->getUser();
        $post = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!($post instanceof Post) || Request::METHOD_POST !== $method || null === $user) {
            return;
        }

        $this->userRepository->incrementPostCount($user);

        // Refresh the user postCount
        $this->entityManager->refresh($user);
    }
}
