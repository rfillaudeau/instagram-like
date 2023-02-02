<?php

namespace App\Controller\Api\Follow;

use App\Entity\Follow;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateFollow extends AbstractController
{
    public function __invoke(User $user): Follow
    {
        return (new Follow())
            ->setUser($this->getUser())
            ->setFollowing($user);
    }
}
