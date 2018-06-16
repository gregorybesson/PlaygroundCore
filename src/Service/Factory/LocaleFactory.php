<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Locale;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocaleFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Service\Locale
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Locale($locator);

        return $service;
    }
}
