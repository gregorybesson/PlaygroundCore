<?php

namespace PlaygroundCore\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\HeadMeta;

class TwitterCard extends AbstractHelper
{
    
    /**
     * @var \Zend\Mvc\I18n\Translator
     */
    protected $translator;
    
    /**
     * @var \Zend\Mvc\Router\Http\RouteMatch
     */
    protected $routeMatch;
    
    /**
     * @var \Zend\View\Helper\HeadMeta
     */
    protected $headMeta;
    
    /**
     * @var string
     */
    protected $container = 'HeadMeta';
    
    protected $pluginContainer;
    
    /**
     * @var boolean
     */
    protected $rendered;
    
    /**
     * @var \PlaygroundCore\TwitterCard\Config
     */
    protected $config;
    
    /**
     * @var \Zend\Stdlib\RequestInterface
     */
    protected $request;
    
    /**
     * @param \Zend\View\Helper\HeadMeta $metaData
     * @return \Zend\View\Helper\HeadMeta
     */
    public function __invoke()
    {
        // Do not render the tag twice
        if ($this->rendered) {
            return;
        }
        
        // Do not render when tracker is disabled
        $config = $this->config;
        if (!$config || !$config->enabled()) {
            return;
        }
        
        // We return if we are in a console request
        if (($this->request instanceof  \Zend\Console\Request)) {
            return;
        }
        
        // We need to be sure $container->appendProperty() can be called
        $container = $this->view->plugin($this->getContainer());
        if (!$container instanceof HeadMeta) {
            throw new \RuntimeException(sprintf(
                'Container %s does not extend HeadMeta view helper',
                $this->getContainer()
            ));
        }
        
        $this->pluginContainer = $container;
        
        if (!$this->routeMatch) {
            return;
        }
        
        $name = $this->routeMatch->getMatchedRouteName();
        $this->addPropertyIfTranslated('twitter:card', 'Twitter Card');
        $this->addPropertyIfTranslated('twitter:site', 'Twitter Site');
        $this->addPropertyIfTranslated('twitter:title', 'Twitter ' . $name . ' Title');
        $this->addPropertyIfTranslated('twitter:description', 'Twitter ' . $name . ' Description');
        $this->addPropertyIfTranslated('twitter:image:src', 'Twitter ' . $name . ' Image Src');
        
        // Mark this tag as rendered
        $this->rendered = true;
    }
    
    /**
     * @param string $key
     * @param string $string
     * @param boolean $canUseDefault
     * @return \PlaygroundCore\View\Helper\TwitterCard
     */
    private function addPropertyIfTranslated($key, $string, $canUseDefault = true)
    {
        $value = $this->getTranslationIfExists($string);
        if (!$value || $value == $string || $value == ' ') {
            // get default insteed of translated if exists / alowed
            if ($canUseDefault && ($default = $this->config->getDefault($key))) {
                $value = $this->translator->translate($default);
            } else {
                return $this;
            }
        }
        $this->pluginContainer->appendProperty($key, $value);
        return $this;
    }
    
    /**
     * return the translated string if exists, false otherwise
     * @param string $string
     * @return string|false
     */
    private function getTranslationIfExists($string)
    {
        $translated = $this->translator->translate($string);
        if ($translated == $string || $translated == " ") {
            return false; // not translated ?
        }
        return $translated;
    }
    
    /**
     * @param \Zend\Mvc\I18n\Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * @param \Zend\Mvc\Router\Http\RouteMatch $routeMatch
     */
    public function setRouteMatch($routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }
    
    /**
     * @param string $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
    
    /**
     * @return string
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * @param \PlaygroundCore\TwitterCard\Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }
    
    /**
     * @param \Zend\Stdlib\RequestInterface $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

}
