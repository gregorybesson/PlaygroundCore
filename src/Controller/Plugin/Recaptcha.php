<?php

namespace PlaygroundCore\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;

class Recaptcha extends AbstractPlugin
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
     * Returns the Google ReCaptcha result
     *
     * @param string $response
     */
    public function recaptcha($response)
    {
        return $this->getService()->recaptcha($response);
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
            $this->setService($this->serviceLocator->get('playgroundcore_recaptcha_service'));
        }

        return $this->service;
    }
}
