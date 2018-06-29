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
use Zend\ModuleManager\ModuleManager;
use Zend\Uri\UriFactory;

class Module implements
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface,
    ViewHelperProviderInterface
{
    public function init(ModuleManager $manager)
    {
        $eventManager = $manager->getEventManager();
    
        /*
         * This event change the config before it's cached
        * The change will apply to 'template_path_stack' and 'assetic_configuration'
        * These 2 config take part in the Playground Theme Management
        */
        $eventManager->attach(\Zend\ModuleManager\ModuleEvent::EVENT_MERGE_CONFIG, array($this, 'onMergeConfig'), 100);
    }

    /**
     * This method is called only when the config is not cached.
     * @param \Zend\ModuleManager\ModuleEvent $e
     */
    public function onMergeConfig(\Zend\ModuleManager\ModuleEvent $e)
    {
        $config = $e->getConfigListener()->getMergedConfig(false);

        if (isset($config['playgroundLocale']) && isset($config['playgroundLocale']['enable']) && $config['playgroundLocale']['enable']) {
            $config['router']['routes']['frontend']['options']['route'] = '/[:locale[/]]';
            $config['router']['routes']['frontend']['options']['constraints']['locale'] = '[a-z]{2}([-_][A-Z]{2})?(?=/|$)';
            $config['router']['routes']['frontend']['options']['defaults']['locale'] = $config['playgroundLocale']['default'];
        }

        $e->getConfigListener()->setMergedConfig($config);
    }

    public function onBootstrap(EventInterface $e)
    {
        // this is useful for zfr-cors to accept chrome extension like Postman
        UriFactory::registerScheme('chrome-extension', 'Zend\Uri\Uri');

        $serviceManager = $e->getApplication()->getServiceManager();
        $config = $e->getApplication()->getServiceManager()->get('config');

        // Locale management
        $translator = $serviceManager->get('MvcTranslator');
        $defaultLocale = 'fr';

        // Gestion de la locale
        if (PHP_SAPI !== 'cli') {
            $config = $serviceManager->get('config');
            if (isset($config['playgroundLocale'])) {
                $pgLocale = $config['playgroundLocale'];
                $defaultLocale = $pgLocale['default'];

                if (isset($pgLocale['strategies'])) {
                    $pgstrat = $pgLocale['strategies'];

                    // Is there a locale in the URL ?
                    if (in_array('uri', $pgstrat)) {
                        $path = $e->getRequest()->getUri()->getPath();
                        $parts = explode('/', trim($path, '/'));
                        $localeCandidate = array_shift($parts);
                        // I switch from locale to... language
                        $localeCandidate = substr($localeCandidate, 0, 2);
                        
                        if (in_array($localeCandidate, $pgLocale['supported'])) {
                            $locale = $localeCandidate;
                        }
                    }

                    // Is there a cookie for the locale ?
                    if (empty($locale) && in_array('cookie', $pgstrat)) {
                        $serviceManager->get('router')->setTranslator($translator);
                        if ($serviceManager->get('router')->match($serviceManager->get('request')) &&
                            strpos($serviceManager->get('router')->match($serviceManager->get('request'))->getMatchedRouteName(), 'admin') !==false
                        ) {
                            if ($e->getRequest()->getCookie() &&
                                $e->getRequest()->getCookie()->offsetExists('pg_locale_back')
                            ) {
                                $locale = $e->getRequest()->getCookie()->offsetGet('pg_locale_back');
                            }
                        } else {
                            if ($e->getRequest()->getCookie() &&
                                $e->getRequest()->getCookie()->offsetExists('pg_locale_frontend')
                            ) {
                                $locale = $e->getRequest()->getCookie()->offsetGet('pg_locale_frontend');
                            }
                        }
                    }

                    // Is there a locale in the request Header ?
                    if (empty($locale) && in_array('header', $pgstrat)) {
                        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                            $localeCandidate = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
                            // I switch from locale to... language
                            $localeCandidate = substr($localeCandidate, 0, 2);
                            if (in_array($localeCandidate, $pgLocale['supported'])) {
                                $locale = $localeCandidate;
                            }
                        }
                    }
                }
                // I take the default locale
                if (empty($locale)) {
                    $locale = $defaultLocale;
                }
            }
            
            // I take the default locale
            if (empty($locale)) {
                $locale = $defaultLocale;
            }

            $translator->setLocale($locale);

            $e->getRouter()->setTranslator($translator);
            $e->getRouter()->setTranslatorTextDomain('routes');

            // Attach the translator to the plugins
            $translate = $serviceManager->get('ViewHelperManager')->get('translate');
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
        $translatableListener->setDefaultLocale($defaultLocale);
        
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
                $signedReq = explode('.', $fb, 2);
                $payload = $signedReq[1];
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

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'QgCKEditor' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $config = $sm->get('config');
                    $QuCk = new View\Helper\AdCKEditor($config['playgroundcore']['ckeditor']);

                    return $QuCk;
                },

                'googleAnalytics' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $tracker = $sm->get('google-analytics');
    
                    $helper  = new View\Helper\GoogleAnalytics($tracker, $sm->get('Request'));
    
                    return $helper;
                },

                'facebookOpengraph' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $tracker = $sm->get('facebook-opengraph');

                    $helper  = new View\Helper\FacebookOpengraph($tracker, $sm->get('Request'));

                    return $helper;
                },
                
                'twitterCard' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $viewHelper = new View\Helper\TwitterCard();
                    $viewHelper->setConfig($sm->get('twitter-card'));
                    $viewHelper->setRequest($sm->get('Request'));

                    return $viewHelper;
                },

                'switchLocaleWidget' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $viewHelper = new View\Helper\SwitchLocaleWidget();
                    $viewHelper->setLocaleService($sm->get('playgroundcore_locale_service'));
                    $viewHelper->setWebsiteService($sm->get('playgroundcore_website_service'));
                    $viewHelper->setRouteMatch($sm->get('Application')->getMvcEvent()->getRouteMatch());
                    
                    return $viewHelper;
                },

                'countryName' => function (\Zend\ServiceManager\ServiceManager $sm) {
                    $service = $sm->get('playgroundcore_country_service');
                    $viewHelper = new View\Helper\CountryName($service);

                    return $viewHelper;
                },
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(

                'shared' => array(
                    'playgroundcore_message' => false,
                    // don't want this service to be a singleton. I have to reset the ffmpeg parameters for each call.
                    'playgroundcore_ffmpeg_service' => false
                ),

                'factories' => array(
                    'playgroundcore_module_options' => function (\Zend\ServiceManager\ServiceManager $sm) {
                        $config = $sm->get('Configuration');

                        return new Options\ModuleOptions(isset($config['playgroundcore']) ? $config['playgroundcore'] : array());
                    },

                    'playgroundcore_formgen_mapper' => function (\Zend\ServiceManager\ServiceManager $sm) {
                        return new Mapper\Formgen(
                            $sm->get('playgroundcore_doctrine_em'),
                            $sm->get('playgroundcore_module_options'),
                            $sm
                        );
                    },

                    'playgroundcore_website_mapper' => function (\Zend\ServiceManager\ServiceManager $sm) {

                        return new Mapper\Website(
                            $sm->get('playgroundcore_doctrine_em'),
                            $sm->get('playgroundcore_module_options'),
                            $sm
                        );
                    },

                    'playgroundcore_locale_mapper' => function (\Zend\ServiceManager\ServiceManager $sm) {
                        return new Mapper\Locale(
                            $sm->get('playgroundcore_doctrine_em'),
                            $sm->get('playgroundcore_module_options'),
                            $sm
                        );
                    },
                    'PlaygroundCore\Analytics\Tracker' => function (\Zend\ServiceManager\ServiceManager $sm) {
                        $config = $sm->get('config');
                        $config = isset($config['playgroundcore']) ? $config['playgroundcore']['googleAnalytics'] : array('id' => 'UA-XXXXXXXX-X');

                        $tracker = new Analytics\Tracker($config['id']);

                        if (isset($config['enable_tracking'])) {
                            $tracker->setEnableTracking($config['enable_tracking']);
                        }

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

    public function getConsoleUsage(\Zend\Console\Adapter\Posix $console)
    {
        return array(
            'cron'  => 'call this command to enable cron tasks'
        );
    }
}
