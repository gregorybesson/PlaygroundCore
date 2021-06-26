<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Recaptcha;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class RecaptchaFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Recaptcha($container);

        return $service;
    }
}
