<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class EntitySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['setUserOnCreate', EventPriorities::PRE_VALIDATE],
            ],
        ];
    }

    public function setUserOnCreate(ViewEvent $event)
    {
        $user = $this->security->getUser();
        if (Request::METHOD_POST !== $event->getRequest()->getMethod() || !($user instanceof User)) {
            return;
        }

        $entity = $event->getControllerResult();
        if ($entity instanceof Post) {
            $entity->setUser($user);
        }
    }
}
