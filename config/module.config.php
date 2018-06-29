<?php
return array(
    'service_manager' => array(
        'aliases' => array(
            'playgroundcore_doctrine_em' => 'doctrine.entitymanager.orm_default',
            'google-analytics'           => 'PlaygroundCore\Analytics\Tracker',
            'facebook-opengraph'         => 'PlaygroundCore\Opengraph\Tracker',
            'twitter-card'               => 'PlaygroundCore\TwitterCard\Config',
            'twilio'                     => 'playgroundcore_twilio',
            'ffmpeg'                     => 'playgroundcore_phpvideotoolkit'
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'playgroundcore_message'             => 'PlaygroundCore\Mail\Service\Factory\MessageFactory',
            'playgroundcore_cron_service'        => 'PlaygroundCore\Service\Factory\CronFactory',
            'playgroundcore_shortenurl_service'  => 'PlaygroundCore\Service\Factory\ShortenUrlFactory',
            'playgroundcore_recaptcha_service'   => 'PlaygroundCore\Service\Factory\RecaptchaFactory',
            'playgroundcore_website_service'     => 'PlaygroundCore\Service\Factory\WebsiteFactory',
            'playgroundcore_locale_service'      => 'PlaygroundCore\Service\Factory\LocaleFactory',
            'playgroundcore_formgen_service'     => 'PlaygroundCore\Service\Factory\FormgenFactory',
            'playgroundcore_image_service'       => 'PlaygroundCore\Service\Factory\ImageFactory',
            'playgroundcore_ffmpeg_service'      => 'PlaygroundCore\Service\Factory\FfmpegFactory',
            'playgroundcore_country_service'     => 'PlaygroundCore\Service\Factory\CountryFactory',
            'playgroundcore_twilio'              => 'PlaygroundCore\Service\Factory\TwilioServiceFactory',
            'playgroundcore_phpvideotoolkit'     => 'PlaygroundCore\Service\Factory\PhpvideotoolkitServiceFactory',
            'playgroundcore_transport'           => 'PlaygroundCore\Mail\Transport\Service\TransportFactory',
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'playgroundcore_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/Entity'
            ),

            'orm_default' => array(
                'drivers' => array(
                    'PlaygroundCore\Entity'  => 'playgroundcore_entity'
                )
            )
        ),
        'connection' => array(
            // default connection name
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host'          => '127.0.0.1',
                    'port'          => '3306',
                    'user'          => 'root',
                    'password'      => 'root',
                    'dbname'        => '',
                    'charset'       => 'utf8',
                    'driverOptions' => array(1002 => 'SET sql_mode="TRADITIONAL"'),
                )
            )
        )
    ),

    'session' => array(
        'remember_me_seconds' => 2419200,
        'use_cookies' => true,
        'cookie_httponly' => true,
    ),

    'bjyauthorize' => array(
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'core'          => array(),
            ),
        ),
    
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('admin'), 'core',           array('dashboard', 'edit')),
                ),
            ),
        ),
    
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                // Frontend
                array('controller' => 'playgroundcore_frontend_switchlocale', 'roles' => array('guest', 'user')),

                // CRON / Console
                array('controller' => 'AsseticBundle\Controller\Console', 'roles' => array('guest', 'user')),
                array('controller' => 'DoctrineModule\Controller\Cli', 'roles' => array('guest', 'user')),
                array('controller' => 'playgroundcore_console', 'roles' => array('guest', 'user')),
    
                // Admin area
                array('controller' => 'playgroundcore_admin_formgen', 'roles' => array('admin')),
                array('controller' => 'playgroundcore_admin_elfinder', 'roles' => array('admin')),
                array('controller' => 'DoctrineORMModule\Yuml\YumlController', 'roles' => array('admin')),
            ),
        ),
    ),
    
    'assetic_configuration' => array(
        'modules' => array(
            'lib' => array(
                'collections' => array(    
                    'core_flags' => array(
                        'assets' => array(
                            __DIR__ . '/../view/images/flag/*.png',
                        ),
                        'options' => array(
                            'move_raw' => true,
                            'output' => 'lib/images/flag',
                        )
                    ),
                ),
            ),
        ),
    ),

    'router' => array(
        'router_class' => '\Zend\Router\Http\TranslatorAwareTreeRouteStack',
        'routes' => array(
            'frontend' => array(
                'type'      => 'Segment',
                'may_terminate' => true,
                'options'   => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
                'child_routes' => array(
                    'switchlocale' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => 'switch[/:lang][/:redirect]',
                            'defaults' => array(
                                'controller' => 'playgroundcore_frontend_switchlocale',
                                'action'     => 'switch',
                            ),
                        ),
                    ),
		            // Give the possibility to call Cron from browser
		            'cron' => array(
		                'type' => 'Zend\Router\Http\Literal',
		                'options' => array(
		                    'route' => 'cron',
		                    'defaults' => array(
		                        'controller' => 'playgroundcore_console',
		                        'action' => 'cron'
		                    ),
		                ),
		            ),
        		),
        	),
            'admin' => array(
                'type' => 'Zend\Router\Http\Literal',
                'priority' => -1000,
                'options' => array(
                    'route'    => '/admin',
                ),
                'child_routes' => array(
                    'elfinder' => array(
                        'type' => 'Zend\Router\Http\Literal',
                        'options' => array(
                            'route' => '/elfinder',
                            'defaults' => array(
                                'controller' => 'playgroundcore_admin_elfinder',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'connector' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/connector',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_elfinder',
                                        'action'     => 'connector',
                                    ),
                                ),
                            ),
                            'ckeditor' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/ckeditor',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_elfinder',
                                        'action'     => 'ckeditor',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'formgen' => array(
                        'type'    => 'Zend\Router\Http\Literal',
                        'options' => array(
                                'route'    => '/formgen',
                                'defaults' => array(
                                        'controller'    => 'playgroundcore_admin_formgen',
                                        'action'        => 'index',
                                ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'create' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/create',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'create',
                                    ),
                                ),
                            ),
                            'generate' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/generate',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'generate',
                                    ),
                                ),
                            ),
                            'list' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/list',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'list',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/edit[/:formId]',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'edit',
                                    ),
                                    'constraints' => array(
                                        'formId' => '[0-9]*',
                                    ),
                                ),
                            ),
                            'activate' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/activate[/:formId]',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'activate',
                                    ),
                                    'constraints' => array(
                                        'formId' => '[0-9]*',
                                    ),
                                ),
                            ),
                            'view' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/view[/:form]',
                                    'constraints' => array(
                                        'form' => '[a-zA-Z0-9_-]+'
                                    ),
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'view',
                                    ),
                                ),
                            ),
                            'input' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/input',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'input',
                                    ),
                                ),
                            ),
                            'paragraph' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/paragraph',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'paragraph',
                                    ),
                                ),
                            ),
                            'number' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/number',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'number',
                                    ),
                                ),
                            ),
                            'phone' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/phone',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'phone',
                                    ),
                                ),
                            ),
                            'checkbox' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/checkbox',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'checkbox',
                                    ),
                                ),
                            ),
                            'radio' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/radio',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'radio',
                                    ),
                                ),
                            ),
                            'dropdown' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/dropdown',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'dropdown',
                                    ),
                                ),
                            ),
                            'password' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/password',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'password',
                                    ),
                                ),
                            ),
                            'passwordverify' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/passwordverify',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'passwordverify',
                                    ),
                                ),
                            ),
                            'email' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/email',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'email',
                                    ),
                                ),
                            ),
                            'date' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/date',
                                    'defaults' => array(
                                        'controller' => 'Index',
                                        'action'     => 'date',
                                    ),
                                ),
                            ),
                            'upload' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/upload',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'upload',
                                    ),
                                ),
                            ),
                            'creditcard' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/creditcard',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'creditcard',
                                    ),
                                ),
                            ),
                            'url' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/url',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'url',
                                    ),
                                ),
                            ),
                            'hidden' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/hidden',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'hidden',
                                    ),
                                ),
                            ),
                            'test' => array(
                                'type' => 'Zend\Router\Http\Literal',
                                'options' => array(
                                    'route' => '/test',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_formgen',
                                        'action'     => 'test',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'website' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/sitecountry',
                            'defaults' => array(
                                'controller' => 'playgroundcore_admin_website',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                     'route' => '/list',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_website',
                                        'action'     => 'list',
                                    ),
                                ),
                            ),

                            'edit-active' => array(
                                'type' => 'Segment',
                                'options' => array(
                                     'route' => '/edit-active/[:websiteId]',
                                    'defaults' => array(
                                        'controller' => 'playgroundcore_admin_website',
                                        'action'     => 'editactive',
                                    ),
                                    'constraints' => array(
                                        'websiteId' => '[0-9]*',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'cron' => array(
                    'options' => array(
                        'route' => 'cron',
                        'defaults' => array(
                            'controller' => 'playgroundcore_console',
                            'action' => 'cron'
                        ),
                    ),
                ),
            )
        )
    ),

    'controllers' => array(
        'factories' => array(
            'playgroundcore_admin_formgen'         => 'PlaygroundCore\Service\Factory\AdminFormgenControllerFactory',
            'playgroundcore_console'               => 'PlaygroundCore\Service\Factory\ConsoleControllerFactory',
            'playgroundcore_admin_elfinder'        => 'PlaygroundCore\Service\Factory\AdminElfinderControllerFactory',
            'playgroundcore_admin_website'         => 'PlaygroundCore\Service\Factory\AdminWebsiteAdminControllerFactory',
            'playgroundcore_frontend_switchlocale' => 'PlaygroundCore\Service\Factory\FrontendSwitchLocaleControllerFactory',
            
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'recaptcha'    => \PlaygroundCore\Service\Factory\ControllerPluginRecaptchaFactory::class,
            'shortenUrl'    => \PlaygroundCore\Service\Factory\ControllerPluginShortenUrlFactory::class,
            'translate'       => \PlaygroundCore\Service\Factory\TranslateFactory::class,
            'translatePlural' => \PlaygroundCore\Service\Factory\TranslatePluralFactory::class,
        ),
    ),

    'playgroundLocale' => array(
        'enable' => false,
        'default' => 'fr',
        'strategies' => array(
            'uri',
            'cookie',
            'header'
        ),
        'supported' => array(
            'fr',
            'fr_FR'
        )
    ),

    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type'         => 'phpArray',
                'base_dir'     => __DIR__ . '/../../../../language',
                'pattern'      => 'routes_%s.php',
                'text_domain'  => 'routes'
            ),
            array(
                'type'         => 'phpArray',
                'base_dir'     => __DIR__ . '/../language',
                'pattern'      => 'routes_%s.php',
                'text_domain'  => 'routes'
            ),
            array(
                'type'         => 'phpArray',
                'base_dir'     => __DIR__ . '/../../../../language',
                'pattern'      => '%s.php',
                'text_domain'  => 'playgroundcore'
            ),
            array(
                'type'         => 'phpArray',
                'base_dir'     => __DIR__ . '/../language',
                'pattern'      => '%s.php',
                'text_domain'  => 'playgroundcore'
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view/admin',
            __DIR__ . '/../view/frontend',
        ),
    ),

    'validators' => array(
        'invokables' => array(
            'NotInBlacklist' => 'PlaygroundCore\Validator\Blacklist',
            'InMailDomainList' => 'PlaygroundCore\Validator\MailDomain',
         ),
    ),
);
