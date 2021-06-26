<?php

namespace PlaygroundCore\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\HeadMeta;

class TwitterCard extends AbstractHelper
{

    /**
     * @var \Laminas\View\Helper\HeadMeta
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
     * @var \Laminas\Stdlib\RequestInterface
     */
    protected $request;
    
    /**
     * @param \Laminas\View\Helper\HeadMeta $metaData
     * @return \Laminas\View\Helper\HeadMeta
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
        if (($this->request instanceof  \Laminas\Console\Request)) {
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
        
        foreach ($config->getTags() as $key => $value) {
            $container->appendProperty($key, $value);
        }
        
        // Mark this tag as rendered
        $this->rendered = true;
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
     * @param \Laminas\Stdlib\RequestInterface $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }
}
