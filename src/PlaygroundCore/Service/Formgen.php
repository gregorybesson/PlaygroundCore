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


    public function render($formPV, $id)
    {
        $form = new Form();
        $form->setAttribute('id', $id);
        $inputFilter = new \Zend\InputFilter\InputFilter();
        $factory = new InputFactory();

        foreach ($formPV as $element) {
            if (isset($element->line_text)) {
                $attributes  = $element->line_text[0];
                $name        = isset($attributes->name)? $attributes->name : '';
                $type        = isset($attributes->type)? $attributes->type : '';
                $position    = isset($attributes->order)? $attributes->order : '';
                $placeholder = isset($attributes->data->placeholder)? $attributes->data->placeholder : '';
                $label       = isset($attributes->data->label)? $attributes->data->label : '';
                //$required    = ($attributes->data->required == 'true') ? true : false ;
                $required = false;
                $class       = isset($attributes->data->class)? $attributes->data->class : '';
                $id          = isset($attributes->data->id)? $attributes->data->id : '';
                $lengthMin   = isset($attributes->data->length)? $attributes->data->length->min : '';
                $lengthMax   = isset($attributes->data->length)? $attributes->data->length->max : '';

                $element = new Element\Text($name);
                $element->setName($label);
                $element->setLabel($label);
                $element->setAttributes(
                    array(
                        'placeholder'   => $placeholder,
                        'required'      => $required,
                        'class'         => $class,
                        'id'            => $id
                    )
                );


                $form->add($element);

                $options = array();
                $options['encoding'] = 'UTF-8';
                if ($lengthMin && $lengthMin > 0) {
                    $options['min'] = $lengthMin;
                }
                if ($lengthMax && $lengthMax > $lengthMin) {
                    $options['max'] = $lengthMax;
                    $element->setAttribute('maxlength', $lengthMax);
                    $options['messages'] = array(\Zend\Validator\StringLength::TOO_LONG => sprintf($this->getServiceManager()->get('translator')->translate('This field contains more than %s characters', 'playgroundgame'), $lengthMax));
                }
                $inputFilter->add($factory->createInput(array(
                    'name'     => $name,
                    'required' => $required,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name'    => 'StringLength',
                            'options' => $options,
                        ),
                    ),
                )));

            }
            if (isset($element->line_email)) {
                $attributes  = $element->line_email[0];
                $name        = isset($attributes->name)? $attributes->name : '';
                $type        = isset($attributes->type)? $attributes->type : '';
                $position    = isset($attributes->order)? $attributes->order : '';
                $placeholder = isset($attributes->data->placeholder)? $attributes->data->placeholder : '';
                $label       = isset($attributes->data->label)? $attributes->data->label : '';
                //$required    = ($attributes->data->required == 'true') ? true : false ;
                $required = false;
                $class       = isset($attributes->data->class)? $attributes->data->class : '';
                $id          = isset($attributes->data->id)? $attributes->data->id : '';
                $lengthMin   = isset($attributes->data->length)? $attributes->data->length->min : '';
                $lengthMax   = isset($attributes->data->length)? $attributes->data->length->max : '';

                $element = new Element\Email($name);
                $element->setLabel($label);
                $element->setName($label);
                $element->setAttributes(
                    array(
                        'placeholder'   => $placeholder,
                        'required'      => $required,
                        'class'         => $class,
                        'id'            => $id
                    )
                );
                $form->add($element);

                $options = array();
                $options['encoding'] = 'UTF-8';
                if ($lengthMin && $lengthMin > 0) {
                    $options['min'] = $lengthMin;
                }
                if ($lengthMax && $lengthMax > $lengthMin) {
                    $options['max'] = $lengthMax;
                    $element->setAttribute('maxlength', $lengthMax);
                    $options['messages'] = array(\Zend\Validator\StringLength::TOO_LONG => sprintf($this->getServiceManager()->get('translator')->translate('This field contains more than %s characters', 'playgroundgame'), $lengthMax));
                }
                $inputFilter->add($factory->createInput(array(
                    'name'     => $name,
                    'required' => $required,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name'    => 'StringLength',
                            'options' => $options,
                        ),
                    ),
                )));

            }
            if (isset($element->line_checkbox)) {
                $attributes  = $element->line_checkbox[0];
                $name        = isset($attributes->name)? $attributes->name : '';
                $type        = isset($attributes->type)? $attributes->type : '';
                $position    = isset($attributes->order)? $attributes->order : '';
                $label       = isset($attributes->data->label)? $attributes->data->label : '';

//                 $required    = ($attributes->data->required == 'yes') ? true : false;
                $required = false;
                $class       = isset($attributes->data->class)? $attributes->data->class : '';
                $id          = isset($attributes->data->id)? $attributes->data->id : '';
                $lengthMin   = isset($attributes->data->length)? $attributes->data->length->min : '';
                $lengthMax   = isset($attributes->data->length)? $attributes->data->length->max : '';
                $innerData   = isset($attributes->data->innerData)? $attributes->data->innerData : array();

                $element = new Element\MultiCheckbox($name);
                $element->setLabel($label);
                $element->setName($label);
                $element->setAttributes(
                    array(
                        'name'     => $name,
                        'required'      => $required,
                        'allowEmpty'    => !$required,
                        'class'         => $class,
                        'id'            => $id
                    )
                );
                $values = array();
                foreach($innerData as $value){
                    $values[] = $value->label;
                }
                $element->setValueOptions($values);
                $form->add($element);

                $options = array();
                $options['encoding'] = 'UTF-8';
                $inputFilter->add($factory->createInput(array(
                    'name'     => $name,
                    'required' => $required,
                    'allowEmpty' => !$required,
                )));

            }
            if (isset($element->line_paragraph)) {
                $attributes  = $element->line_paragraph[0];
                $name        = isset($attributes->name)? $attributes->name : '';
                $type        = isset($attributes->type)? $attributes->type : '';
                $position    = isset($attributes->order)? $attributes->order : '';
                $placeholder = isset($attributes->data->placeholder)? $attributes->data->placeholder : '';
                $label       = isset($attributes->data->label)? $attributes->data->label : '';
                $required    = ($attributes->data->required == 'true') ? true : false ;
                $class       = isset($attributes->data->class)? $attributes->data->class : '';
                $id          = isset($attributes->data->id)? $attributes->data->id : '';
                $lengthMin   = isset($attributes->data->length)? $attributes->data->length->min : '';
                $lengthMax   = isset($attributes->data->length)? $attributes->data->length->max : '';

                $element = new Element\Textarea($name);
                $element->setName($label);
                $element->setLabel($label);
                $element->setAttributes(
                    array(
                        'placeholder'   => $placeholder,
                        'required'      => $required,
                        'class'         => $class,
                        'id'            => $id
                    )
                );
                $form->add($element);

                $options = array();
                $options['encoding'] = 'UTF-8';
                if ($lengthMin && $lengthMin > 0) {
                    $options['min'] = $lengthMin;
                }
                if ($lengthMax && $lengthMax > $lengthMin) {
                    $options['max'] = $lengthMax;
                    $element->setAttribute('maxlength', $lengthMax);
                }
                $inputFilter->add($factory->createInput(array(
                    'name'     => $name,
                    'required' => $required,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name'    => 'StringLength',
                            'options' => $options,
                        ),
                    ),
                )));
            }
            if (isset($element->line_upload)) {
                $attributes  = $element->line_upload[0];
                //print_r($attributes);
                $name        = isset($attributes->name)? $attributes->name : '';
                $type        = isset($attributes->type)? $attributes->type : '';
                $position    = isset($attributes->order)? $attributes->order : '';
                $label       = isset($attributes->data->label)? $attributes->data->label : '';
                $required    = ($attributes->data->required == 'true') ? true : false ;
                $class       = isset($attributes->data->class)? $attributes->data->class : '';
                $id          = isset($attributes->data->id)? $attributes->data->id : '';
                $filesizeMin = isset($attributes->data->filesize)? $attributes->data->filesize->min : '';
                $filesizeMax = isset($attributes->data->filesize)? $attributes->data->filesize->max : '';
                $element = new Element\File($name);
                $element->setLabel($label);
                $element->setName($label);
                $element->setAttributes(
                    array(
                        'required'  => $required,
                        'class'     => $class,
                        'id'        => $id
                    )
                );
                $form->add($element);

                $inputFilter->add($factory->createInput(array(
                    'name'     => $name,
                    'required' => $required,
                    'validators' => array(
                            array('name' => '\Zend\Validator\File\Size', 'options' => array('max' => 10*1024*1024)),
                            array('name' => '\Zend\Validator\File\Extension', 'options'  => array('png,PNG,jpg,JPG,jpeg,JPEG,gif,GIF', 'messages' => array(
                            \Zend\Validator\File\Extension::FALSE_EXTENSION => 'Veuillez télécharger une image' ))
                        ),
                    ),
                )));

            }

             if (isset($element->line_radio)) {
                 $attributes  = $element->line_radio[0];
                $name        = isset($attributes->name)? $attributes->name : '';
                $type        = isset($attributes->type)? $attributes->type : '';
                $position    = isset($attributes->order)? $attributes->order : '';
                $label       = isset($attributes->data->label)? $attributes->data->label : '';

//                 $required    = ($attributes->data->required == 'yes') ? true : false;
                $required = false;
                $class       = isset($attributes->data->class)? $attributes->data->class : '';
                $id          = isset($attributes->data->id)? $attributes->data->id : '';
                $lengthMin   = isset($attributes->data->length)? $attributes->data->length->min : '';
                $lengthMax   = isset($attributes->data->length)? $attributes->data->length->max : '';
                $innerData   = isset($attributes->data->innerData)? $attributes->data->innerData : array();

                $element = new Element\Radio($name);
                $element->setLabel($label);
                $element->setName($label);

                $element->setAttributes(
                    array(
                        'name'     => $name,
                        'required'      => $required,
                        'allowEmpty'    => !$required,
                        'class'         => $class,
                        'id'            => $id
                    )
                );
                $values = array();
                foreach($innerData as $value){
                    $values[] = $value->label;
                }
                $element->setValueOptions($values);
                $form->add($element);
            }


            if (isset($element->line_dropdown)) {
                $attributes  = $element->line_dropdown[0];
                $name        = isset($attributes->name)? $attributes->name : '';
                $type        = isset($attributes->type)? $attributes->type : '';
                $position    = isset($attributes->order)? $attributes->order : '';
                $label       = isset($attributes->data->label)? $attributes->data->label : '';

//                 $required    = ($attributes->data->required == 'yes') ? true : false;
                $required = false;
                $class       = isset($attributes->data->class)? $attributes->data->class : '';
                $id          = isset($attributes->data->id)? $attributes->data->id : '';
                $lengthMin   = isset($attributes->data->length)? $attributes->data->length->min : '';
                $lengthMax   = isset($attributes->data->length)? $attributes->data->length->max : '';
                $dropdownValues   = isset($attributes->data->dropdownValues)? $attributes->data->dropdownValues : array();

                $element = new Element\Select($name);
                $element->setLabel($label);
                $element->setName($label);

                $element->setAttributes(
                    array(
                        'name'     => $name,
                        'required'      => $required,
                        'allowEmpty'    => !$required,
                        'class'         => $class,
                        'id'            => $id
                    )
                );
                $values = array();
                foreach($dropdownValues as $value){
                    $values[] = $value->dropdown_label;
                }
                $element->setValueOptions($values);
                $form->add($element);
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
