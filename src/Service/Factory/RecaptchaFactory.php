<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Recaptcha;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RecaptchaFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Service\Recaptcha
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Recaptcha($locator);

        return $service;
    }
}
