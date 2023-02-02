<?php

namespace App\Controller\Api\AccessToken;

use App\Repository\AccessTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[AsController]
class RevokeAccessToken extends AbstractController
{
    public function __construct(
        private readonly AccessTokenRepository  $accessTokenRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(Request $request)
    {
        $authorization = $request->headers->get('authorization');
        if (null === $authorization) {
            throw new AccessDeniedHttpException('Authorization not found.');
        }

        $accessToken = $this->accessTokenRepository->findOneByToken(
            str_replace('Bearer ', '', $authorization)
        );
        if (null === $accessToken) {
            throw new AccessDeniedHttpException('Access Token not found.');
        }

        $this->entityManager->remove($accessToken);
        $this->entityManager->flush();
    }
}
