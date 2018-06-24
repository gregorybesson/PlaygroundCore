<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Image;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class ImageFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Image($container);

        return $service;
    }
}
