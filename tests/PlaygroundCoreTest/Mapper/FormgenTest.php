<?php

namespace PlaygroundCoreTest\Mapper;

use PlaygroundCoreTest\Bootstrap;
use \PlaygroundCore\Entity\Formgen as FormgenEntity;

class FormgenTest extends \PHPUnit\Framework\TestCase
{
    protected $traceError = true;

    protected $formgenMapper;

    protected function setUp(): void
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
        // It has to work with 5.3.x and closure don't support direct $this referencing
        $self = $this;
        $this->em->transactional(function ($em) use ($self) {
            $formgen = new FormgenEntity();
            $formgen->setTitle('Titre de test');
            $formgen->setDescription('Description de test');
            $formgen->setFormjsonified('[{"dujsondetest"}]');
            $formgen->setFormtemplate('<li>test</li>');
            $self->getFormgenMapper()->insert($formgen);
        });

        $this->em->flush();
        $this->em->clear();

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 1);

        $this->em->transactional(function ($em) use ($self) {
            $formgen = new formgenEntity();
            $formgen->setTitle('Titre de test 2');
            $formgen->setDescription('Description de test 2');
            $formgen->setFormjsonified('[{"dujsondetest"}]');
            $formgen->setFormtemplate('<li>test</li>');
            $self->getFormgenMapper()->insert($formgen);
        });
        $this->em->flush();
        $this->em->clear();

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 2);
    }

    public function testFindBy()
    {

        $self = $this;
        $this->em->transactional(function ($em) use ($self) {
            $formgen = new FormgenEntity();
            $formgen->setTitle('Titre de test');
            $formgen->setDescription('Description de test');
            $formgen->setFormjsonified('[{"dujsondetest"}]');
            $formgen->setFormtemplate('<li>test</li>');
            $self->getFormgenMapper()->insert($formgen);
        });

        $this->em->flush();
        $this->em->clear();

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 1);

        $this->em->transactional(function ($em) use ($self) {
            $formgen = new formgenEntity();
            $formgen->setTitle('Titre de test 2');
            $formgen->setDescription('Description de test 2');
            $formgen->setFormjsonified('[{"dujsondetest"}]');
            $formgen->setFormtemplate('<li>test</li>');
            $self->getFormgenMapper()->insert($formgen);
        });
        $this->em->flush();
        $this->em->clear();

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 2);

        $formgens = $this->getFormgenMapper()->findBy(array('description'=>'Description de test 2'));
        $this->assertEquals(count($formgens), 1);
    }

    public function testFindById()
    {
        $self = $this;
        $this->em->transactional(function ($em) use ($self) {
            $formgen = new FormgenEntity();
            $formgen->setTitle('Titre de test');
            $formgen->setDescription('Description de test');
            $formgen->setFormjsonified('[{"dujsondetest"}]');
            $formgen->setFormtemplate('<li>test</li>');
            $self->getFormgenMapper()->insert($formgen);
        });
        $this->em->flush();
        $this->em->clear();

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 1);

        $self = $this;
        $this->em->transactional(function ($em) use ($self) {
            $formgen = new formgenEntity();
            $formgen->setTitle('Titre de test 2');
            $formgen->setDescription('Description de test 2');
            $formgen->setFormjsonified('[{"dujsondetest"}]');
            $formgen->setFormtemplate('<li>test</li>');
            $self->getFormgenMapper()->insert($formgen);
        });
        $this->em->flush();
        $this->em->clear();

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 2);
        $formgen = $formgens[0];

        $formgens = $this->getFormgenMapper()->findById($formgen->getId());
        $this->assertEquals(count([$formgens]), 1);
    }


    public function testUpdate()
    {
        $self = $this;
        $this->em->transactional(function ($em) use ($self) {
            $formgen = new FormgenEntity();
            $formgen->setTitle('Titre de test');
            $formgen->setDescription('Description de test');
            $formgen->setFormjsonified('[{"dujsondetest"}]');
            $formgen->setFormtemplate('<li>test</li>');
            $self->getFormgenMapper()->insert($formgen);
        });

        $this->em->flush();
        $this->em->clear();

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 1);

        $this->em->transactional(function ($em) use ($self) {
            $formgen2 = new formgenEntity();
            $formgen2->setTitle('Titre de test 2');
            $formgen2->setDescription('Description de test 2');
            $formgen2->setFormjsonified('[{"dujsondetest2"}]');
            $formgen2->setFormtemplate('<li>test2</li>');
            $self->getFormgenMapper()->insert($formgen2);
        });
        $this->em->flush();
        $this->em->clear();

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
        $self = $this;
        $this->em->transactional(function ($em) use ($self) {
            $formgen = new FormgenEntity();
            $formgen->setTitle('Titre de test');
            $formgen->setDescription('Description de test');
            $formgen->setFormjsonified('[{"dujsondetest"}]');
            $formgen->setFormtemplate('<li>test</li>');
            $self->getFormgenMapper()->insert($formgen);
        });

        $this->em->flush();
        $this->em->clear();

        $formgens = $this->getFormgenMapper()->findAll();
        $this->assertEquals(count($formgens), 1);

        $this->em->transactional(function ($em) use ($self) {
            $formgen = new formgenEntity();
            $formgen->setTitle('Titre de test 2');
            $formgen->setDescription('Description de test 2');
            $formgen->setFormjsonified('[{"dujsondetest"}]');
            $formgen->setFormtemplate('<li>test</li>');
            $self->getFormgenMapper()->insert($formgen);
        });

        $this->em->flush();
        $this->em->clear();

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

    protected function tearDown(): void
    {
        $dbh = $this->em->getConnection();
        unset($this->sm);
        unset($this->em);
        parent::tearDown();
    }
}
