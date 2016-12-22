<?php

namespace Sophont\Migrations\Controller;

use Sophont\Migrations\Service\MigrationsCommandService;
use Sophont\Migrations\Service\MigrationsStateCollectorService;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class CommandController
 * @package Sophont\Migrations\Controller
 */
class CommandController extends AbstractActionController
{
    /** @var MigrationsCommandService $commandService */
    protected $commandService;

    /** @var MigrationsStateCollectorService $stateService */
    protected $stateService;

    /**
     * CommandController constructor.
     *
     * @param MigrationsCommandService $commandService
     * @param MigrationsStateCollectorService $stateCommand
     */
    public function __construct(
        MigrationsCommandService $commandService,
        MigrationsStateCollectorService $stateCommand
    )
    {
        $this->commandService = $commandService;
        $this->stateService = $stateCommand;
    }

    /**
     * Generate new migration for the request module
     */
    public function generateAction()
    {
        $moduleName = $this->params()->fromRoute('moduleName');
        $this->commandService->migrationGenerate($moduleName);
    }

    /**
     * Show db configurations and migration status
     */
    public function statusAction()
    {
        $this->commandService->migrationStatus();
    }

    /**
     * Add existing migration to migrations chain
     */
    public function addAction()
    {
        if($this->stateService->canMigrateUnexecutedMigrations()) {
            $this->commandService->migrationMigrate();
        }

        $version = $this->params()->fromRoute('version');
        $this->commandService->migrationAdd($version);
    }

    /**
     * Remove migration from chain by its version
     */
    public function deleteAction()
    {
        $version = $this->params()->fromRoute('version');
        $this->commandService->migrationDelete($version);
    }

    /**
     * Migrate available migrations
     */
    public function migrateAction()
    {
        if($this->stateService->canMigrateUnexecutedMigrations()) {
            $this->commandService->migrationMigrate();
        }
    }

    /**
     * Rollback to a specific version
     */
    public function rollbackAction()
    {
        $version = $this->params()->fromRoute("version");

        if($this->stateService->canMigrateUnexecutedMigrations()) {
            $this->commandService->migrationRollback($version);
        }
    }

    /**
     * Validate migration files
     */
    public function validateAction()
    {
        if($this->stateService->canMigrateUnexecutedMigrations()) {
            echo "All available migration files are valid";
            exit(0);
        }

        exit(1); // exit with an error code
    }
}