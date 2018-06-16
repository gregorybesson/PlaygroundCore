<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Formgen;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormgenFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Service\Formgen
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Formgen($locator);

        return $service;
    }
}
