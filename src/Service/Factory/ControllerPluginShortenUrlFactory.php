<?php

namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Plugin\ShortenUrl;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Service\AbstractPluginManagerFactory;

class ControllerPluginShortenUrlFactory extends AbstractPluginManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        return new ShortenUrl($container);
    }
}
