<?php

namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Plugin\Recaptcha;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Service\AbstractPluginManagerFactory;

class ControllerPluginRecaptchaFactory extends AbstractPluginManagerFactory
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getController()->getServiceLocator();

        return new Recaptcha($serviceLocator);
    }
}
