<?php

namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Plugin\ShortenUrl;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Service\AbstractPluginManagerFactory;

class ControllerPluginShortenUrlFactory extends AbstractPluginManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        return new ShortenUrl($container);
    }
}
