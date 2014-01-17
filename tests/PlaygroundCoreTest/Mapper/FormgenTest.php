<?php

namespace PlaygroundCoreTest\Mapper;

use PlaygroundCoreTest\Bootstrap;
use \PlaygroundCore\Entity\Formgen as FormgenEntity;

class FormgenTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;

    protected $formgenMapper;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
        parent::setUp();
    }

    public function testInsert()
    {
        $formgen = new FormgenEntity();
        $formgen->setTitle('Titre de test');
        $formgen->setDescription('Description de test');
        $formgen->setFormjsonified('[{"dujsondetest"}]');
        $formgen->setFormtemplate('<li>test</li>');
        $this->getFormgenMapper()->insert($formgen);

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 1);

        $formgen = new formgenEntity();
        $formgen->setTitle('Titre de test 2');
        $formgen->setDescription('Description de test 2');
        $formgen->setFormjsonified('[{"dujsondetest"}]');
        $formgen->setFormtemplate('<li>test</li>');
        $this->getFormgenMapper()->insert($formgen);

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 2);
    }

    public function testFindBy(){

        $formgen = new FormgenEntity();
        $formgen->setTitle('Titre de test');
        $formgen->setDescription('Description de test');
        $formgen->setFormjsonified('[{"dujsondetest"}]');
        $formgen->setFormtemplate('<li>test</li>');
        $this->getFormgenMapper()->insert($formgen);

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 1);

        $formgen = new formgenEntity();
        $formgen->setTitle('Titre de test 2');
        $formgen->setDescription('Description de test 2');
        $formgen->setFormjsonified('[{"dujsondetest"}]');
        $formgen->setFormtemplate('<li>test</li>');
        $this->getFormgenMapper()->insert($formgen);

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 2);

        $formgens = $this->getFormgenMapper()->findBy(array('description'=>'Description de test 2'));
        $this->assertEquals(count($formgens), 1);
    }

    public function testFindById()
    {
        $formgen = new FormgenEntity();
        $formgen->setTitle('Titre de test');
        $formgen->setDescription('Description de test');
        $formgen->setFormjsonified('[{"dujsondetest"}]');
        $formgen->setFormtemplate('<li>test</li>');
        $this->getFormgenMapper()->insert($formgen);

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 1);

        $formgen = new formgenEntity();
        $formgen->setTitle('Titre de test 2');
        $formgen->setDescription('Description de test 2');
        $formgen->setFormjsonified('[{"dujsondetest"}]');
        $formgen->setFormtemplate('<li>test</li>');
        $this->getFormgenMapper()->insert($formgen);

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 2);
        $formgen = $formgens[0];

        $formgens = $this->getFormgenMapper()->findById($formgen->getId());
        $this->assertEquals(count($formgens), 1); 
    }


    public function testUpdate()
    {
        $formgen = new FormgenEntity();
        $formgen->setTitle('Titre de test');
        $formgen->setDescription('Description de test');
        $formgen->setFormjsonified('[{"dujsondetest"}]');
        $formgen->setFormtemplate('<li>test</li>');
        $this->getFormgenMapper()->insert($formgen);

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 1);

        $formgen = new formgenEntity();
        $formgen->setTitle('Titre de test 2');
        $formgen->setDescription('Description de test 2');
        $formgen->setFormjsonified('[{"dujsondetest"}]');
        $formgen->setFormtemplate('<li>test</li>');
        $this->getFormgenMapper()->insert($formgen);

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 2);
        $formgen = $formgens[0];


        $formgen->setTitle('Titre de test 3');
        $formgen = $this->getFormgenMapper()->update($formgen);

        $formgens = $this->getFormgenMapper()->findBy(array('title'=>'Titre de test 3'));
        $this->assertEquals(count($formgens), 1);


    }

    public function testRemove()
    {
        $formgen = new FormgenEntity();
        $formgen->setTitle('Titre de test');
        $formgen->setDescription('Description de test');
        $formgen->setFormjsonified('[{"dujsondetest"}]');
        $formgen->setFormtemplate('<li>test</li>');
        $this->getFormgenMapper()->insert($formgen);

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 1);

        $formgen = new formgenEntity();
        $formgen->setTitle('Titre de test 2');
        $formgen->setDescription('Description de test 2');
        $formgen->setFormjsonified('[{"dujsondetest"}]');
        $formgen->setFormtemplate('<li>test</li>');
        $this->getFormgenMapper()->insert($formgen);

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 2);
        
        foreach ($formgens as $formgen) {
           $this->getFormgenMapper()->remove($formgen); 
        }

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 0);
    }
  

       
    public function getFormgenMapper()
    {

        if (null === $this->formgenMapper) {
            $this->formgenMapper = $this->sm->get('playgroundcore_formgen_mapper');
        }

        return $this->formgenMapper;
    }

    public function tearDown()
    {
        $dbh = $this->em->getConnection();
        unset($this->sm);
        unset($this->em);
        parent::tearDown();
    }


}