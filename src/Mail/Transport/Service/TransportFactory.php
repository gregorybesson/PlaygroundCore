<?php
namespace PlaygroundCore\Mail\Transport\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class TransportFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $config = $container->get('Config');

        $transportOptions = (isset($config['playgroundcore']) ? $config['playgroundcore'] : array());

        if (!isset($transportOptions['transport_class'])) {
            throw new \Exception('Transport class has to be configured');
        }

        $transportClass = $transportOptions['transport_class'];
        $transport = new $transportClass();

        if (isset($transportOptions['options_class'])) {
            $optionsClass = $transportOptions['options_class'];
            $options = new $optionsClass($transportOptions['options']);
            $transport->setOptions($options);
        }

        return $transport;
    }
}
