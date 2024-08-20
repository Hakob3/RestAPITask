<?php

namespace App\EventListener;

use App\Service\ExceptionService;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

readonly class ApiExceptionListener
{
    /**
     * @param ExceptionService $exceptionService
     * @param string $environment
     */
    public function __construct(
        private ExceptionService $exceptionService,
        private string           $environment
    )
    {
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (str_contains($request->getPathInfo(), '/api/')) {
            $this->exceptionService->setEnvironment($this->environment);
            $response = $this->exceptionService->prepareExceptionResponse($exception);
            $event->allowCustomResponseCode();
            $event->setResponse($response);
        }
    }
}