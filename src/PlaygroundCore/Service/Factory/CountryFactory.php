<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Country;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CountryFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Service\Country
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Country($locator);

        return $service;
    }
}
