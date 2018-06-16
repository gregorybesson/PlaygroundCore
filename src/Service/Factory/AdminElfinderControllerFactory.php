<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Admin\ElfinderController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdminElfinderControllerFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Controller\Admin\ElfinderController
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $controller = new ElfinderController($locator);

        return $controller;
    }
}
