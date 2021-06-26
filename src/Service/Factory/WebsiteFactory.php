<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Website;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class WebsiteFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Website($container);

        return $service;
    }
}
