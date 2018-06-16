<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\ConsoleController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsoleControllerFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Controller\ConsoleController
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $controller = new ConsoleController($locator);

        return $controller;
    }
}
