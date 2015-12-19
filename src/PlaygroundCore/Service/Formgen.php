<?php

namespace PlaygroundCore\Service;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use PlaygroundCore\Options\ModuleOptions;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFactory;

class Formgen extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var formgenMapper
     */
    protected $formgenMapper;

     /**
     * @var localeService
     */
    protected $localeService;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;

    public function insert($data)
    {
        $formgen = new \PlaygroundCore\Entity\Formgen();
        $data = $this->getData($data);
        $formgen->populate($data);
        if (!empty($data['locale'])) {
            $formgen->setLocale($this->getLocaleService()->getLocaleMapper()->findById($data['locale']));
        }
        return $this->getFormgenMapper()->insert($formgen);
    }

    public function update($formgen, $data)
    {
        $data = $this->getData($data);
        $formgen->setTitle($data['title']);
        $formgen->setDescription($data['description']);
        $formgen->setFormjsonified($data['formjsonified']);
        $formgen->setFormTemplate($data['formtemplate']);
        if (!empty($data['locale'])) {
            $formgen->setLocale($this->getLocaleService()->getLocaleMapper()->findById($data['locale']));
        }
        return $this->getFormgenMapper()->update($formgen);
    }

    private function getData($data)
    {
        $title = '';
        $description = '';
        if (isset($data['form_jsonified']) && $data['form_jsonified']) {
            $jsonTmp = str_replace('\\', '_', $data['form_jsonified']);
            $jsonPV = json_decode($jsonTmp);
            foreach ($jsonPV as $element) {
                if ($element->form_properties) {
                    $attributes = $element->form_properties[0];
                    $title = $attributes->title;
                    $description = $attributes->description;
                    $locale = $attributes->locale;
                    break;
                }
            }
        }
        $return = array();
        $return['title'] = $title;
        $return['description'] = $description;
        $return['locale'] = isset($locale) ? $locale : null;
        $return['formjsonified'] = isset($data['form_jsonified']) ? $data['form_jsonified'] : null;
        $return['formtemplate'] = isset($data['form_template']) ? $data['form_template'] : null;
        $return['active'] = true;
        return $return;
    }

    public function getAttributes($attributes)
    {
        $a = array();

        $a['name']          = isset($attributes->name)? $attributes->name : '';
        $a['placeholder']   = isset($attributes->data->placeholder)? $attributes->data->placeholder : '';
        $a['label']         = isset($attributes->data->label)? $attributes->data->label : '';
        $a['required']      = (isset($attributes->data->required) && $attributes->data->required == 'true')?
            true:
            false;
        $a['class']         = isset($attributes->data->class)? $attributes->data->class : '';
        $a['id']            = isset($attributes->data->id)? $attributes->data->id : '';
        $a['lengthMin']     = isset($attributes->data->length)? $attributes->data->length->min : '';
        $a['lengthMax']     = isset($attributes->data->length)? $attributes->data->length->max : '';
        $a['validator']     = isset($attributes->data->validator)? $attributes->data->validator : '';
        $a['innerData']     = isset($attributes->data->innerData)? $attributes->data->innerData : array();
        $a['dropdownValues']= isset($attributes->data->dropdownValues)?
            $attributes->data->dropdownValues :
            array();
        $a['filesizeMin']   = isset($attributes->data->filesize)? $attributes->data->filesize->min : 0;
        $a['filesizeMax']   = isset($attributes->data->filesize)? $attributes->data->filesize->max : 10*1024*1024;

        return $a;
    }

    /**
     * @param \Zend\InputFilter\InputFilter $inputFilter
     */
    public function decorate($element, $attr, $inputFilter)
    {
        $factory = new InputFactory();
        $element->setName($attr['label']);
        $element->setLabel($attr['label']);
        $element->setAttributes(
            array(
                'placeholder'   => $attr['placeholder'],
                'required'      => $attr['required'],
                'class'         => $attr['class'],
                'id'            => $attr['id']
            )
        );

        $options = array();
        $options['encoding'] = 'UTF-8';
        if ($attr['lengthMin'] && $attr['lengthMin'] > 0) {
            $options['min'] = $attr['lengthMin'];
        }
        if ($attr['lengthMax'] && $attr['lengthMax'] > $attr['lengthMin']) {
            $options['max'] = $attr['lengthMax'];
            $element->setAttribute('maxlength', $attr['lengthMax']);
            $options['messages'] = array(
                \Zend\Validator\StringLength::TOO_LONG => sprintf(
                    $this->getServiceManager()->get('translator')->translate(
                        'This field contains more than %s characters',
                        'playgroundcore'
                    ),
                    $attr['lengthMax']
                )
            );
        }

        $validators = array(
            array(
                'name'    => 'StringLength',
                'options' => $options,
            ),
        );
        if ($attr['validator']) {
            $regex = "/.*\(([^)]*)\)/";
            preg_match($regex, $attr['validator'], $matches);
            $valArray = array(
                'name' => str_replace(
                    '('.$matches[1].')',
                    '',
                    $attr['validator']
                ),
                'options' => array($matches[1])
            );
            $validators[] = $valArray;
        }

        $inputFilter->add($factory->createInput(array(
            'name'     => $attr['name'],
            'required' => $attr['required'],
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => $validators,
        )));

        return $element;
    }

    public function render($formPV, $id)
    {
        $form = new Form();
        $form->setAttribute('id', $id);
        $inputFilter = new \Zend\InputFilter\InputFilter();
        $factory = new InputFactory();

        foreach ($formPV as $element) {
            if (isset($element->line_text)) {
                $attr  = $this->getAttributes($element->line_text[0]);
                $element = new Element\Text($attr['name']);
                $element = $this->decorate($element, $attr, $inputFilter);
                $form->add($element);
            }
            if (isset($element->line_password)) {
                $attr = $this->getAttributes($element->line_password[0]);
                $element = new Element\Password($attr['name']);
                $element = $this->decorate($element, $attr, $inputFilter);
                $form->add($element);
            }
            if (isset($element->line_hidden)) {
                $attr = $this->getAttributes($element->line_hidden[0]);
                $element = new Element\Hidden($attr['name']);
                $element = $this->decorate($element, $attr, $inputFilter);
                $form->add($element);
            }
            if (isset($element->line_email)) {
                $attr = $this->getAttributes($element->line_email[0]);
                $element = new Element\Email($attr['name']);
                $element = $this->decorate($element, $attr, $inputFilter);
                $form->add($element);
            }
            if (isset($element->line_radio)) {
                $attr = $this->getAttributes($element->line_radio[0]);
                $element = new Element\Radio($attr['name']);

                $element->setLabel($attr['label']);
                $element->setAttributes(
                    array(
                        'name'      => $attr['name'],
                        'required'  => $attr['required'],
                        'allowEmpty'=> !$attr['required'],
                        'class'     => $attr['class'],
                        'id'        => $attr['id']
                    )
                );
                $values = array();
                foreach ($attr['innerData'] as $value) {
                    $values[] = $value->label;
                }
                $element->setValueOptions($values);
        
                $options = array();
                $options['encoding'] = 'UTF-8';
                $options['disable_inarray_validator'] = true;
        
                $element->setOptions($options);
        
                $form->add($element);
        
                $inputFilter->add($factory->createInput(array(
                    'name'     => $attr['name'],
                    'required' => $attr['required'],
                    'allowEmpty' => !$attr['required'],
                )));
            }
            if (isset($element->line_checkbox)) {
                $attr = $this->getAttributes($element->line_checkbox[0]);
                $element = new Element\MultiCheckbox($attr['name']);
        
                $element->setLabel($attr['label']);
                $element->setAttributes(
                    array(
                        'name'      => $attr['name'],
                        'required'  => $attr['required'],
                        'allowEmpty'=> !$attr['required'],
                        'class'     => $attr['class'],
                        'id'        => $attr['id']
                    )
                );

                $values = array();
                foreach ($attr['innerData'] as $value) {
                    $values[] = $value->label;
                }
                $element->setValueOptions($values);
                $form->add($element);
        
                $options = array();
                $options['encoding'] = 'UTF-8';
                $options['disable_inarray_validator'] = true;
        
                $element->setOptions($options);
        
                $inputFilter->add($factory->createInput(array(
                    'name'      => $attr['name'],
                    'required'  => $attr['required'],
                    'allowEmpty'=> !$attr['required'],
                )));
            }
            if (isset($element->line_dropdown)) {
                $attr = $this->getAttributes($element->line_dropdown[0]);
                $element = new Element\Select($attr['name']);

                $element->setLabel($attr['label']);
                $element->setAttributes(
                    array(
                        'name'      => $attr['name'],
                        'required'  => $attr['required'],
                        'allowEmpty'=> !$attr['required'],
                        'class'     => $attr['class'],
                        'id'        => $attr['id']
                    )
                );
                $values = array();
                foreach ($attr['dropdownValues'] as $value) {
                    $values[] = $value->dropdown_label;
                }
                $element->setValueOptions($values);
                $form->add($element);
        
                $options = array();
                $options['encoding'] = 'UTF-8';
                $options['disable_inarray_validator'] = true;
        
                $element->setOptions($options);
        
                $inputFilter->add($factory->createInput(array(
                    'name'     => $attr['name'],
                    'required' => $attr['required'],
                    'allowEmpty' => !$attr['required'],
                )));
            }
            if (isset($element->line_paragraph)) {
                $attr = $this->getAttributes($element->line_paragraph[0]);
                $element = new Element\Textarea($attr['name']);
                $element = $this->decorate($element, $attr, $inputFilter);
                $form->add($element);
            }
            if (isset($element->line_upload)) {
                $attr = $this->getAttributes($element->line_upload[0]);
                $element = new Element\File($attr['name']);

                $element->setLabel($attr['label']);
                $element->setAttributes(
                    array(
                        'name'      => $attr['name'],
                        'required'  => $attr['required'],
                        'class'     => $attr['class'],
                        'id'        => $attr['id']
                    )
                );
                $form->add($element);
        
                $inputFilter->add($factory->createInput(array(
                    'name'     => $attr['name'],
                    'required' => $attr['required'],
                    'validators' => array(
                        array(
                            'name' => '\Zend\Validator\File\Size',
                            'options' => array('min' => $attr['filesizeMin'], 'max' => $attr['filesizeMax'])
                        ),
                        array(
                            'name' => '\Zend\Validator\File\Extension',
                            'options'  => array(
                                'png,PNG,jpg,JPG,jpeg,JPEG,gif,GIF',
                                'messages' => array(
                                    \Zend\Validator\File\Extension::FALSE_EXTENSION =>'Veuillez télécharger une image'
                                )
                            )
                        ),
                    ),
                )));
            }
        }

        $form->setInputFilter($inputFilter);

        return $form;
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


    public function getLocaleService()
    {
        if (null === $this->localeService) {
            $this->localeService = $this->getServiceManager()->get('playgroundcore_locale_service');
        }

        return $this->localeService;
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
