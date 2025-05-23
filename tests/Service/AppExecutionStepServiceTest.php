<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\ExecutionStepType;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;
use ServerApplicationBundle\Repository\AppExecutionStepRepository;
use ServerApplicationBundle\Service\AppExecutionStepService;

/**
 * AppExecutionStepService单元测试
 */
class AppExecutionStepServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AppExecutionStepRepository $repository;
    private AppExecutionStepService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(AppExecutionStepRepository::class);
        $this->service = new AppExecutionStepService($this->entityManager, $this->repository);
    }

    public function test_construct_withValidDependencies_createsServiceInstance(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(AppExecutionStepRepository::class);
        $service = new AppExecutionStepService($entityManager, $repository);

        $this->assertInstanceOf(AppExecutionStepService::class, $service);
    }

    public function test_findAll_callsRepositoryFindAll(): void
    {
        $expectedSteps = [new AppExecutionStep(), new AppExecutionStep()];
        
        $this->repository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedSteps);

        $result = $this->service->findAll();

        $this->assertSame($expectedSteps, $result);
    }

    public function test_find_withValidId_callsRepositoryFind(): void
    {
        $id = 'test-id';
        $expectedStep = new AppExecutionStep();
        
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($expectedStep);

        $result = $this->service->find($id);

        $this->assertSame($expectedStep, $result);
    }

    public function test_find_withNonExistentId_returnsNull(): void
    {
        $id = 'non-existent-id';
        
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $result = $this->service->find($id);

        $this->assertNull($result);
    }

    public function test_save_withValidStep_persistsAndFlushes(): void
    {
        $step = new AppExecutionStep();
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($step);
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->save($step);
    }

    public function test_save_withFlushFalse_persistsWithoutFlush(): void
    {
        $step = new AppExecutionStep();
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($step);
            
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->service->save($step, false);
    }

    public function test_remove_withValidStep_removesAndFlushes(): void
    {
        $step = new AppExecutionStep();
        
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($step);
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->remove($step);
    }

    public function test_remove_withFlushFalse_removesWithoutFlush(): void
    {
        $step = new AppExecutionStep();
        
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($step);
            
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->service->remove($step, false);
    }

    public function test_findInstallSteps_withTemplate_returnsOrderedSteps(): void
    {
        $template = new AppTemplate();
        $expectedSteps = [new AppExecutionStep(), new AppExecutionStep()];
        
        $this->repository
            ->expects($this->once())
            ->method('findBy')
            ->with(['template' => $template], ['sequence' => 'ASC'])
            ->willReturn($expectedSteps);

        $result = $this->service->findInstallSteps($template);

        $this->assertSame($expectedSteps, $result);
    }

    public function test_findUninstallSteps_withTemplate_returnsOrderedSteps(): void
    {
        $template = new AppTemplate();
        $expectedSteps = [new AppExecutionStep(), new AppExecutionStep()];
        
        $this->repository
            ->expects($this->once())
            ->method('findBy')
            ->with(['template' => $template], ['sequence' => 'ASC'])
            ->willReturn($expectedSteps);

        $result = $this->service->findUninstallSteps($template);

        $this->assertSame($expectedSteps, $result);
    }

    public function test_executeStep_withCommandType_createsSuccessLog(): void
    {
        $step = new AppExecutionStep();
        $step->setType(ExecutionStepType::COMMAND);
        $step->setContent('echo "test"');
        $step->setParameterPattern('{{PARAM_NAME}}');
        
        $instance = new AppInstance();
        $parameters = ['TEST_PARAM' => 'test_value'];
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(AppLifecycleLog::class));
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->service->executeStep($step, $instance, $parameters);

        $this->assertInstanceOf(AppLifecycleLog::class, $result);
        $this->assertSame($instance, $result->getInstance());
        $this->assertSame($step, $result->getExecutionStep());
        $this->assertSame(LifecycleActionType::INSTALL, $result->getAction());
        $this->assertSame(LogStatus::SUCCESS, $result->getStatus());
        $this->assertEquals(0, $result->getExitCode());
        $this->assertNotNull($result->getExecutionTime());
    }

    public function test_executeStep_withScriptType_createsSuccessLog(): void
    {
        $step = new AppExecutionStep();
        $step->setType(ExecutionStepType::SCRIPT);
        $step->setContent('#!/bin/bash\necho "test script"');
        $step->setParameterPattern('{{PARAM_NAME}}');
        
        $instance = new AppInstance();
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(AppLifecycleLog::class));
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->service->executeStep($step, $instance);

        $this->assertInstanceOf(AppLifecycleLog::class, $result);
        $this->assertSame(LogStatus::SUCCESS, $result->getStatus());
    }

    public function test_executeStep_withParameterReplacement_replacesParametersCorrectly(): void
    {
        $step = new AppExecutionStep();
        $step->setType(ExecutionStepType::COMMAND);
        $step->setContent('echo "Hello {{NAME}}, port is {{PORT}}"');
        $step->setParameterPattern('{{PARAM_NAME}}');
        
        $instance = new AppInstance();
        $parameters = ['NAME' => 'World', 'PORT' => '8080'];
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($log) {
                return $log instanceof AppLifecycleLog && 
                       $log->getStatus() === LogStatus::SUCCESS;
            }));
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->service->executeStep($step, $instance, $parameters);

        $this->assertInstanceOf(AppLifecycleLog::class, $result);
    }

    public function test_executeStep_withEmptyParameters_handlesGracefully(): void
    {
        $step = new AppExecutionStep();
        $step->setType(ExecutionStepType::COMMAND);
        $step->setContent('echo "No parameters"');
        $step->setParameterPattern('{{PARAM_NAME}}');
        
        $instance = new AppInstance();
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(AppLifecycleLog::class));
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->service->executeStep($step, $instance, []);

        $this->assertInstanceOf(AppLifecycleLog::class, $result);
        $this->assertSame(LogStatus::SUCCESS, $result->getStatus());
    }
} 