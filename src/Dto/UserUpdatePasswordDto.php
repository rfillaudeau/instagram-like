<?php

namespace App\Dto;

use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class UserUpdatePasswordDto
{
    #[Assert\NotBlank]
    #[UserPassword]
    public ?string $currentPlainPassword;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public ?string $newPlainPassword;
}
