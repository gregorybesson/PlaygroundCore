<?php
namespace PlaygroundCore\Mail\Service\Factory;

use PlaygroundCore\Mail\Service\Message;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class MessageFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Message($container);

        return $service;
    }
}
