<?php

namespace App\Service\ErrorHandler;

use App\DTO\Response\ExceptionDto;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

#[AutoconfigureTag(self::TAG)]
interface ErrorHandlerInterface
{
    /** @const string */
    public const TAG = 'error.handler';

    /**
     * @param Exception|Throwable $exception
     * @param JsonResponse $response
     * @param string $envMode
     * @return ExceptionDto
     */
    public function handlingException(
        Exception|Throwable $exception,
        JsonResponse         $response,
        string               $envMode
    ): ExceptionDto;

    /**
     * @param Exception|Throwable $exception
     * @return bool
     */
    public function isApply(Exception|Throwable $exception): bool;

    /**
     * @return int
     */
    public static function getPriority(): int;
}