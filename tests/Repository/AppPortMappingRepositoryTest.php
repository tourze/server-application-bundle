<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppPortMapping;
use ServerApplicationBundle\Repository\AppPortMappingRepository;

/**
 * AppPortMappingRepository 测试类
 */
class AppPortMappingRepositoryTest extends TestCase
{
    private AppPortMappingRepository $repository;
    private ManagerRegistry $registry;

    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    public function testGetClassName(): void
    {
        $this->assertTrue(class_exists(AppPortMapping::class));
    }

    public function testRepositoryIsCallable(): void
    {
        $this->assertInstanceOf(AppPortMappingRepository::class, $this->repository);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new AppPortMappingRepository($this->registry);
    }
}
