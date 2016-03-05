<?php

namespace PlaygroundCoreTest\Service;

use PlaygroundCoreTest\Bootstrap;
use PlaygroundCore\Entity\Cronjob;
use PlaygroundCore\Service\Registry;
use PlaygroundCore\Mapper\Cronjob as CronjobMapper;

class CronTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;
    
    protected $cronData;

    public function setUp()
    {
        parent::setUp();
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        
        $cronData1 = new Cronjob();
        $cronData1->setCode('TestCode1');
        $cronData1->setStatus('pending');
        $cronData1->setCreateTime(new \DateTime('2013-11-4'));
        $cronData1->setScheduleTime(new \DateTime('2013-11-4'));
        
        $cronData2 = new Cronjob();
        $cronData2->setCode('TestCode2');
        $cronData2->setStatus('pending');
        $cronData2->setCreateTime(new \DateTime('2013-11-5'));
        $cronData2->setScheduleTime(new \DateTime('2013-11-5'));
        
        
        $this->cronData[] = $cronData1;
        $this->cronData[] = $cronData2;
        
    }

    public function testSetCronjobs()
    {
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $this->assertInstanceOf('PlaygroundCore\Service\Cron', $cronService);

        $arrayCron[] = $this->cronData[0];
        $arrayCron[] = $this->cronData[1];
        
        $retour = $cronService->setCronjobs($arrayCron);
        $this->assertInstanceOf('PlaygroundCore\Service\Cron', $retour);
        
        $cronTest = $this->cronData[0];
        return $cronTest;
    }
    
    
    public function testGetCronjobs()
    {
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $arrayCron[] = $this->cronData[0];
        $arrayCron[] = $this->cronData[1];
        
        $retour = $cronService->getCronjobs($cronService->setCronjobs($arrayCron));
        $this->assertInternalType('array', $retour);
        $this->assertEquals(count($retour), 2);
        
        $retour = $cronService->getCronjobs($cronService->setCronjobs(null));
        $this->assertInternalType('array', $retour);
        $this->assertEquals(count($retour), 0);
    }
    
    public function testGetPendingWithNoPendingCron()
    {
        $arrayReturn = array();
        
        $entityManagerMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
        ->disableOriginalConstructor()
        ->getMock();
        
        $entityRepositoryMock = $this->getMockBuilder('PlaygroundCore\Mapper\Cronjob')
        ->disableOriginalConstructor()
        ->getMock();
        
        $entityRepositoryMock
        ->expects($this->once())
        ->method('getPending')
        ->will($this->returnValue($arrayReturn));
        
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $cronService->setEm($entityManagerMock);
        $cronService->getEm()
        ->expects($this->once())
        ->method('getRepository')
        ->will($this->returnValue($entityRepositoryMock));

        $retour = $cronService->getPending();
        $this->assertInternalType('array', $retour);
        $this->assertEmpty($retour);

    }
    
    public function testGetPendingWithCrons()
    {
        $arrayReturn = $this->cronData;
    
        $entityManagerMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
        ->disableOriginalConstructor()
        ->getMock();
    
        $entityRepositoryMock = $this->getMockBuilder('PlaygroundCore\Mapper\Cronjob')
        ->disableOriginalConstructor()
        ->getMock();
    
        $entityRepositoryMock
        ->expects($this->once())
        ->method('getPending')
        ->will($this->returnValue($arrayReturn));
    
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $cronService->setEm($entityManagerMock);
        $cronService->getEm()
        ->expects($this->once())
        ->method('getRepository')
        ->will($this->returnValue($entityRepositoryMock));
    
        $retour = $cronService->getPending();
        $this->assertInternalType('array', $retour);
        $this->assertEquals(count($retour), 2);
        $this->assertInstanceOf('PlaygroundCore\Entity\Cronjob', $retour[0]);
    
    }
    
    public function testResetPendingReturnValue()
    {
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService->resetPending();
        $this->assertInstanceOf('PlaygroundCore\Service\Cron', $retour);
    }
    
    /**
     * @depends testResetPendingReturnValue
     */
    public function testGetPendingAfterResetPending()
    {
        $arrayReturn = null;
        
        $entityManagerMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
        ->disableOriginalConstructor()
        ->getMock();
        
        $entityRepositoryMock = $this->getMockBuilder('PlaygroundCore\Mapper\Cronjob')
        ->disableOriginalConstructor()
        ->getMock();
        
        $entityRepositoryMock
        ->expects($this->once())
        ->method('getPending')
        ->will($this->returnValue($arrayReturn));
        
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $cronService->setEm($entityManagerMock);
        $cronService->getEm()
        ->expects($this->once())
        ->method('getRepository')
        ->will($this->returnValue($entityRepositoryMock));
        
        $retour = $cronService->getPending();
        $this->assertNull($retour);
    }
    
    /**
     * @depends testSetCronjobs
     */
    public function testTryLockJobWithCronPending($cronTest)
    {
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService->tryLockJob($cronTest);
        $this->assertTrue($retour);
        $this->assertEquals('running', $cronTest->getStatus());
    }
    
    /**
     * @depends testSetCronjobs
     */
    public function testTryLockJobWithNoCronPending($cronTest)
    {
        $cronTest->setStatus('error');
        
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService->tryLockJob($cronTest);
        $this->assertFalse($retour);
        $this->assertEquals('error', $cronTest->getStatus());
    }
    
    public function testExprToNumericWithIntAndCorrectValue(int $value = null)
    {
        $value = 1;
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService::exprToNumeric($value);
        
        $this->assertInternalType('int', $retour);
        $this->assertEquals($value, $retour);
    }
    
    public function testExprToNumericWithIntAndIncorrectValue(int $value = null)
    {
        $value = 32;
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService::exprToNumeric($value);
    
        $this->assertInternalType('bool', $retour);
        $this->assertFalse($retour);
    }
    
    public function testExprToNumericWithStringAndCorrectValue(string $value = null)
    {
        $value = 'jan';
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService::exprToNumeric($value);
    
        $this->assertInternalType('int', $retour);
        $this->assertEquals(1, $retour);
    }
    
    public function testExprToNumericWithStringAndIncorrectValue(string $value = null)
    {
        $value = 'false';
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService::exprToNumeric($value);
    
        $this->assertInternalType('bool', $retour);
        $this->assertFalse($retour);
    }
    
    public function testMatchTimeComponentHandleAllMatch(string $expr = null, int $num = null)
    {
        $expr = '*';
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService::matchTimeComponent($expr, $num);
        
        $this->assertInternalType('bool', $retour);
        $this->assertTrue($retour);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testMatchTimeComponentWithIncorrectModulusIncorrectNbArg(string $expr = null, int $num = null)
    {
        //Test de l'exception nb arg !=2
        $expr = '10/5/5';
        $cronService = $this->sm->get('playgroundcore_cron_service');

        $retour = $cronService::matchTimeComponent($expr, $num);

    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testMatchTimeComponentWithIncorrectModulusSecondArgumentNotInt(string $expr = null, int $num = null)
    {
        //Test de l'exception si le deuxieme argument n'est pas un int
        $expr = '10/"Bad Argument"';
        $cronService = $this->sm->get('playgroundcore_cron_service');
    
        $retour = $cronService::matchTimeComponent($expr, $num);
    
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testMatchTimeComponentWithIncorrectRangeIncorrectNbArg(string $expr = null, int $num = null)
    {
        //Test de l'exception nb arg !=2
        $expr = '10-59-1';
        $cronService = $this->sm->get('playgroundcore_cron_service');
        
        $retour = $cronService::matchTimeComponent($expr, $num);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testMatchTimeComponentWithIncorrectFromTo(string $expr = null, int $num = null)
    {
        $expr = '100';
        $cronService = $this->sm->get('playgroundcore_cron_service');
    
        $retour = $cronService::matchTimeComponent($expr, $num);
    }
    
    public function testMatchTimeComponentWithNumericAndIncorrectValue(string $expr = null, int $num = null)
    {
        $expr = '10-20/5';
        $num = 14;
        $cronService = $this->sm->get('playgroundcore_cron_service');
        
        $retour = $cronService::matchTimeComponent($expr, $num);
        $this->assertInternalType('bool', $retour);
        $this->assertFalse($retour);
    }
    
    public function testMatchTimeComponentWithNumericAndCorrectValue(string $expr = null, int $num = null)
    {
        $expr = '10-20/5';
        $num = 15;
        $cronService = $this->sm->get('playgroundcore_cron_service');
    
        $retour = $cronService::matchTimeComponent($expr, $num);
        $this->assertInternalType('bool', $retour);
        $this->assertTrue($retour);
    }
    
    public function testMatchTimeComponentWithNonNumericAndIncorrectValue(string $expr = null, int $num = null)
    {
        $expr = 'january-june';
        $num = 11;
        $cronService = $this->sm->get('playgroundcore_cron_service');
    
        $retour = $cronService::matchTimeComponent($expr, $num);
        $this->assertInternalType('bool', $retour);
        $this->assertFalse($retour);
    }
    
    public function testMatchTimeComponentWithNonNumericAndCorrectValue(string $expr = null, int $num = null)
    {
        $expr = 'january-june';
        $num = 3;
        $cronService = $this->sm->get('playgroundcore_cron_service');
    
        $retour = $cronService::matchTimeComponent($expr, $num);
        $this->assertInternalType('bool', $retour);
        $this->assertTrue($retour);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testMatchTimeWithMoreThanFiveArgs($time = null, string $expr = null)
    {
        $expr = '0-5,10-59/5 * 2-10,15-25 january-june/2 mon-fri OneMoreArgument';

        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService::matchTime($time, $expr);
    }
    
    public function testMatchTimeWithCorrectValue($time = null, string $expr = null)
    {
        $expr = '* * 2-10 january-june *'; // Toutes les heures, toutes les minutes du 2 au 10 entre le mois de janvier et juin
        $time = 1357640100; // 8 Janvier 2013 à 10h15
    
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService::matchTime($time, $expr);
        
        $this->assertInternalType('bool', $retour);
        $this->assertTrue($retour);
    }
    
    public function testMatchTimeWithIncorrectValue($time = null, string $expr = null)
    {
        $expr = '* * 2-10 january-june *'; // Toutes les heures, toutes les minutes du 2 au 10 entre le mois de janvier et juin
        $time = 1383905700; // 8 Novembre 2013 à 10h15
    
        $cronService = $this->sm->get('playgroundcore_cron_service');
        $retour = $cronService::matchTime($time, $expr);
    
        $this->assertInternalType('bool', $retour);
        $this->assertFalse($retour);
    }
    
    public function testRun()
    {
        $cronService = $this->getMockBuilder('PlaygroundCore\Service\Cron')
            ->setMethods(array('schedule', 'process', 'cleanup'))
            ->disableOriginalConstructor()
            ->getMock();
        
        $cronService
        ->expects($this->once())
        ->method('schedule')
        ->will($this->returnSelf());
        $cronService
        ->expects($this->once())
        ->method('process')
        ->will($this->returnSelf());
        $cronService
        ->expects($this->once())
        ->method('cleanup')
        ->will($this->returnSelf());
        
        $retour = $cronService->run();
        $this->assertInstanceOf('PlaygroundCore\Service\Cron', $retour);
    }
    
    public function testCleanup()
    {
        $cronService = $this->getMockBuilder('PlaygroundCore\Service\Cron')
            ->setMethods(array('recoverRunning', 'cleanLog'))
            ->disableOriginalConstructor()
            ->getMock();

        $cronService
        ->expects($this->once())
        ->method('recoverRunning')
        ->will($this->returnSelf());
        $cronService
        ->expects($this->once())
        ->method('cleanLog')
        ->will($this->returnSelf());
        
        $retour = $cronService->cleanup();
        $this->assertInstanceOf('PlaygroundCore\Service\Cron', $retour);
    }
}
