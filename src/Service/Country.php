<?php
namespace PlaygroundCore\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

final class Country
{
    use EventManagerAwareTrait;

    private $translatedTo;

    private $path;

    private $corePath;

    /**
     *
     * @var ServiceManager
     */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

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
        if (empty($this->path)) {
            $this->path = str_replace('\\', '/', getcwd()) . '/language/countries';
        }

        return $this->path;
    }

    public function getCorePath()
    {
        if (empty($this->corePath)) {
            $this->corePath = __DIR__ . '/../../language/countries';
        }

        return $this->corePath;
    }

    public function getAllCountries($translatedTo = null)
    {
        if (null === $translatedTo) {
            $translatedTo = $this->serviceLocator->get('MvcTranslator')->getLocale();
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
            $translatedTo = $this->serviceLocator->get('MvcTranslator')->getLocale();
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
}
