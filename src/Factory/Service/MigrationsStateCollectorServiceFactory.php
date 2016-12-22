<?php

namespace CurrencySolutions\Migrations\Factory\Service;

use CurrencySolutions\Migrations\Service\MigrationsStateCollectorService;
use CurrencySolutions\Migrations\Validator\MigrationFile;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MigrationsStateCollectorService
 * @package CurrencySolutions\Migrations\Factory\Service
 */
class MigrationsStateCollectorServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        return new MigrationsStateCollectorService(
            new MigrationFile(),
            $config['db'],
            $config['migrations']['directories']
        );
    }
}
{

}