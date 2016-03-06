<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Image;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ImageFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Service\Image
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Image($locator);

        return $service;
    }
}
