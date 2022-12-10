<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractApiController extends AbstractController
{
    /**
     * @return UserInterface|User|null
     */
    protected function getUser(): UserInterface|User|null
    {
        return parent::getUser();
    }

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
