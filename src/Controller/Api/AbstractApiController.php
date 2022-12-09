<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractApiController extends AbstractController
{
    protected function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        if (count($errors) === 0) {
            return [];
        }

        $formattedErrors = [];
        foreach ($errors as $message) {
            $formattedErrors[] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        return $formattedErrors;
    }
}
