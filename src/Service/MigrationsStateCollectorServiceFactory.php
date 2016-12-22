<?php

namespace Sophont\Migrations\Migrations\Factory\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Sophont\Migrations\Service\MigrationsStateCollectorService;
use Sophont\Migrations\Validator\MigrationFile;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;


/**
 * Class MigrationsStateCollectorService
 * @package Sophont\Migrations\Migrations\Factory\Service
 */
class MigrationsStateCollectorServiceFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        return new MigrationsStateCollectorService(
            new MigrationFile(),
            $config['db'],
            $config['migrations']['directories']
        );
    }
}