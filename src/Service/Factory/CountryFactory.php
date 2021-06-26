<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Country;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class CountryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Country($container);

        return $service;
    }
}
