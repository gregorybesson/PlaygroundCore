<?php

namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Plugin\ShortenUrl;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Service\AbstractPluginManagerFactory;

class ControllerPluginShortenUrlFactory extends AbstractPluginManagerFactory
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getController()->getServiceLocator();

        return new ShortenUrl($serviceLocator);
    }
}
