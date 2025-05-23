<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Enum\AppStatus;
use ServerApplicationBundle\Repository\AppInstanceRepository;
use ServerApplicationBundle\Service\AppInstanceService;

/**
 * AppInstanceService单元测试
 */
class AppInstanceServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AppInstanceRepository $repository;
    private AppInstanceService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(AppInstanceRepository::class);
        $this->service = new AppInstanceService($this->entityManager, $this->repository);
    }

    public function test_construct_withValidDependencies_createsServiceInstance(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(AppInstanceRepository::class);
        $service = new AppInstanceService($entityManager, $repository);

        $this->assertInstanceOf(AppInstanceService::class, $service);
    }

    public function test_findAll_callsRepositoryFindAll(): void
    {
        $expectedInstances = [new AppInstance(), new AppInstance()];
        
        $this->repository
            ->method('findAll')
            ->willReturn($expectedInstances);

        $result = $this->service->findAll();

        $this->assertSame($expectedInstances, $result);
    }

    public function test_find_withValidId_callsRepositoryFind(): void
    {
        $id = 'test-id';
        $expectedInstance = new AppInstance();
        
        $this->repository
            ->method('find')
            ->with($id)
            ->willReturn($expectedInstance);

        $result = $this->service->find($id);

        $this->assertSame($expectedInstance, $result);
    }

    public function test_find_withNonExistentId_returnsNull(): void
    {
        $id = 'non-existent-id';
        
        $this->repository
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $result = $this->service->find($id);

        $this->assertNull($result);
    }

    public function test_save_withValidInstance_persistsAndFlushes(): void
    {
        $instance = new AppInstance();
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($instance);
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->save($instance);
        
        // 验证方法被调用
        $this->assertTrue(true);
    }

    public function test_save_withFlushFalse_persistsWithoutFlush(): void
    {
        $instance = new AppInstance();
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($instance);
            
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->service->save($instance, false);
        
        // 验证方法被调用
        $this->assertTrue(true);
    }

    public function test_remove_withValidInstance_removesAndFlushes(): void
    {
        $instance = new AppInstance();
        
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($instance);
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->remove($instance);
        
        // 验证方法被调用
        $this->assertTrue(true);
    }

    public function test_remove_withFlushFalse_removesWithoutFlush(): void
    {
        $instance = new AppInstance();
        
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($instance);
            
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->service->remove($instance, false);
        
        // 验证方法被调用
        $this->assertTrue(true);
    }

    public function test_findByStatus_withValidStatus_returnsFilteredInstances(): void
    {
        $status = AppStatus::RUNNING;
        $expectedInstances = [new AppInstance(), new AppInstance()];
        
        $this->repository
            ->method('findBy')
            ->with(['status' => $status])
            ->willReturn($expectedInstances);

        $result = $this->service->findByStatus($status);

        $this->assertSame($expectedInstances, $result);
    }

    public function test_deploy_withValidInstance_setsStatusToInstalling(): void
    {
        $instance = new AppInstance();
        $instance->setStatus(AppStatus::STOPPED);
        $originalStatus = $instance->getStatus();
        
        $this->entityManager
            ->method('persist')
            ->with($instance);
            
        $this->entityManager
            ->method('flush');

        $this->service->deploy($instance);

        $this->assertSame(AppStatus::INSTALLING, $instance->getStatus());
        $this->assertNotSame($originalStatus, $instance->getStatus());
    }

    public function test_start_withValidInstance_setsStatusToRunning(): void
    {
        $instance = new AppInstance();
        $instance->setStatus(AppStatus::STOPPED);
        
        $this->entityManager
            ->method('persist')
            ->with($instance);
            
        $this->entityManager
            ->method('flush');

        $this->service->start($instance);

        $this->assertSame(AppStatus::RUNNING, $instance->getStatus());
    }

    public function test_stop_withValidInstance_setsStatusToStopped(): void
    {
        $instance = new AppInstance();
        $instance->setStatus(AppStatus::RUNNING);
        
        $this->entityManager
            ->method('persist')
            ->with($instance);
            
        $this->entityManager
            ->method('flush');

        $this->service->stop($instance);

        $this->assertSame(AppStatus::STOPPED, $instance->getStatus());
    }

    public function test_uninstall_withValidInstance_setsStatusToUninstalling(): void
    {
        $instance = new AppInstance();
        $instance->setStatus(AppStatus::RUNNING);
        
        $this->entityManager
            ->method('persist')
            ->with($instance);
            
        $this->entityManager
            ->method('flush');

        $this->service->uninstall($instance);

        $this->assertSame(AppStatus::UNINSTALLING, $instance->getStatus());
    }

    public function test_checkHealth_withValidInstance_updatesLastHealthCheck(): void
    {
        $instance = new AppInstance();
        $instance->setHealthy(true);
        $originalHealthCheckTime = $instance->getLastHealthCheck();
        
        $this->entityManager
            ->method('persist')
            ->with($instance);
            
        $this->entityManager
            ->method('flush');

        $result = $this->service->checkHealth($instance);

        $this->assertTrue($result);
        $this->assertNotNull($instance->getLastHealthCheck());
        $this->assertNotSame($originalHealthCheckTime, $instance->getLastHealthCheck());
    }

    public function test_checkHealth_withUnhealthyInstance_returnsFalse(): void
    {
        $instance = new AppInstance();
        $instance->setHealthy(false);
        
        $this->entityManager
            ->method('persist')
            ->with($instance);
            
        $this->entityManager
            ->method('flush');

        $result = $this->service->checkHealth($instance);

        $this->assertFalse($result);
        $this->assertNotNull($instance->getLastHealthCheck());
    }
} 