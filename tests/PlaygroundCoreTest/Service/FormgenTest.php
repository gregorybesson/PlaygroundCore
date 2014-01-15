<?php
namespace PlaygroundCoreTest\Service;

use PlaygroundCoreTest\Bootstrap;
use \PlaygroundCore\Entity\Formgen as FormgenEntity;

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
    
}