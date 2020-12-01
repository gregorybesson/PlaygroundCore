<?php
namespace PlaygroundCore\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * main class
 */
class Recaptcha
{
    use EventManagerAwareTrait;

    /**
     * @var ModuleOptions
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
     * This method calls Google ReCaptcha.
     * @param  unknown_type $url
     * @return unknown
     */
    public function recaptcha($response, $ipClient = null)
    {
        $platformSettings = $this->serviceLocator->get('playgrounddesign_settings_service')->getSettingsMapper()->findById(1);
        if ($this->getOptions()->getGRecaptchaKey() || ($platformSettings && $platformSettings->getGReCaptchaKey() !== null) ) {
            $rUrl = ($this->getOptions()->getGRecaptchaUrl() !== null) ? $this->getOptions()->getGRecaptchaUrl() : $platformSettings->getGReCaptchaUrl();
            $rKey = ($this->getOptions()->getGRecaptchaKey() !== null) ? $this->getOptions()->getGRecaptchaKey() : $platformSettings->getGReCaptchaKey();

            $client = new \Zend\Http\Client($rUrl);
            $client->setParameterPost(array(
                'secret'  => $rKey,
                'response' => $response,
                'remoteip'   => $ipClient,
            ));

            $client->setMethod(\Zend\Http\Request::METHOD_POST);
    
            $result = $client->send();
            if ($result) {
                $jsonResult = \Zend\Json\Json::decode($result->getBody());
                if ($jsonResult->success) {
                    return true;
                }
            }
        }

        return false;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions($this->serviceLocator->get('playgroundcore_module_options'));
        }

        return $this->options;
    }
}
