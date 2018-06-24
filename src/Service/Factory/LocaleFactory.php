<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Locale;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class LocaleFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Locale($container);

        return $service;
    }
}
