<?php

namespace PlaygroundCore\ZendDevTools\Collector;

use ZendDeveloperTools\Collector\CollectorInterface;
use ZendDeveloperTools\Collector\AutoHideInterface;

use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class TemplateHintCollector implements CollectorInterface, AutoHideInterface
{
    /**
     * Collector priority
     */
    const PRIORITY = 101;
    
    private $__data = null;

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'template_hint';
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
    	$session = new Container('zendDevTools');
    	 
    	if ($session->offsetExists('templateHint')) {
    		$this->__data = $session->offsetGet('templateHint');
    	}
    	$session->getManager()->getStorage()->clear('zendDevTools');
    }
    
    /**
     * {@inheritDoc}
     */
    public function canHide()
    {
    	return false;
    }
    
    /**
     * Returns the request method
     *
     * @return string
     */
    public function getTemplates()
    {
    	return $this->__data;
    }
    
	public function getTemplatesCount()
    {
    	return count($this->__data);
    }
    
}
