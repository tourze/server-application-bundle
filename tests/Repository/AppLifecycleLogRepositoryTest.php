<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Repository\AppLifecycleLogRepository;

/**
 * AppLifecycleLogRepository 测试类
 */
class AppLifecycleLogRepositoryTest extends TestCase
{
    private AppLifecycleLogRepository $repository;
    private ManagerRegistry $registry;

    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    public function testGetClassName(): void
    {
        $this->assertTrue(class_exists(AppLifecycleLog::class));
    }

    public function testRepositoryIsCallable(): void
    {
        $this->assertInstanceOf(AppLifecycleLogRepository::class, $this->repository);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new AppLifecycleLogRepository($this->registry);
    }
}
