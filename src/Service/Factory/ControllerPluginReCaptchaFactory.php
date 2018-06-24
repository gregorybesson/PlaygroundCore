<?php

namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Plugin\Recaptcha;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Service\AbstractPluginManagerFactory;

class ControllerPluginRecaptchaFactory extends AbstractPluginManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        return new Recaptcha($container);
    }
}
