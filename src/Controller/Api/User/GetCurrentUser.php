<?php

namespace App\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetCurrentUser extends AbstractController
{
    public function __invoke(): RedirectResponse
    {
        return $this->redirectToRoute('api_get_user', ['id' => $this->getUser()->getId()]);
    }
}
