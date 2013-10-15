<?php

namespace PlaygroundCore;

use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Validator\AbstractValidator;
use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\ModuleEvent;
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
            $locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);    
            $translator->setLocale($locale);
        
            // plugins
            $translate = $serviceManager->get('viewhelpermanager')->get('translate');
            $translate->getTranslator()->setLocale($locale);

            $options = $serviceManager->get('playgroundcore_module_options');
            $options->setLocale($locale);
        }
        // positionnement de la langue pour les traductions de date avec strftime
        setlocale(LC_TIME, "fr_FR", 'fr_FR.utf8', 'fra');

        AbstractValidator::setDefaultTranslator($translator,'playgroundcore');

        /**
         * Adding a Filter to slugify a string (make it URL compliiant)
         */
        $filterChain = new \Zend\Filter\FilterChain();
        $filterChain->getPluginManager()->setInvokableClass(
            'slugify', 'PlaygroundCore\Filter\Slugify'
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
        $e->getApplication()->getEventManager()->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER, function(\Zend\Mvc\MvcEvent $e) use ($serviceManager) {
            $view   = $serviceManager->get('ViewHelperManager');
            $plugin = $view->get('googleAnalytics');
            $plugin();

            $pluginOG = $view->get('facebookOpengraph');
            $pluginOG();
            
            $viewModel 		 = $e->getViewModel();
            $match			 = $e->getRouteMatch();
            $channel		 = isset($match)? $match->getParam('channel', ''):'';
            $viewModel->channel = $channel;
            foreach($viewModel->getChildren() as $child){
            	$child->channel = $channel;
            }
        });

        // Detect if the app is called from FB and store unencrypted signed_request
        $e->getApplication()->getEventManager()->attach("dispatch", function($e) {
       		$session = new Container('facebook');
       		$fb = $e->getRequest()->getPost()->get('signed_request');
       		if ($fb) {
       			list($encoded_sig, $payload) = explode('.', $fb, 2);
       			$sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
       			$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
        		$session->offsetSet('signed_request',  $data);
        	}
        },200);
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
                'QgCKEditor' => function ($sm) {
                    $config = $sm->getServiceLocator()->get('config');
                    $QuCk = new View\Helper\AdCKEditor($config['playgroundcore']['ckeditor']);

                    return $QuCk;
                },

                'googleAnalytics' => function($sm) {
                $tracker = $sm->getServiceLocator()->get('google-analytics');

                $helper  = new View\Helper\GoogleAnalytics($tracker, $sm->getServiceLocator()->get('Request'));

                return $helper;
                },
                
                'facebookOpengraph' => function($sm) {
                    $tracker = $sm->getServiceLocator()->get('facebook-opengraph');
                
                    $helper  = new View\Helper\FacebookOpengraph($tracker, $sm->getServiceLocator()->get('Request'));
                
                    return $helper;
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
                ),

                'shared' => array(
                    'playgroundcore_message' => false
                ),

                'invokables' => array(
                    'Zend\Session\SessionManager' => 'Zend\Session\SessionManager',
                    'playgroundcore_message'       => 'PlaygroundCore\Mail\Service\Message',
                    'playgroundcore_cron_service'  => 'PlaygroundCore\Service\Cron',
                    'playgroundcore_shortenurl_service'  => 'PlaygroundCore\Service\ShortenUrl',
                ),
                'factories' => array(
                    'playgroundcore_module_options' => function ($sm) {
                        $config = $sm->get('Configuration');

                        return new Options\ModuleOptions(isset($config['playgroundcore']) ? $config['playgroundcore'] : array());
                    },
                    'playgroundcore_transport' => 'PlaygroundCore\Mail\Transport\Service\TransportFactory',
                    'PlaygroundCore\Analytics\Tracker' => function($sm) {
                        $config = $sm->get('config');
                        $config = isset($config['playgroundcore']) ? $config['playgroundcore']['googleAnalytics'] : array('id' => 'UA-XXXXXXXX-X');

                        $tracker = new Analytics\Tracker($config['id']);

						if (isset($config['custom_vars'])) {
							foreach($config['custom_vars'] as $customVar) {
								$customVarId 		= $customVar['id'];
								$customVarName 		= $customVar['name'];
								$customVarValue 	= $customVar['value'];
								$customVarOptScope  = $customVar['optScope'];
								$customVar = new Analytics\CustomVar ($customVarId, $customVarName, $customVarValue, $customVarOptScope);
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
                    'PlaygroundCore\Opengraph\Tracker' => function($sm) {
                        $config = $sm->get('config');
                        $config = isset($config['playgroundcore']['facebookOpengraph']) ? $config['playgroundcore']['facebookOpengraph'] : array('appId' => '');
                    
                        $tracker = new Opengraph\Tracker($config['appId']);
                    
                        if (isset($config['enable'])) {
                            $tracker->setEnableOpengraph($config['enable']);
                        }
                        
                        if (isset($config['tags'])) {
                            foreach($config['tags'] as $type => $value) {
                                $tag = new Opengraph\Tag ($type, $value);
                                $tracker->addTag($tag);
                            }
                        }
                    
                        return $tracker;
                    },
                ),
        );
    }
}
