<?php

namespace App\Serializer;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\LikeRepository;
use ArrayObject;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class PostNormalizer implements NormalizerInterface
{
    public function __construct(
        private NormalizerInterface $normalizer,
        private Security            $security,
        private LikeRepository      $likeRepository,
        private string              $postsRelativeDirectory
    )
    {
    }

    /**
     * @param Post $object
     * @param string|null $format
     * @param array $context
     * @return float|array|ArrayObject|bool|int|string|null
     * @throws ExceptionInterface|NonUniqueResultException
     */
    public function normalize(
        mixed  $object,
        string $format = null,
        array  $context = []
    ): float|array|ArrayObject|bool|int|string|null
    {
        $object->setPictureFilePath(sprintf(
            '%s/%s',
            $this->postsRelativeDirectory,
            $object->getPictureFilename()
        ));

        /** @var User $user */
        $user = $this->security->getUser();

        $object->setIsLiked(
            null !== $user
            && null !== $this->likeRepository->findOneByUserAndPost($user, $object)
        );

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Post;
    }
}
