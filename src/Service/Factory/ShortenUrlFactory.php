<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\ShortenUrl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ShortenUrlFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Service\ShortenUrl
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new ShortenUrl($locator);

        return $service;
    }
}
