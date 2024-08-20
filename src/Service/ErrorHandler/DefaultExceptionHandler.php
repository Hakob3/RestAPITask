<?php

namespace App\Service\ErrorHandler;

use App\DTO\Response\ExceptionDto;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DefaultExceptionHandler implements ErrorHandlerInterface
{

    private int $unexpectedServerExceptionCode;

    private  string $unexpectedServerExceptionMessage;

    /**
     * @param Exception|Throwable $exception
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
        $dto->code = $this->getUnexpectedServerExceptionCode();
        if ('dev' === $envMode) {
            $dto->message = $exception->getMessage();
        } else {
            $dto->message = $this->getUnexpectedServerExceptionMessage();
        }
        $response->setStatusCode(Response::HTTP_BAD_GATEWAY);

        return $dto;
    }

    /**
     * @param Exception|Throwable $exception
     * @return bool
     */
    public function isApply(Exception|Throwable $exception): bool
    {
        return $exception instanceof Exception;
    }

    /**
     * @return int
     */
    public function getUnexpectedServerExceptionCode(): int
    {
        return $this->unexpectedServerExceptionCode;
    }

    /**
     * @param int $unexpectedServerExceptionCode
     * @return DefaultExceptionHandler
     */
    public function setUnexpectedServerExceptionCode(int $unexpectedServerExceptionCode): DefaultExceptionHandler
    {
        $this->unexpectedServerExceptionCode = $unexpectedServerExceptionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnexpectedServerExceptionMessage(): string
    {
        return $this->unexpectedServerExceptionMessage;
    }

    /**
     * @param string $unexpectedServerExceptionMessage
     * @return DefaultExceptionHandler
     */
    public function setUnexpectedServerExceptionMessage(string $unexpectedServerExceptionMessage
    ): DefaultExceptionHandler
    {
        $this->unexpectedServerExceptionMessage = $unexpectedServerExceptionMessage;

        return $this;
    }

    /**
     * @return int
     */
    public static function getPriority(): int
    {
        return 1;
    }
}