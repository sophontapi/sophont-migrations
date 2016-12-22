<?php

namespace CurrencySolutions\Migrations\Factory\Controller;

use CurrencySolutions\Migrations\Controller\CommandController;
use CurrencySolutions\Migrations\Service\MigrationsCommandService;
use CurrencySolutions\Migrations\Service\MigrationsStateCollectorService;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\PluginManager;

/**
 * Class CommandControllerFactory
 * @package CurrencySolutions\Migrations\Factory\Controller
 */
class CommandControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface|PluginManager $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();

        /** @var MigrationsCommandService $commandService */
        $commandService = $services->get(MigrationsCommandService::class);

        /** @var MigrationsStateCollectorService $stateService */
        $stateService = $services->get(MigrationsStateCollectorService::class);

        return new CommandController($commandService, $stateService);
    }
}