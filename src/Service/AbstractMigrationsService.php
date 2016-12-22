<?php

namespace Sophont\Migrations\Service;

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Zend\Code\Reflection\FileReflection;

/**
 * Class AbstractMigrationsService
 * @package Sophont\Migrations\Service
 */
abstract class AbstractMigrationsService
{
    const DEFAULT_NAMESPACE = 'Sophont\\Migrations';

    /** @var array $dbConfig */
    protected $dbConfig;

    /** @var array $moduleDirectories */
    protected $moduleDirectories;

    /** @var Connection $connection */
    protected $connection;

    /** @var string $migrationsTempDirectory */
    protected $migrationsTempDirectory;

    /** @var Application $application */
    protected $application;

    /** @var Configuration $dbalConfiguration */
    protected $dbalConfiguration;

    /**
     * MigrationsCommandService constructor.
     *
     * @param array $dbConfig
     * @param array $moduleDirectories
     */
    public function __construct(array $dbConfig, array $moduleDirectories)
    {
        $this->dbConfig = $this->getDbalAdaptedDbConfig($dbConfig);
        $this->moduleDirectories = $moduleDirectories;

        $this->setupApplication();

    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connection = new Connection(
                $this->dbConfig,
                new Driver()
            );
        }

        return $this->connection;
    }

    /**
     * @return HelperSet
     */
    public function getHelperSet()
    {
        $helperSet = new HelperSet(array(
            new ConnectionHelper(
                $this->getConnection()
            )
        ));

        $helperSet->set(new QuestionHelper(), 'question');
        return $helperSet;
    }

    /**
     * @return Configuration
     */
    public function getDbalConfiguration()
    {
        if (null !== $this->dbalConfiguration) {
            return $this->dbalConfiguration;
        }

        $this->dbalConfiguration = $dbalConfiguration = new Configuration(
            $this->getConnection()
        );

        $this->migrationsTempDirectory = getcwd() . "/data/db/migrations";

        if (!file_exists($this->migrationsTempDirectory) || !is_dir($this->migrationsTempDirectory)) {
            mkdir($this->migrationsTempDirectory, 0775, true);
        }

        $dbalConfiguration->setMigrationsTableName('migration_versions');
        $dbalConfiguration->setName('Sophont DB Migrations');
        $dbalConfiguration->setMigrationsNamespace(self::DEFAULT_NAMESPACE);
        $dbalConfiguration->setMigrationsDirectory($this->migrationsTempDirectory);

        foreach (array_unique($this->moduleDirectories) as $directory) {
            $dbalConfiguration->registerMigrationsFromDirectory($directory);
        }

        return $dbalConfiguration;
    }

    /**
     * @param $config
     * @return array
     * @throws \Exception
     */
    public function getDbalAdaptedDbConfig($config)
    {
        preg_match("/mysql:dbname=(.*);host=(.*)/", $config['dsn'], $matches);

        if (count($matches) !== 3) {
            throw new \Exception("Wrong db configuration is provided");
        }

        return $dbConfig = [
            'user' => $config['username'],
            'password' => $config['password'],
            'dbname' => $matches[1],
            'host' => $matches[2],
            'driver' => 'mysql'
        ];
    }

    /**
     * @param $moduleName
     * @return string
     * @throws \Exception
     */
    protected function getModuleMigrationDirectory($moduleName)
    {
        foreach ($this->moduleDirectories as $directory) {
            $path = sprintf("%s/%s", rtrim($directory, "/"), $moduleName);
            if (!(file_exists($path) && is_dir($path))) {
                continue;
            }

            $migrationsDir = $path . "/migrations";
            if (!file_exists($migrationsDir)) {
                mkdir($migrationsDir, 0775, false);
            }

            return $migrationsDir;
        }

        throw new \Exception(
            sprintf(
                "Can't find '%s' module in registered directories %s",
                $moduleName,
                join(" ", $this->moduleDirectories)
            )
        );
    }

    /**
     * Configure db connection and all needed migration commands
     */
    protected function setupApplication()
    {
        $commands = array(
            new ExecuteCommand(),
            new GenerateCommand(),
            new MigrateCommand(),
            new StatusCommand(),
            new VersionCommand()
        );

        $dbalConfig = $this->getDbalConfiguration();
        array_walk($commands, function (AbstractCommand $command) use ($dbalConfig) {
            $command->setMigrationConfiguration($dbalConfig);
        });

        $this->application = $app = new Application();
        $app->setCatchExceptions(true);
        $app->setHelperSet($this->getHelperSet());
        $app->addCommands($commands);
        $app->setAutoExit(false);
    }

    /**
     * @param InputInterface $inputInterface
     * @param OutputInterface $outputInterface
     * @return int
     * @throws \Exception
     */
    protected function runWithoutInteractive(InputInterface $inputInterface, OutputInterface $outputInterface)
    {
        $inputInterface->setInteractive(false);
        return $this->application->run($inputInterface, $outputInterface);
    }

    /**
     * @param $filename
     * @return string
     */
    protected function getModuleNamespaceFromFile($filename)
    {
        $fileReflection = new FileReflection($filename);
        return $fileReflection->getNamespace();
    }

    /**
     * @param $migrationClass
     */
    protected function triggerMigrationsAutoloader($migrationClass)
    {
        new $migrationClass;
    }
}