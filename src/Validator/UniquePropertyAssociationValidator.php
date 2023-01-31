<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniquePropertyAssociationValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof UniquePropertyAssociation)) {
            throw new UnexpectedValueException($constraint, UniquePropertyAssociation::class);
        }

        $criteria = [];
        foreach ($constraint->getProperties() as $property) {
            $method = sprintf('get%s', ucfirst($property));

            $criteria[$property] = method_exists($value, $method) ? $value->$method() : $value->$property;
        }

        $object = $this->entityManager->getRepository($value::class)->findOneBy($criteria);
        if (null !== $object) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ class }}', $value::class)
                ->setParameter('{{ properties }}', implode(', ', $constraint->getProperties()))
                ->addViolation();
        }
    }
}
