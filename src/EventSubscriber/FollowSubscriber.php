<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Follow;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class FollowSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepository         $userRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['handleFollowCount', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function handleFollowCount(ViewEvent $event)
    {
        $follow = $event->getControllerResult();
        if (!($follow instanceof Follow)) {
            return;
        }

        switch ($event->getRequest()->getMethod()) {
            case Request::METHOD_POST:
                $this->userRepository->incrementFollowerCount($follow->getFollowing());
                $this->userRepository->incrementFollowingCount($follow->getUser());
                break;

            case Request::METHOD_DELETE:
                $this->userRepository->decrementFollowerCount($follow->getFollowing());
                $this->userRepository->decrementFollowingCount($follow->getUser());
                break;
        }

        // Refresh the users followers and following counts
        $this->entityManager->refresh($follow->getUser());
        $this->entityManager->refresh($follow->getFollowing());
    }
}
