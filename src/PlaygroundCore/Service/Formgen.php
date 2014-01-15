<?php

namespace PlaygroundCore\Service;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\NotEmpty;
use ZfcBase\EventManager\EventProvider;
use PlaygroundCore\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Zend\Stdlib\ErrorHandler;

class Formgen extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var localeMapper
     */
    protected $formgenMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;

    public function insert($data) {
        $formgen = new \PlaygroundCore\Entity\Formgen();
        $data = $this->getData($data);
        $formgen->populate($data);
        return $this->getFormgenMapper()->insert($formgen);
    }

    private function getData($data) {
        $title = '';
        $description = '';
        if ($data['form_jsonified']) {
            $jsonTmp = str_replace('\\', '_', $data['form_jsonified']);
            $jsonPV = json_decode($jsonTmp);
            foreach ($jsonPV as $element) {
                if ($element->form_properties) {
                    $attributes = $element->form_properties[0];
                    $title = $attributes->title;
                    $description = $attributes->description;
                    break;
                }
            }
        }
        $return = array();
        $return['title'] = $title;
        $return['description'] = $description;
        $return['formjsonified'] = $data['form_jsonified'];
        $return['formtemplate'] = $data['form_template'];
        $return['active'] = true;
        return $return;
    }

    /**
     * getFormgenMapper
     *
     * @return FormgenMapper
     */
    public function getFormgenMapper()
    {
        if (null === $this->formgenMapper) {
            $this->formgenMapper = $this->getServiceManager()->get('playgroundcore_formgen_mapper');
        }

        return $this->formgenMapper;
    }

    /**
     * setFormgenMapper
     * @param  FormgenMapper $formgenMapper
     *
     * @return PlaygroundCore\Mapper\FormGen FormGen
     */
    public function setFormgenMapper($formgenMapper)
    {
        $this->formgenMapper = $formgenMapper;

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
            $this->setOptions($this->getServiceManager()->get('playgroundcore_module_options'));
        }

        return $this->options;
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