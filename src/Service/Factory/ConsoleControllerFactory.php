<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\ConsoleController;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class ConsoleControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new ConsoleController($container);

        return $controller;
    }
}
