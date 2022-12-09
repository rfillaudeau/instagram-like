<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DefaultController extends AbstractController
{
    #[Route(
        '/{reactRouting}',
        name: 'app_index',
        requirements: ['reactRouting' => '.+'],
        defaults: ['reactRouting' => null],
        priority: '-1'
    )]
    public function index(SerializerInterface $serializer, NormalizerInterface $normalizer): Response
    {
        $user = $this->getUser();

        $userData = null;
        if (null !== $user) {
            $userData = $serializer->serialize(
                $user,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'user:read']
            );
        }

        return $this->render('default/index.html.twig', [
            'user' => $userData,
        ]);
    }
}
