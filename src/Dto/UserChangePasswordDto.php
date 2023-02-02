<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserChangePasswordDto
{
    #[Assert\NotBlank]
    public ?string $currentPlainPassword;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public ?string $newPlainPassword;
}
