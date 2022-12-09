<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const CHANGE_PASSWORD = 'CHANGE_PASSWORD';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::CHANGE_PASSWORD])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $loggedUser = $token->getUser();
        if (!$loggedUser instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var User $user */
        $user = $subject;

        return match($attribute) {
            self::CHANGE_PASSWORD => $this->canChangePassword($user, $loggedUser),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canChangePassword(User $user, User $loggedUser): bool
    {
        return $user === $loggedUser;
    }
}
