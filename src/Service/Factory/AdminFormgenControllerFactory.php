<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Admin\FormgenController;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class AdminFormgenControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new FormgenController($container);

        return $controller;
    }
}
