<?php

namespace App\Serializer;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\LikeRepository;
use ArrayObject;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PostNormalizer implements NormalizerInterface
{
    public function __construct(
        // Known issue workaround (https://github.com/symfony/maker-bundle/issues/1252)
        #[Autowire(service: ObjectNormalizer::class)]
        private readonly NormalizerInterface $normalizer,
        private readonly Security $security,
        private readonly LikeRepository $likeRepository
    )
    {}

    /**
     * @param Post $object
     * @param string|null $format
     * @param array $context
     * @return float|array|ArrayObject|bool|int|string|null
     * @throws ExceptionInterface|NonUniqueResultException
     */
    public function normalize(mixed $object, string $format = null, array $context = []): float|array|ArrayObject|bool|int|string|null
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        /** @var User $user */
        $user = $this->security->getUser();

        $data['isLiked'] = null !== $user && null !== $this->likeRepository->findOneByUserAndPost($user, $object);

        return $data;
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Post;
    }
}
