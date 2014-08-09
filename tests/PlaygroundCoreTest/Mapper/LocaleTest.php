<?php

namespace PlaygroundCoreTest\Mapper;

use PlaygroundCoreTest\Bootstrap;
use \PlaygroundCore\Entity\Locale as localeEntity;

class LocaleTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;

    protected $localeMapper;

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
        $self = $this;
        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('French');
            $locale->setLocale('fr_FR');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });

        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $this->assertEquals(count($locales), 1);

        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('English');
            $locale->setLocale('en_UK');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });

        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $this->assertEquals(count($locales), 2);
    }

    public function testFindBy()
    {
        $self = $this;
        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('French');
            $locale->setLocale('fr_FR');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });

        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $this->assertEquals(count($locales), 1);

        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('English');
            $locale->setLocale('en_UK');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });
        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $this->assertEquals(count($locales), 2);

        $locales = $this->getLocaleMapper()->findBy(array('name'=>'English'));
        $this->assertEquals(count($locales), 1);
    }

    public function testFindById()
    {
        $self = $this;
        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('French');
            $locale->setLocale('fr_FR');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });

        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $this->assertEquals(count($locales), 1);

        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('English');
            $locale->setLocale('en_UK');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });

        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $this->assertEquals(count($locales), 2);
        $locale = $locales[0];

        $locales = $this->getLocaleMapper()->findById($locale->getId());
        $this->assertEquals(count($locales), 1); 
    }


    public function testUpdate()
    {
        $self = $this;
        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('French');
            $locale->setLocale('fr_FR');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });

        $this->em->flush();
        $this->em->clear();


        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('English');
            $locale->setLocale('en_UK');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });

        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $this->assertEquals(count($locales), 2);
        $locale = $locales[0];


        $locale->setName('French2');
        $locale = $this->getLocaleMapper()->update($locale);

        $locales = $this->getLocaleMapper()->findBy(array('name'=>'French2'));
        $this->assertEquals(count($locales), 1);


    }

    public function testRemove()
    {
        $self = $this;
        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('French');
            $locale->setLocale('fr_FR');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });

        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $this->assertEquals(count($locales), 1);

        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('English');
            $locale->setLocale('en_UK');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });

        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $this->assertEquals(count($locales), 2);
        
        foreach ($locales as $locale) {
            $this->getLocaleMapper()->remove($locale); 
        }

        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $this->assertEquals(count($locales), 0);
    }

    public function testGetFlag()
    {
        $self = $this;
        $this->em->transactional(function($em) use ($self) {
            $locale = new localeEntity();
            $locale->setName('French');
            $locale->setLocale('fr_FR');
            $locale->setActiveFront(1);
            $locale->setActiveBack(1);
            $self->getLocaleMapper()->insert($locale);
        });

        $this->em->flush();
        $this->em->clear();

        $locales = $this->getLocaleMapper()->findAll();
        $locale = $locales[0];
        $flag = $locale->getFlag();
        $this->assertEquals($flag, '/lib/images/flag/fr');
        
        $locale->setLocale('');
        $flag = $locale->getFlag();
        $this->assertEquals($flag, '');
    }
       
    public function getLocaleMapper()
    {

        if (null === $this->localeMapper) {
            $this->localeMapper = $this->sm->get('playgroundcore_locale_mapper');
        }

        return $this->localeMapper;
    }

    public function tearDown()
    {
        $dbh = $this->em->getConnection();
        unset($this->sm);
        unset($this->em);
        parent::tearDown();
    }


}