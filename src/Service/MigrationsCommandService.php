<?php

namespace Sophont\Migrations\Service;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class MigrationsCommandService
 * @package Sophont\Migrations\Service
 */
class MigrationsCommandService extends AbstractMigrationsService
{
    /**
     * Generate new migration file for the request module
     *
     * @param $moduleName
     * @throws \Exception
     */
    public function migrationGenerate($moduleName)
    {
        $moduleMigrationDirectory = $this->getModuleMigrationDirectory($moduleName);

        $input = new ArrayInput([
            'command' => 'migrations:generate'
        ]);
        $output = new BufferedOutput();
        $this->application->run($input, $output);

        $output = $output->fetch();
        preg_match('/Version(\d+).php/', $output, $matches);
        $version = !empty($matches[1]) ? $matches[1] : null;

        if(!$version) {
            throw new \Exception("Something went wrong");
        }

        $this->moveGeneratedFileToItsModuleDir($version, $moduleMigrationDirectory);
        printf("Generated new migration class (Version%d.php) to \"%s\" module \n",
            $version,
            $moduleName
        );
    }

    /**
     * Show db configurations and current migration status
     *
     * @throws \Exception
     */
    public function migrationStatus()
    {
        $input = new ArrayInput(['command' => 'migrations:status']);
        $output = new ConsoleOutput();
        $exitCode = $this->runWithoutInteractive($input, $output);
        exit($exitCode);
    }

    /**
     * Manually add an existing migration file to migration chain
     * @param $version
     */
    public function migrationAdd($version)
    {
        $input = new ArrayInput([
            'command' => 'migrations:version',
            '--add' => $version
        ]);
        $output = new ConsoleOutput();
        $exitCode = $this->runWithoutInteractive($input, $output);
        exit($exitCode);
    }

    /**
     * Manually remove a migration from migration chain
     * @param $version
     */
    public function migrationDelete($version)
    {
        $input = new ArrayInput([
            'command' => 'migrations:version',
            '--delete' => $version
        ]);
        $output = new ConsoleOutput();
        $exitCode = $this->runWithoutInteractive($input, $output);
        exit($exitCode);
    }

    /**
     * @throws \Exception
     */
    public function migrationMigrate()
    {
        $input = new ArrayInput([
            'command' => 'migrations:migrate'
        ]);
        $output = new ConsoleOutput();
        $exitCode = $this->runWithoutInteractive($input, $output);
        exit($exitCode);
    }

    /**
     * @param $version
     * @throws \Exception
     */
    public function execute($version) {
        $input = new ArrayInput([
            'command' => 'migrations:execute',
            'version' => $version
        ]);
        $output = new ConsoleOutput();
        $exitCode = $this->runWithoutInteractive($input, $output);
        exit($exitCode);
    }

    /**
     * @param $version
     * @throws \Exception
     */
    public function migrationRollback($version)
    {
        $input = new ArrayInput([
            'command' => 'migrations:migrate',
            $version => ''
        ]);
        $output = new ConsoleOutput();
        $exitCode = $this->runWithoutInteractive($input, $output);
        exit($exitCode);
    }

    /**
     * @param $version
     * @param $migrationDir
     */
    private function moveGeneratedFileToItsModuleDir($version, $migrationDir)
    {
        $fileName = "Version$version.php";

        $generatedFile = $this->migrationsTempDirectory . "/" . $fileName;
        rename($generatedFile, $migrationDir . "/" . $fileName);
    }
}