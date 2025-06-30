<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Repository\AppInstanceRepository;

/**
 * AppInstanceRepository 测试类
 */
class AppInstanceRepositoryTest extends TestCase
{
    private AppInstanceRepository $repository;
    private ManagerRegistry $registry;

    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    public function testRepositoryExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    public function testRepositoryIsCallable(): void
    {
        $this->assertInstanceOf(AppInstanceRepository::class, $this->repository);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new AppInstanceRepository($this->registry);
    }
}
