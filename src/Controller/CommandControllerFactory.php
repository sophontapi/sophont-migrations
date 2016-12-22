<?php

namespace Sophont\Migrations\Migrations\Factory\Controller;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;

use Sophont\Migrations\Controller\CommandController;
use Sophont\Migrations\Service\MigrationsCommandService;
use Sophont\Migrations\Service\MigrationsStateCollectorService;

use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class CommandControllerFactory
 * @package Sophont\Migrations\Migrations\Factory\Controller
 */
class CommandControllerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException if unable to resolve the service.
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $services = $container->get('ServiceManager');

        /** @var MigrationsCommandService $commandService */
        $commandService = $services->get(MigrationsCommandService::class);

        /** @var MigrationsStateCollectorService $stateService */
        $stateService = $services->get(MigrationsStateCollectorService::class);

        return new CommandController($commandService, $stateService);
    }
}