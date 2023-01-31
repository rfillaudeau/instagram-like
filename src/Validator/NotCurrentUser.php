<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class NotCurrentUser extends Constraint
{
    public string $message = 'This user should not be the same as the current user.';
}
