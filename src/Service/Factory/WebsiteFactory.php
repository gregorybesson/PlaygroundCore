<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Website;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WebsiteFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Service\Website
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Website($locator);

        return $service;
    }
}
