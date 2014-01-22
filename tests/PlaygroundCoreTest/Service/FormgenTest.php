<?php
namespace PlaygroundCoreTest\Service;

use PlaygroundCoreTest\Bootstrap;
use \PlaygroundCore\Entity\Formgen as FormgenEntity;
use stdClass;

class FormgenTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;

    /**
     * Formegen sample
     * @var Array
     */
    protected $formgenData;

    public function setUp()
    {
        $this->formgenData = array(
            'title' => 'Titre de test',
            'description' => 'Description de test',
            'formjsonified'  => '[{"dujsondetest"}]',
            'formtemplate' => '<li>test</li>',
        );
        parent::setUp();
    }

    public function testCreateTrue()
    {
        $service = new \PlaygroundCore\Service\Formgen();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $formgenPostUpdate = new FormgenEntity;
        $formgenPostUpdate->populate($this->formgenData);

        $mapper = $this->getMockBuilder('PlaygroundCore\Mapper\Formgen')
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($formgenPostUpdate));
        $mapper->expects($this->any())
            ->method('update')
            ->will($this->returnValue($formgenPostUpdate));

        $service->setFormgenMapper($mapper);

        $formgenDataFromForm = $this->formgenData;
        $formgenDataFromForm['form_jsonified'] = '[{"form_properties":[{"name":"form_properties","namespace":"","title":"Titre du formulaire","description":"Description","class":"","model_name":"","id":"","class_name":""}]},{"line_text":[{"name":"text","type":"Zend\\Form\\Element\\Text","order":"1","data":{"placeholder":"Your civility...","label":"Civility","required":"0","class":"","id":"","length":{"min":"","max":""}}}]},{"line_text":[{"name":"text","type":"Zend\\Form\\Element\\Text","order":"2","data":{"placeholder":"Your firstname...","label":"Firstname","required":"0","class":"","id":"","length":{"min":"","max":""}}}]},{"line_text":[{"name":"text","type":"Zend\\Form\\Element\\Text","order":"3","data":{"placeholder":"Your lastname...","label":"Lastname","required":"0","class":"","id":"","length":{"min":"","max":""}}}]}]';
        $formgenDataFromForm['form_template'] = $this->formgenData['formtemplate'];

        $formgen = $service->insert($formgenDataFromForm);

        $this->assertEquals($this->formgenData['title'], $formgen->getTitle());
    }


    public function testRender(){
        $service = new \PlaygroundCore\Service\Formgen();
        $service->setServiceManager(Bootstrap::getServiceManager());

        $formGem = array(); 
        $element = new stdClass();
        $element->line_text = array( (object) array('name' => 'firstname',
                                     'type' => 'Zend\\Form\\Element\\Text"',
                                     'order' => '1',
                                     'data' => (object) array(
                                        'placeholder' => 'Your firstname...',
                                         'label' => 'firstname',
                                         'required' => '0',
                                         'class' => '',
                                         'id' => '',
                                         'length' => (object) array(
                                            'min' => '10',
                                            'max' => '20'
                                        )
                                    )
                                )
                            );
        $formGem[] = $element;

        $element = new stdClass();
        $element->line_email = array( (object) array('name' => 'email',
                                     'type' => 'Zend\\Form\\Element\\Mail"',
                                     'order' => '2',
                                     'data' => (object) array(
                                        'placeholder' => 'Your mail...',
                                         'label' => 'email',
                                         'required' => '0',
                                         'class' => '',
                                         'id' => '',
                                         'length' => (object) array(
                                            'min' => '10',
                                            'max' => '20'
                                        )
                                    )
                                )
                            );
        $formGem[] = $element;


        $element = new stdClass();
        $element->line_checkbox = array( (object) array('name' => 'optin',
                                     'type' => 'Zend\\Form\\Element\\Checkbox"',
                                     'order' => '3',
                                     'data' => (object) array(
                                        'placeholder' => '',
                                        'label' => 'optin',
                                        'required' => '0',
                                        'class' => '',
                                        'id' => '',
                                        'innerData'=> array(  (object) array(
                                            'label' => 'optin'
                                        )),
                                         'length' => (object) array(
                                            'min' => '',
                                            'max' =>''
                                        )
                                    )
                                )
                            );
        $formGem[] = $element;

        $element = new stdClass();
        $element->line_paragraph = array( (object) array('name' => 'comment',
                                     'type' => 'Zend\\Form\\Element\\TextArea"',
                                     'order' => '4',
                                     'data' => (object) array(
                                        'placeholder' => 'Your comment',
                                         'label' => 'comment',
                                         'required' => '0',
                                         'class' => '',
                                         'id' => '',
                                         'length' =>   (object) array(
                                            'min' => '10',
                                            'max' => '20'
                                        )
                                    )
                                )
                            );
        $formGem[] = $element;

        $element = new stdClass();
        $element->line_upload = array( (object) array('name' => 'file',
                                     'type' => 'Zend\\Form\\Element\\File"',
                                     'order' => '4',
                                     'data' => (object) array(
                                        'placeholder' => '',
                                         'label' => 'file',
                                         'required' => '0',
                                         'class' => '',
                                         'id' => '',
                                         'length' => (object) array(
                                            'min' => '',
                                            'max' =>''
                                        )
                                    )
                                )
                            );
        $formGem[] = $element;
        

        $form = $service->render($formGem, "formtest");

        $this->assertEquals(get_class($form), "Zend\Form\Form");
        $this->assertEquals($form->get('firstname')->getLabel(), "firstname");
        $this->assertEquals(get_class($form->get('firstname')), "Zend\Form\Element\Text");

        $this->assertEquals($form->get('email')->getLabel(), "email");
        $this->assertEquals(get_class($form->get('email')), "Zend\Form\Element\Email");
        $this->assertEquals($form->get('optin')->getLabel(), "optin");
        $this->assertEquals(get_class($form->get('optin')), "Zend\Form\Element\MultiCheckbox");
        $this->assertEquals($form->get('comment')->getLabel(), "comment");
        $this->assertEquals(get_class($form->get('comment')), "Zend\Form\Element\Textarea");
        $this->assertEquals($form->get('file')->getLabel(), "file");
        $this->assertEquals(get_class($form->get('file')), "Zend\Form\Element\File");
    }

    public function testSetFormgemMapper()
    {
        $service = new \PlaygroundCore\Service\Formgen();
        $service->setServiceManager(Bootstrap::getServiceManager());
        $service->setFormgenMapper($service->getServiceManager()->get('playgroundcore_formgen_mapper'));
        $this->assertEquals(get_class($service->getFormgenMapper()), "PlaygroundCore\Mapper\Formgen");
    }

    public function testGetFormgemMapper()
    {
        $service = new \PlaygroundCore\Service\Formgen();
        $service->setServiceManager(Bootstrap::getServiceManager());
        $this->assertEquals(get_class($service->getFormgenMapper()), "PlaygroundCore\Mapper\Formgen");
    }
    

    public function testSetOptions()
    {
        $service = new \PlaygroundCore\Service\Formgen();
        $service->setServiceManager(Bootstrap::getServiceManager());
        $service->setOptions($service->getServiceManager()->get('playgroundcore_module_options'));
        $this->assertEquals(get_class($service->getOptions()), "PlaygroundCore\Options\ModuleOptions");
    }

    public function testGetOptions()
    {
        $service = new \PlaygroundCore\Service\Formgen();
        $service->setServiceManager(Bootstrap::getServiceManager());
        $this->assertEquals(get_class($service->getOptions()), "PlaygroundCore\Options\ModuleOptions");
    }
}