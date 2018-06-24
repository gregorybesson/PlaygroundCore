<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\ShortenUrl;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class ShortenUrlFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new ShortenUrl($container);

        return $service;
    }
}
