<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Admin\FormgenController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdminFormgenControllerFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Controller\Admin\FormgenController
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $controller = new FormgenController($locator);

        return $controller;
    }
}
