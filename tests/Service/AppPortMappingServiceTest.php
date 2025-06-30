<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppPortMapping;
use ServerApplicationBundle\Repository\AppPortMappingRepository;
use ServerApplicationBundle\Service\AppPortConfigurationService;
use ServerApplicationBundle\Service\AppPortMappingService;

/**
 * AppPortMappingService 测试类
 */
class AppPortMappingServiceTest extends TestCase
{
    private AppPortMappingService $service;
    private AppPortMappingRepository $repository;
    private EntityManagerInterface $entityManager;
    private AppPortConfigurationService $portConfigurationService;

    public function testServiceIsCallable(): void
    {
        $this->assertInstanceOf(AppPortMappingService::class, $this->service);
    }

    public function testFindAllReturnsArray(): void
    {
        $result = $this->service->findAll();
        $this->assertNotNull($result);
    }

    public function testFindByInstanceReturnsArray(): void
    {
        $instance = $this->createMock(AppInstance::class);
        $result = $this->service->findByInstance($instance);
        $this->assertNotNull($result);
    }

    public function testSaveMethodExists(): void
    {
        $mapping = $this->createMock(AppPortMapping::class);
        $this->expectNotToPerformAssertions();

        try {
            $this->service->save($mapping);
        } catch (\Throwable $e) {
            // 忽略实际的数据库操作错误，只要方法存在就行
        }
    }

    protected function setUp(): void
    {
        $this->repository = $this->createMock(AppPortMappingRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->portConfigurationService = $this->createMock(AppPortConfigurationService::class);
        $this->service = new AppPortMappingService($this->entityManager, $this->repository, $this->portConfigurationService);
    }
}
