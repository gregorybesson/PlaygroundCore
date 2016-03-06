<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Frontend\SwitchLocaleController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FrontendSwitchLocaleControllerFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Controller\Frontend\SwitchLocaleController
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $controller = new SwitchLocaleController($locator);

        return $controller;
    }
}
