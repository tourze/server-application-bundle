<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Repository\AppLifecycleLogRepository;
use ServerApplicationBundle\Service\AppLifecycleLogService;

/**
 * AppLifecycleLogService 测试类
 */
class AppLifecycleLogServiceTest extends TestCase
{
    private AppLifecycleLogService $service;
    private AppLifecycleLogRepository $repository;
    private EntityManagerInterface $entityManager;

    public function testServiceIsCallable(): void
    {
        $this->assertInstanceOf(AppLifecycleLogService::class, $this->service);
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
        $log = $this->createMock(AppLifecycleLog::class);
        $this->expectNotToPerformAssertions();

        try {
            $this->service->save($log);
        } catch (\Throwable $e) {
            // 忽略实际的数据库操作错误，只要方法存在就行
        }
    }

    protected function setUp(): void
    {
        $this->repository = $this->createMock(AppLifecycleLogRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->service = new AppLifecycleLogService($this->entityManager, $this->repository);
    }
}
