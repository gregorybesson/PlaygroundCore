<?php

namespace PlaygroundCore;

use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Validator\AbstractValidator;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface,
    ViewHelperProviderInterface
{

    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $config = $e->getApplication()->getServiceManager()->get('config');

        $translator = $serviceManager->get('translator');

        // Gestion de la locale
        if (PHP_SAPI !== 'cli') {
            //translator

            // Gestion locale pour le back
            if ($serviceManager->get('router')->match($serviceManager->get('request')) && strpos($serviceManager->get('router')->match($serviceManager->get('request'))->getMatchedRouteName(), 'admin') !==false) {
                if ($e->getRequest()->getCookie() && $e->getRequest()->getCookie()->offsetExists('pg_locale_back')) {
                    $locale = $e->getRequest()->getCookie()->offsetGet('pg_locale_back');
                }
            }

            if (empty($locale)) {
                if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                    $locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
                } else {
                    $locale = 'fr_FR';
                }
            }
            $translator->setLocale($locale);

            // plugins
            $translate = $serviceManager->get('viewhelpermanager')->get('translate');
            $translate->getTranslator()->setLocale($locale);

            $options = $serviceManager->get('playgroundcore_module_options');
            $options->setLocale($locale);
        }
        // positionnement de la langue pour les traductions de date avec strftime
        setlocale(LC_TIME, "fr_FR", 'fr_FR.utf8', 'fra');

        AbstractValidator::setDefaultTranslator($translator, 'playgroundcore');

        /*
         * Entity translation based on Doctrine Gedmo library
         */
        $doctrine = $serviceManager->get('doctrine.entitymanager.orm_default');
        $evm = $doctrine->getEventManager();

        $translatableListener = new \Gedmo\Translatable\TranslatableListener();
        $translatableListener->setDefaultLocale('fr_FR');
        // If no translation is found, fallback to entity data
        $translatableListener->setTranslationFallback(true);
        // set Locale
        if (!empty($locale)) {
            $translatableListener->setTranslatableLocale($locale);
        }

        $evm->addEventSubscriber($translatableListener);

        /**
         * Adding a Filter to slugify a string (make it URL compliiant)
         */
        $filterChain = new \Zend\Filter\FilterChain();
        $filterChain->getPluginManager()->setInvokableClass(
            'slugify',
            'PlaygroundCore\Filter\Slugify'
        );
        $filterChain->attach(new Filter\Slugify());

        // Start the session container
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($config['session']);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->start();

        /**
         * Optional: If you later want to use namespaces, you can already store the
         * Manager in the shared (static) Container (=namespace) field
         */
        \Zend\Session\Container::setDefaultManager($sessionManager);

        // Google Analytics : When the render event is triggered, we invoke the view helper to
        // render the javascript code.
        $e->getApplication()->getEventManager()->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER, function (\Zend\Mvc\MvcEvent $e) use ($serviceManager) {
            $view   = $serviceManager->get('ViewHelperManager');
            $plugin = $view->get('googleAnalytics');
            $plugin();

            $pluginOG = $view->get('facebookOpengraph');
            $pluginOG();
            
            $pluginTC = $view->get('twitterCard');
            $pluginTC();
        });


        if (PHP_SAPI !== 'cli') {
            $session = new Container('facebook');
            $fb = $e->getRequest()->getPost()->get('signed_request');
            if ($fb) {
                list($encoded_sig, $payload) = explode('.', $fb, 2);
                $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
                $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
                $session->offsetSet('signed_request', $data);

                // This fix exists only for safari on Windows : we need to redirect the user to the page outside of iframe
                // for the cookie to be accepted. Core just adds a 'redir_fb_page_id' var to alert controllers
                // that they need to send the user back to FB...

                if (!count($_COOKIE) > 0 && strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
                    echo '<script type="text/javascript">' .
                    'window.top.location.href = window.location.href+"?redir_fb_page_id='. $data["page"]["id"]. '";' .
                    '</script>';
                }

                // This fix exists only for IE6+, when this app is embedded into an iFrame : The P3P policy has to be set.
                $response = $e->getResponse();
                if ($response instanceof \Zend\Http\Response && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') || strpos($_SERVER['HTTP_USER_AGENT'], 'rv:11.'))) {
                    $response->getHeaders()->addHeaderLine('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
                }
            }
        }
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'QgCKEditor' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $config = $sm->getServiceLocator()->get('config');
                    $QuCk = new View\Helper\AdCKEditor($config['playgroundcore']['ckeditor']);

                    return $QuCk;
                },

                'googleAnalytics' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $tracker = $sm->getServiceLocator()->get('google-analytics');
    
                    $helper  = new View\Helper\GoogleAnalytics($tracker, $sm->getServiceLocator()->get('Request'));
    
                    return $helper;
                },

                'facebookOpengraph' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $tracker = $sm->getServiceLocator()->get('facebook-opengraph');

                    $helper  = new View\Helper\FacebookOpengraph($tracker, $sm->getServiceLocator()->get('Request'));

                    return $helper;
                },
                
                'twitterCard' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $viewHelper = new View\Helper\TwitterCard();
                    $viewHelper->setConfig($sm->getServiceLocator()->get('twitter-card'));
                    $viewHelper->setRequest($sm->getServiceLocator()->get('Request'));
                    return $viewHelper;
                },

                'switchLocaleWidget' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $viewHelper = new View\Helper\SwitchLocaleWidget();
                    $viewHelper->setLocaleService($sm->getServiceLocator()->get('playgroundcore_locale_service'));
                    $viewHelper->setWebsiteService($sm->getServiceLocator()->get('playgroundcore_website_service'));
                    $viewHelper->setRouteMatch($sm->getServiceLocator()->get('application')->getMvcEvent()->getRouteMatch());
                    return $viewHelper;
                },
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(

                'aliases' => array(
                    'playgroundcore_doctrine_em' => 'doctrine.entitymanager.orm_default',
                    'google-analytics'           => 'PlaygroundCore\Analytics\Tracker',
                    'facebook-opengraph'         => 'PlaygroundCore\Opengraph\Tracker',
                    'twitter-card'               => 'PlaygroundCore\TwitterCard\Config',
                    'twilio'                     => 'playgroundcore_twilio',
                    'ffmpeg'                     => 'playgroundcore_phpvideotoolkit'
                ),

                'shared' => array(
                    'playgroundcore_message' => false,
                    // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
                    'playgroundcore_ffmpeg_service' => false
                ),

                'invokables' => array(
                    'Zend\Session\SessionManager'        => 'Zend\Session\SessionManager',
                    'playgroundcore_message'             => 'PlaygroundCore\Mail\Service\Message',
                    'playgroundcore_cron_service'        => 'PlaygroundCore\Service\Cron',
                    'playgroundcore_shortenurl_service'  => 'PlaygroundCore\Service\ShortenUrl',
                    'playgroundcore_website_service'     => 'PlaygroundCore\Service\Website',
                    'playgroundcore_locale_service'      => 'PlaygroundCore\Service\Locale',
                    'playgroundcore_formgen_service'     => 'PlaygroundCore\Service\Formgen',
                    'playgroundcore_image_service'       => 'PlaygroundCore\Service\Image',
                    'playgroundcore_ffmpeg_service'      => 'PlaygroundCore\Service\Ffmpeg',
                ),
                'factories' => array(
                    'playgroundcore_module_options' => function (\Zend\ServiceManager\ServiceManager $sm) {
                        $config = $sm->get('Configuration');

                        return new Options\ModuleOptions(isset($config['playgroundcore']) ? $config['playgroundcore'] : array());
                    },

                    'playgroundcore_formgen_mapper' => function (\Zend\ServiceManager\ServiceManager $sm) {
                        return new Mapper\Formgen($sm->get('playgroundcore_doctrine_em'), $sm->get('playgroundcore_module_options'));
                    },

                    'playgroundcore_website_mapper' => function (\Zend\ServiceManager\ServiceManager $sm) {

                        return new Mapper\Website($sm->get('playgroundcore_doctrine_em'), $sm->get('playgroundcore_module_options'));
                    },

                    'playgroundcore_locale_mapper' => function (\Zend\ServiceManager\ServiceManager $sm) {
                        return new Mapper\Locale($sm->get('playgroundcore_doctrine_em'), $sm->get('playgroundcore_module_options'));
                    },

                    'playgroundcore_twilio' => 'PlaygroundCore\Service\Factory\TwilioServiceFactory',
                    'playgroundcore_phpvideotoolkit' => 'PlaygroundCore\Service\Factory\PhpvideotoolkitServiceFactory',
                    'playgroundcore_transport' => 'PlaygroundCore\Mail\Transport\Service\TransportFactory',
                    'PlaygroundCore\Analytics\Tracker' => function (\Zend\ServiceManager\ServiceManager $sm) {
                        $config = $sm->get('config');
                        $config = isset($config['playgroundcore']) ? $config['playgroundcore']['googleAnalytics'] : array('id' => 'UA-XXXXXXXX-X');

                        $tracker = new Analytics\Tracker($config['id']);

                        if (isset($config['custom_vars'])) {
                            foreach ($config['custom_vars'] as $customVar) {
                                $customVarId        = $customVar['id'];
                                $customVarName        = $customVar['name'];
                                $customVarValue    = $customVar['value'];
                                $customVarOptScope  = $customVar['optScope'];
                                $customVar = new Analytics\CustomVar($customVarId, $customVarName, $customVarValue, $customVarOptScope);
                                $tracker->addCustomVar($customVar);
                            }
                        }

                        if (isset($config['domain_name'])) {
                            $tracker->setDomainName($config['domain_name']);
                        }

                        if (isset($config['allow_linker'])) {
                            $tracker->setAllowLinker($config['allow_linker']);
                        }

                        if (isset($config['allow_hash'])) {
                            $tracker->setAllowHash($config['allow_hash']);
                        }

                        return $tracker;
                    },
                    'PlaygroundCore\Opengraph\Tracker' => function (\Zend\ServiceManager\ServiceManager $sm) {
                        $config = $sm->get('config');
                        $config = isset($config['playgroundcore']['facebookOpengraph']) ? $config['playgroundcore']['facebookOpengraph'] : array('appId' => '');

                        $tracker = new Opengraph\Tracker($config['appId']);

                        if (isset($config['enable'])) {
                            $tracker->setEnableOpengraph($config['enable']);
                        }

                        if (isset($config['tags'])) {
                            foreach ($config['tags'] as $type => $value) {
                                $tag = new Opengraph\Tag($type, $value);
                                $tracker->addTag($tag);
                            }
                        }

                        return $tracker;
                    },
                    'PlaygroundCore\TwitterCard\Config' => function (\Zend\ServiceManager\ServiceManager $sm) {
                        $config = $sm->get('config');
                        $config = isset($config['playgroundcore']['twitterCard']) ? $config['playgroundcore']['twitterCard'] : array();
                        return new TwitterCard\Config($config);
                    },
                ),
        );
    }
}
