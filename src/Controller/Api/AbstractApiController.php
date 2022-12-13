<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    protected static function getPagination(Request $request): array
    {
        $page = $request->query->getInt('page');
        if ($page <= 0) {
            $page = 1;
        }

        $maxResults = $request->query->getInt('itemsPerPage');
        if ($page <= 0) {
            $maxResults = 10;
        }

        $firstResult = ($page - 1) * $maxResults;

        return [$firstResult, $maxResults];
    }
}
