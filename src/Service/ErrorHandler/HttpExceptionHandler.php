<?php

namespace App\Service\ErrorHandler;

use App\DTO\Response\ExceptionDto;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class HttpExceptionHandler implements ErrorHandlerInterface
{

    /**
     * @param HttpException|Throwable $exception
     * @param JsonResponse $response
     * @param string $envMode
     * @return ExceptionDto
     */
    public function handlingException(
        Exception|Throwable $exception,
        JsonResponse        $response,
        string              $envMode
    ): ExceptionDto {
        $dto = new ExceptionDto();
        $dto->message = $exception->getMessage();
        $dto->code = $exception->getStatusCode();
        $response->setStatusCode($exception->getStatusCode());
        $response->headers->replace($exception->getHeaders());

        return $dto;
    }

    /**
     * @param Exception|Throwable $exception
     * @return bool
     */
    public function isApply(Exception|Throwable $exception): bool
    {
        return $exception instanceof HttpException;
    }

    /**
     * @return int
     */
    public static function getPriority(): int
    {
        return 2;
    }
}