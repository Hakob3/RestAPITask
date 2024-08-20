<?php

namespace App\DependencyInjection\Compiler;

use App\Service\ErrorHandler\ErrorHandlerInterface;
use App\Service\ExceptionService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ErrorHandlerPath implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ExceptionService::class)) {
            return;
        }

        $service = $container->findDefinition(ExceptionService::class);
        foreach (array_keys($container->findTaggedServiceIds(ErrorHandlerInterface::TAG)) as $serviceId) {
            $service->addMethodCall('addExceptionHandler', [new Reference($serviceId)]);
        }
    }
}