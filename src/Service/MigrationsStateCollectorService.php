<?php

namespace Sophont\Migrations\Service;

use Sophont\Migrations\Validator\MigrationFile;

/**
 * Class MigrationsStatusCollectorService
 * @package Sophont\Migrations\Service
 */
class MigrationsStateCollectorService extends AbstractMigrationsService
{
    /** @var MigrationFile $validator */
    protected $validator;

    /**
     * MigrationsStateCollectorService constructor.
     *
     * @param MigrationFile $validator
     * @param array $dbConfig
     * @param array $moduleDirectories
     */
    public function __construct(
        MigrationFile $validator,
        array $dbConfig,
        array $moduleDirectories)
    {
        parent::__construct($dbConfig, $moduleDirectories);
        $this->validator = $validator;
    }

    /**
     * @return array
     */
    public function getUnexecutedMigrations()
    {
        $configuration = $this->getDbalConfiguration();

        $executedMigrations = $configuration->getMigratedVersions();
        $availableMigrations = $configuration->getAvailableVersions();

        return array_diff($availableMigrations, $executedMigrations);
    }

    /**
     * @return bool
     */
    public function canMigrateUnexecutedMigrations()
    {
        $migrations = $this->getUnexecutedMigrations();
        foreach($migrations as $version) {
            $className = self::DEFAULT_NAMESPACE . "\\Version$version";
            $isValid = $this->validator->isValid($className);

            if(!$isValid) {
                $this->displayUnexecutedMigrationsState();
                return false;
            }
        }

        return true;
    }

    /**
     *
     */
    public function displayUnexecutedMigrationsState()
    {
        $migrations = $this->getUnexecutedMigrations();
        printf("\n\tUnexecuted migrations\n\t---------------------\n");
        foreach($migrations as $migration) {
            $this->displayUnexecutedMigrationState($migration);
        }
        printf("\n\n");
    }

    /**
     * @param $version
     */
    public function displayUnexecutedMigrationState($version)
    {
        $className = self::DEFAULT_NAMESPACE . "\\Version$version";
        $isValid = $this->validator->isValid($className);

        echo sprintf(
            "\t  \033[0m >> Migration (%d) %s: \e[%sm%s \e[0m%s\n",
            $version,
            $this->validator->getIssue(),
            $isValid ? '0;32' : '0;31', // color it green if migration is valid, red otherwise
            $isValid ? 'valid' : 'invalid',
            $this->validator->getDescription() ? : "no description found"
        );
    }
}