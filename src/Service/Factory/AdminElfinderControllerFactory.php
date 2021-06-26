<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Admin\ElfinderController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class AdminElfinderControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new ElfinderController($container);

        return $controller;
    }
}
