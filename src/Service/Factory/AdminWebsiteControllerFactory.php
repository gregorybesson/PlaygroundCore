<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Admin\WebsiteController;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class AdminWebsiteControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $controller = new WebsiteController($container);

        return $controller;
    }
}
