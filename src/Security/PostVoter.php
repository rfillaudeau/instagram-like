<?php

namespace App\Security;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    public const UPDATE = 'UPDATE';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::UPDATE, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed|Post $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $loggedUser = $token->getUser();
        if (!$loggedUser instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        return match($attribute) {
            self::UPDATE, self::DELETE => $subject->getUser() === $loggedUser,
            default => throw new \LogicException('This code should not be reached!')
        };
    }
}
