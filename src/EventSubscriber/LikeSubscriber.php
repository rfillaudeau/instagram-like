<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Like;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class LikeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PostRepository         $postRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['handleLikeCount', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function handleLikeCount(ViewEvent $event)
    {
        $like = $event->getControllerResult();
        if (!($like instanceof Like)) {
            return;
        }

        switch ($event->getRequest()->getMethod()) {
            case Request::METHOD_POST:
                $this->postRepository->incrementLikeCount($like->getPost());
                break;

            case Request::METHOD_DELETE:
                $this->postRepository->decrementLikeCount($like->getPost());
                break;
        }

        // Refresh the post like count
        $this->entityManager->refresh($like->getPost());
    }
}
