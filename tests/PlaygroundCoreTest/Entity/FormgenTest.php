<?php

namespace PlaygroundCoreTest\Entity;

use PlaygroundCoreTest\Bootstrap;
use \PlaygroundCore\Entity\Formgen as FormgenEntity;

class FormgenTest extends \PHPUnit_Framework_TestCase
{

    protected $formgen;

    public function setUp()
    {

        $this->formgen = array(
            'title' => 'Titre de test',
            'description' => 'Description de test',
            'formjsonified'  => '[{"dujsondetest"}]',
            'formtemplate' => '<li>test</li>',
        );

        parent::setUp();
    }

    public function testPopulate() 
    {
        $formgenEntity = new FormgenEntity;
        $formgenEntity->populate($this->formgen);
        $this->assertEquals($this->formgen["title"], $formgenEntity->getTitle());
        $this->assertEquals($this->formgen["description"], $formgenEntity->getDescription());
        $this->assertEquals($this->formgen["formjsonified"], $formgenEntity->getFormjsonified());
        $this->assertEquals($this->formgen["formtemplate"], $formgenEntity->getFormtemplate());

        
    }

}