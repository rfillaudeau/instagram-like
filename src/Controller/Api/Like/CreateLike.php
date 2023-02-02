<?php

namespace App\Controller\Api\Like;

use App\Entity\Like;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateLike extends AbstractController
{
    public function __invoke(Post $post): Like
    {
        return (new Like())
            ->setPost($post)
            ->setUser($this->getUser());
    }
}
