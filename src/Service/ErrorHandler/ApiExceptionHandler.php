<?php

namespace App\Service\ErrorHandler;

use App\DTO\Response\ExceptionDto;
use App\Exception\ApiException;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiExceptionHandler implements ErrorHandlerInterface
{
    /**
     * @param ApiException|Throwable $exception
     * @param JsonResponse $response
     * @param string $envMode
     * @return ExceptionDto
     */
    public function handlingException(
        Exception|Throwable $exception,
        JsonResponse         $response,
        string               $envMode
    ): ExceptionDto
    {
        $dto = new ExceptionDto();

        $dto->message = $exception->getMessage();
        $dto->code = $exception->getCode();
        $dto->payload = $exception->payload;
        $response->setStatusCode(Response::HTTP_MULTI_STATUS);

        return $dto;
    }

    /**
     * @param Exception|Throwable $exception
     * @return bool
     */
    public function isApply(Exception|Throwable $exception): bool
    {
        return $exception instanceof ApiException;
    }

    /**
     * @return int
     */
    public static function getPriority(): int
    {
        return 3;
    }
}