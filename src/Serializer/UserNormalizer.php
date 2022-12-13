<?php

namespace App\Serializer;

use App\Entity\User;
use App\Repository\FollowRepository;
use ArrayObject;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserNormalizer implements NormalizerInterface
{
    public function __construct(
        // Known issue workaround (https://github.com/symfony/maker-bundle/issues/1252)
        #[Autowire(service: ObjectNormalizer::class)]
        private readonly NormalizerInterface $normalizer,
        private readonly Security $security,
        private readonly FollowRepository $followRepository,
        private readonly string $defaultAvatarRelativePath,
        private readonly string $avatarsRelativeDirectory
    )
    {}

    /**
     * @param User $object
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

        $data['isFollowed'] = null !== $user &&
            $user !== $object &&
            null !== $this->followRepository->findOneByUserAndFollowing($user, $object);

        $avatarFilepath = $this->defaultAvatarRelativePath;
        if (null !== $object->getAvatarFilename()) {
            $avatarFilepath = sprintf(
                '%s/%s',
                $this->avatarsRelativeDirectory,
                $object->getAvatarFilename()
            );
        }

        $data['avatarFilepath'] = $avatarFilepath;

        return $data;
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }
}
