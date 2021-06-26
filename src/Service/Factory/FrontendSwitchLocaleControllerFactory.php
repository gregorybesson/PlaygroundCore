<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Frontend\SwitchLocaleController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FrontendSwitchLocaleControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new SwitchLocaleController($container);

        return $controller;
    }
}
