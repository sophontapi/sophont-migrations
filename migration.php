#!/usr/bin/env php
<?php

define("IS_AFFILIATE_APP", false);

$basePath = getcwd();
ini_set('user_agent', 'CS - Command line migrations tool');
// load autoloader
if (file_exists("$basePath/vendor/autoload.php")) {
    require_once "$basePath/vendor/autoload.php";
} elseif (file_exists("$basePath/init_autoloader.php")) {
    require_once "$basePath/init_autoloader.php";
} elseif (\Phar::running()) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    echo 'Error: I cannot find the autoloader of the application.' . PHP_EOL;
    echo "Check if $basePath contains a valid ZF2 application." . PHP_EOL;
    exit(2);
}
if (file_exists("$basePath/config/application.config.php")) {
    $appConfig = require "$basePath/config/application.config.php";
    if (!isset($appConfig['modules']['Sophont\Migrations\Migrations'])) {
        $appConfig['modules'][] = 'Sophont\Migrations\Migrations';
        $appConfig['module_listener_options']['module_paths']['Sophont\Migrations\Migrations'] = __DIR__;
    }
} else {
    $appConfig = array(
        'modules' => array(
            'Sophont\Migrations\Migrations',
        ),
        'module_listener_options' => array(
            'config_glob_paths'    => array(
                'config/autoload/{,*.}{global,local}.php',
            ),
            'module_paths' => array(
                '.',
                './vendor',
            ),
        ),
    );
}
Zend\Mvc\Application::init($appConfig)->run();