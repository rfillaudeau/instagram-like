<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NotCurrentUserValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof NotCurrentUser)) {
            throw new UnexpectedValueException($constraint, NotCurrentUser::class);
        }

        if (null === $value) {
            return;
        }

        if (!($value instanceof User)) {
            throw new UnexpectedValueException($value, User::class);
        }

        if ($value === $this->security->getUser()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
