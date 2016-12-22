<?php

namespace Sophont\Migrations;

return [
    'migrations' => [
        'directories' => [
            './module',
            './vendor/sophontapi'
        ]
    ],

    'service_manager' => [
        'factories' => [
            Service\MigrationsCommandService::class => Factory\Service\MigrationsCommandServiceFactory::class,
            Service\MigrationsStateCollectorService::class =>
                Factory\Service\MigrationsStateCollectorServiceFactory::class
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\CommandController::class => Factory\Controller\CommandControllerFactory::class
        ]
    ],
    'console' => array(
        'router' => array(
            'routes' => array(
                'generate' => [
                    'type' => 'Simple',
                    'options' => [
                        'route' => 'new <moduleName>',
                        'defaults' => [
                            'controller' => Controller\CommandController::class,
                            'action' => 'generate',
                        ],
                    ],
                ],
                'add' => [
                    'type' => 'Simple',
                    'options' => [
                        'route' => 'add <version>',
                        'defaults' => [
                            'controller' => Controller\CommandController::class,
                            'action' => 'add',
                        ],
                    ],
                ],
                'delete' => [
                    'type' => 'Simple',
                    'options' => [
                        'route' => 'delete <version>',
                        'defaults' => [
                            'controller' => Controller\CommandController::class,
                            'action' => 'delete',
                        ],
                    ],
                ],
                'status' => [
                    'type' => 'Simple',
                    'options' => [
                        'route' => 'status',
                        'defaults' => [
                            'controller' => Controller\CommandController::class,
                            'action' => 'status',
                        ],
                    ],
                ],
                'migrate' => [
                    'type' => 'Simple',
                    'options' => [
                        'route' => 'migrate',
                        'defaults' => [
                            'controller' => Controller\CommandController::class,
                            'action' => 'migrate',
                        ],
                    ],
                ],
                'rollback' => [
                    'type' => 'Simple',
                    'options' => [
                        'route' => 'rollback',
                        'defaults' => [
                            'controller' => Controller\CommandController::class,
                            'action' => 'rollback',
                        ],
                    ],
                ],
                'validate' => [
                    'type' => 'Simple',
                    'options' => [
                        'route' => 'validate',
                        'defaults' => [
                            'controller' => Controller\CommandController::class,
                            'action' => 'validate',
                        ],
                    ],
                ],
            )
        )

    )
];