<?php

namespace App\Serializer;

use App\Entity\User;
use App\Repository\FollowRepository;
use ArrayObject;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class UserNormalizer implements NormalizerInterface
{
    public function __construct(
        private NormalizerInterface $normalizer,
        private Security            $security,
        private FollowRepository    $followRepository,
        private string              $defaultAvatarRelativePath,
        private string              $avatarsRelativeDirectory
    )
    {
    }

    /**
     * @param User $object
     * @param string|null $format
     * @param array $context
     * @return float|array|ArrayObject|bool|int|string|null
     * @throws ExceptionInterface|NonUniqueResultException
     */
    public function normalize(mixed $object, string $format = null, array $context = []): float|array|ArrayObject|bool|int|string|null
    {
        $avatarFilepath = $this->defaultAvatarRelativePath;
        if (null !== $object->getAvatarFilename()) {
            $avatarFilepath = sprintf(
                '%s/%s',
                $this->avatarsRelativeDirectory,
                $object->getAvatarFilename()
            );
        }

        $object->setAvatarFilePath($avatarFilepath);

        /** @var User $user */
        $user = $this->security->getUser();

        $object->setIsFollowed(
            null !== $user
            && $user !== $object
            && null !== $this->followRepository->findOneByUserAndFollowing($user, $object)
        );

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }
}
