<?php

namespace App\Service\ErrorHandler;

use App\DTO\Response\ExceptionDto;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Throwable;

class ForbiddenExceptionHandler implements ErrorHandlerInterface
{
    /** @const string */
    public const FORBIDDEN_MESSAGE = 'Forbidden';

    /**
     * @param Throwable|Exception $exception
     * @param JsonResponse $response
     * @param string $envMode
     * @return ExceptionDto
     */
    public function handlingException(Throwable|Exception $exception, JsonResponse $response, string $envMode): ExceptionDto
    {
        $dto = new ExceptionDto();
        $dto->message = self::FORBIDDEN_MESSAGE;
        $dto->code = Response::HTTP_FORBIDDEN;

        $response->setStatusCode(403);

        return $dto;
    }

    /**
     * @param Throwable|Exception $exception
     * @return bool
     */
    public function isApply(Throwable|Exception $exception): bool
    {
        return $exception instanceof AccessDeniedException || $exception instanceof AccessDeniedHttpException;
    }

    /**
     * @return int
     */
    public static function getPriority(): int
    {
        return 999;
    }
}