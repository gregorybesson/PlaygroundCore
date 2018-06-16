<?php

namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Controller\Plugin\Translate;
use Zend\I18n\Translator\TranslatorServiceFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Service\AbstractPluginManagerFactory;

class TranslateFactory extends AbstractPluginManagerFactory
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getController()->getServiceLocator();
        if ($serviceLocator->has('MvcTranslator')) {
            $translator = $serviceLocator->get('MvcTranslator');
        } else {
            $serviceFactory = new TranslatorServiceFactory();
            $translator     = $serviceFactory->createService($serviceLocator);
        }
        return new Translate($translator);
    }
}
