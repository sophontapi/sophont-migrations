<?php

namespace CurrencySolutions\Migrations\Factory\Service;

use CurrencySolutions\Migrations\Service\MigrationsCommandService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MigrationsCommandServiceFactory
 * @package CurrencySolutions\Migrations\Factory\Service
 */
class MigrationsCommandServiceFactory implements FactoryInterface
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

        return new MigrationsCommandService(
            $config['db'],
            $config['migrations']['directories']
        );
    }
}