<?php
namespace PlaygroundCore\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;

final class Country extends EventProvider implements ServiceManagerAwareInterface
{
    private $translatedTo;

    private $path;

    private $corePath;

    public function getTranslatedTo()
    {
        return $this->translatedTo;
    }

    public function setTranslatedTo($translatedTo)
    {
        $this->translatedTo = (string) $translatedTo;

        return $this;
    }

    public function getPath()
    {
        if(empty($this->path)){
            $this->path = str_replace('\\', '/', getcwd()) . '/language/countries';
        }

        return $this->path;
    }

    public function getCorePath()
    {
        if(empty($this->corePath)){
            $this->corePath = __DIR__ . '/../../../language/countries';
        }

        return $this->corePath;
    }

    public function getAllCountries($translatedTo = null)
    {
        if (null === $translatedTo) {
            $translatedTo = $this->getServiceManager()->get('translator')->getLocale();
        }
        
        $fileName = $this->getPath().'/'.$translatedTo.'.php';
        if (! file_exists($fileName)) {
            $fileName = $this->getCorePath().'/'.$translatedTo.'.php';

            if (! file_exists($fileName)) {
                throw new \InvalidArgumentException("Language $translatedTo not found.");
            }
        }

        return include $fileName;
    }

    public function getCountry($country, $translatedTo = null)
    {
        if (null === $translatedTo) {
            $translatedTo = $this->getServiceManager()->get('translator')->getLocale();
        }
        $fileName = $this->getPath().'/'.$translatedTo.'.php';
        if (! file_exists($fileName)) {
            $fileName = $this->getCorePath().'/'.$translatedTo.'.php';
            if (! file_exists($fileName)) {
                throw new \InvalidArgumentException("Language $translatedTo not found.");
            }
        }

        $list = include $fileName;
        if (!is_array($list)) {
            throw new \InvalidArgumentException("Language $translatedTo not found.");
        }
        $country = strtoupper($country);
        if (!array_key_exists($country, $list)) {
            throw new \InvalidArgumentException("Country $country not found for $translatedTo.");
        }

        return $list[$country];
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}
