<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Admin\WebsiteController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdminWebsiteControllerFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Controller\Admin\WebsiteController
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $controller = new WebsiteController($locator);

        return $controller;
    }
}
