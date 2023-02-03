<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Follow;
use App\Entity\Post;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class OnlyPostsFromFollowingFilter extends AbstractFilter
{
    private const PARAMETER_NAME = 'onlyPostsFromFollowing';

    public function __construct(
        private readonly Security $security,
        ManagerRegistry           $managerRegistry,
        LoggerInterface           $logger = null,
        ?array                    $properties = null,
        ?NameConverterInterface   $nameConverter = null,
    )
    {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    public function getDescription(string $resourceClass): array
    {
        if (Post::class !== $resourceClass) {
            return [];
        }

        return [
            self::PARAMETER_NAME => [
                'property' => self::PARAMETER_NAME,
                'type' => Type::BUILTIN_TYPE_BOOL,
                'required' => false,
                'description' => 'Include only posts from users that the current user follows.',
            ],
        ];
    }

    protected function filterProperty(
        string                      $property,
        mixed                       $value,
        QueryBuilder                $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string                      $resourceClass,
        Operation                   $operation = null,
        array                       $context = []
    ): void
    {
        if (
            self::PARAMETER_NAME !== $property
            || ('true' !== $value && '1' !== $value)
            || Post::class !== $resourceClass
            || null === $user = $this->security->getUser()
        ) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $joinAlias = $queryNameGenerator->generateJoinAlias('follow');
        $userParameter = $queryNameGenerator->generateParameterName('user');

        $queryBuilder
            ->distinct()
            ->leftJoin(
                Follow::class,
                $joinAlias,
                Join::WITH,
                "$joinAlias.user = :$userParameter"
            )
            ->andWhere("($rootAlias.user = :$userParameter OR $rootAlias.user = $joinAlias.following)")
            ->setParameter($userParameter, $user);
    }
}
