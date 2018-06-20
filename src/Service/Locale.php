<?php

namespace PlaygroundCore\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\EventManagerAwareTrait;
use PlaygroundCore\Options\ModuleOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class Locale
{
    use EventManagerAwareTrait;

    /**
     * @var localeMapper
     */
    protected $localeMapper;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;

    /**
     *
     * @var ServiceManager
     */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

    /**
     * getLocaleMapper
     *
     * @return LocaleMapper
     */
    public function getLocaleMapper()
    {
        if (null === $this->localeMapper) {
            $this->localeMapper = $this->serviceLocator->get('playgroundcore_locale_mapper');
        }

        return $this->localeMapper;
    }

    /**
     * setLocaleMapper
     * @param  LocaleMapper $localeMapper
     *
     * @return PlaygroundCore\Entity\Locale Locale
     */
    public function setLocaleMapper($localeMapper)
    {
        $this->localeMapper = $localeMapper;

        return $this;
    }

    /**
     * setOptions
     * @param  ModuleOptions $options
     *
     * @return PlaygroundCore\Service\Locale $this
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * getOptions
     *
     * @return ModuleOptions $optins
     */
    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->serviceLocator->get('playgroundcore_module_options'));
        }

        return $this->options;
    }
}
