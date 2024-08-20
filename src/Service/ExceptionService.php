<?php

namespace App\Service;

use App\Service\ErrorHandler\DefaultExceptionHandler;
use App\Service\ErrorHandler\ErrorHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class ExceptionService
{
    /** @const int */
    private const UNEXPECTED_EXCEPTION_CODE = 500;

    /** @const string */
    private const UNEXPECTED_EXCEPTION_MESSAGE_IN_PROD_MODE = "Unexpected Server EXCEPTION";

    /** @var array|null */
    private ?array $errorHandlers = [];

    /**
     * @param SerializerService $serializerService
     * @param string $environment
     */
    public function __construct(
        private readonly SerializerService $serializerService,
        private string                     $environment = 'prod',
    )
    {
    }

    /**
     * @param Throwable $exception
     *
     * @return JsonResponse
     */
    public function prepareExceptionResponse(Throwable $exception): JsonResponse
    {
        $response = new JsonResponse();
        usort(
            $this->errorHandlers,
            static fn(ErrorHandlerInterface $a, ErrorHandlerInterface $b) => $b::getPriority() - $a::getPriority()
        );
//        dd($this->errorHandlers);
        /** @var ErrorHandlerInterface $errorHandler */
        foreach ($this->errorHandlers ?? [] as $errorHandler) {
            if ($errorHandler->isApply($exception)) {
                $dto = $this->serializerService->serializeToJson(
                    $errorHandler->handlingException(
                        $exception,
                        $response,
                        $this->environment
                    )
                );

                return $response->setJson($dto);
            }
        }

        $defaultErrorHandler = new DefaultExceptionHandler();
        $defaultErrorHandler->setUnexpectedServerExceptionCode(self::UNEXPECTED_EXCEPTION_CODE);
        $defaultErrorHandler->setUnexpectedServerExceptionMessage(self::UNEXPECTED_EXCEPTION_MESSAGE_IN_PROD_MODE);
        $dto = $this->serializerService->serializeToJson(
            $defaultErrorHandler->handlingException(
                $exception,
                $response,
                $this->environment
            )
        );

        return $response->setJson($dto);
    }

    /**
     * @param ErrorHandlerInterface $errorHandler
     */
    public function addExceptionHandler(ErrorHandlerInterface $errorHandler): void
    {
        array_unshift($this->errorHandlers, $errorHandler);
    }

    /**
     * @param string $environment
     */
    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }
}