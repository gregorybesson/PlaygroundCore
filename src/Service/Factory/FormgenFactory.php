<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Formgen;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FormgenFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Formgen($container);

        return $service;
    }
}
