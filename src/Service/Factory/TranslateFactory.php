<?php

namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Plugin\Translate;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Service\AbstractPluginManagerFactory;

class TranslateFactory extends AbstractPluginManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        if ($container->has('MvcTranslator')) {
            $translator = $container->get('MvcTranslator');
        } else {
            $serviceFactory = new TranslatorServiceFactory;
            $translator     = $serviceFactory($container);
        }
        return new Translate($translator);
    }
}
