<?php

namespace App\Controller\Api\User;

use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class GetByUsername extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(string $username): RedirectResponse
    {
        $user = $this->userRepository->findOneByUsername($username);
        if (null === $user) {
            throw new NotFoundHttpException(sprintf('User "%s" not found.', $username));
        }

        return $this->redirectToRoute('api_get_user', ['id' => $user->getId()]);
    }
}
