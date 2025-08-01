<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Repository\AppPortConfigurationRepository;
use ServerApplicationBundle\Service\AppPortConfigurationService;

/**
 * AppPortConfigurationService单元测试
 */
class AppPortConfigurationServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AppPortConfigurationRepository $repository;
    private AppPortConfigurationService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(AppPortConfigurationRepository::class);
        $this->service = new AppPortConfigurationService($this->entityManager, $this->repository);
    }

    public function test_construct_withValidDependencies_createsServiceInstance(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(AppPortConfigurationRepository::class);
        $service = new AppPortConfigurationService($entityManager, $repository);

        $this->assertInstanceOf(AppPortConfigurationService::class, $service);
    }

    public function test_findAll_callsRepositoryFindAll(): void
    {
        $expectedConfigs = [new AppPortConfiguration(), new AppPortConfiguration()];
        
        $this->repository
            ->method('findAll')
            ->willReturn($expectedConfigs);

        $result = $this->service->findAll();

        $this->assertSame($expectedConfigs, $result);
    }

    public function test_find_withValidId_callsRepositoryFind(): void
    {
        $id = 'test-id';
        $expectedConfig = new AppPortConfiguration();
        
        $this->repository
            ->method('find')
            ->with($id)
            ->willReturn($expectedConfig);

        $result = $this->service->find($id);

        $this->assertSame($expectedConfig, $result);
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

    public function test_save_withValidConfig_persistsAndFlushes(): void
    {
        $config = new AppPortConfiguration();
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($config);
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->save($config);
        
        $this->assertTrue(true);
    }

    public function test_save_withFlushFalse_persistsWithoutFlush(): void
    {
        $config = new AppPortConfiguration();
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($config);
            
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->service->save($config, false);
        
        $this->assertTrue(true);
    }

    public function test_remove_withValidConfig_removesAndFlushes(): void
    {
        $config = new AppPortConfiguration();
        
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($config);
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->remove($config);
        
        $this->assertTrue(true);
    }

    public function test_remove_withFlushFalse_removesWithoutFlush(): void
    {
        $config = new AppPortConfiguration();
        
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($config);
            
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->service->remove($config, false);
        
        $this->assertTrue(true);
    }

    public function test_findByTemplate_withTemplate_returnsConfigurations(): void
    {
        $template = new AppTemplate();
        $expectedConfigs = [new AppPortConfiguration(), new AppPortConfiguration()];
        
        $this->repository
            ->method('findBy')
            ->with(['template' => $template])
            ->willReturn($expectedConfigs);

        $result = $this->service->findByTemplate($template);

        $this->assertSame($expectedConfigs, $result);
    }

    public function test_checkHealth_withTcpConnectType_returnsBooleanValue(): void
    {
        $config = new AppPortConfiguration();
        $config->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $config->setHealthCheckTimeout(1);
        
        // 测试一个通常不存在的端口
        $result = $this->service->checkHealth($config, 9999, '127.0.0.1');
        
        // 只验证返回值是布尔类型，不验证具体结果（因为端口可能存在或不存在）
        $this->assertFalse($result); // 端口 9999 通常是关闭的
    }

    public function test_checkHealth_withUdpPortCheckType_returnsBoolean(): void
    {
        $config = new AppPortConfiguration();
        $config->setHealthCheckType(HealthCheckType::UDP_PORT_CHECK);
        
        // 测试 UDP 端口检测
        $result = $this->service->checkHealth($config, 9999, '127.0.0.1');
        
        // UDP 由于其无连接特性，fsockopen 通常会成功
        $this->assertTrue($result);
    }

    public function test_checkHealth_withCommandType_returnsBasedOnExitCode(): void
    {
        $config = new AppPortConfiguration();
        $config->setHealthCheckType(HealthCheckType::COMMAND);
        $config->setHealthCheckConfig(['command' => 'true', 'successExitCode' => 0]);
        
        $result = $this->service->checkHealth($config, 80, 'localhost');
        
        $this->assertTrue($result); // true 命令总是返回退出码 0
    }
} 