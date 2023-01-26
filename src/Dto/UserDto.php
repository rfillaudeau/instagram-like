<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    public const GROUP_DEFAULT = 'Default';
    public const GROUP_CREATE = 'user:create';
    public const GROUP_UPDATE_AVATAR = 'user:update:avatar';

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 30)]
    public ?string $username;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    public ?string $email;

    public ?string $bio;

    #[Assert\NotNull(groups: [self::GROUP_UPDATE_AVATAR])]
    #[Assert\Image(
        maxSize: '3m',
        maxWidth: 3000,
        maxHeight: 3000,
        groups: [self::GROUP_UPDATE_AVATAR]
    )]
    public ?File $avatar = null;

    #[Assert\NotBlank(groups: [self::GROUP_CREATE])]
    #[Assert\Length(min: 6, groups: [self::GROUP_CREATE])]
    public ?string $plainPassword = null;
}
