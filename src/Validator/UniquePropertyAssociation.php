<?php

namespace App\Validator;

use Attribute;
use Exception;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniquePropertyAssociation extends Constraint
{
    public string $message = 'An entity "{{ class }}" with the same properties ({{ properties }}) already exists.';

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly array $properties,
        mixed                  $options = null,
        array                  $groups = null,
        mixed                  $payload = null
    )
    {
        parent::__construct($options, $groups, $payload);

        if (count($this->properties) < 2) {
            throw new Exception('At least 2 properties are required.');
        }
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
