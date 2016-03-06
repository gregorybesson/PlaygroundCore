<?php
namespace PlaygroundCore\Mail\Service\Factory;

use PlaygroundCore\Mail\Service\Message;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MessageFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Service\Country
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Message($locator);

        return $service;
    }
}
