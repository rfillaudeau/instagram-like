<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const UPDATE = 'UPDATE';
    public const UPDATE_AVATAR = 'UPDATE_AVATAR';
    public const UPDATE_PASSWORD = 'UPDATE_PASSWORD';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::UPDATE, self::UPDATE_AVATAR, self::UPDATE_PASSWORD])) {
            return false;
        }

        if (!($subject instanceof User)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed|User $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $loggedUser = $token->getUser();
        if (!($loggedUser instanceof User)) {
            // the user must be logged in; if not, deny access
            return false;
        }

        return match ($attribute) {
            self::UPDATE, self::UPDATE_AVATAR, self::UPDATE_PASSWORD => $subject === $loggedUser,
            default => throw new \LogicException('This code should not be reached!')
        };
    }
}
