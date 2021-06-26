<?php

namespace PlaygroundCore\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ShortenUrl extends AbstractPlugin
{
    /**
     * @var ServiceLocator
     */
    protected $serviceLocator;

    /**
     * @var service
     */
    protected $service;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

    /**
     * Returns a shortened Url via bit.ly
     *
     * @param string $longUrl
     */
    public function shortenUrl($longUrl)
    {
        return $this->getService()->shortenUrl($longUrl);
    }

    /**
     * set service
     *
     * @param  $service
     * @return String
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * get mapper
     *
     * @return Service
     */
    public function getService()
    {
        if (!$this->service) {
            $this->setService($this->serviceLocator->get('playgroundcore_shortenurl_service'));
        }

        return $this->service;
    }
}
