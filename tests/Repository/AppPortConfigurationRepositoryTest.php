<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Repository\AppPortConfigurationRepository;

/**
 * AppPortConfigurationRepository 测试类
 */
class AppPortConfigurationRepositoryTest extends TestCase
{
    private AppPortConfigurationRepository $repository;
    private ManagerRegistry|MockObject $registry;

    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    public function testGetClassName(): void
    {
        $this->assertTrue(class_exists(AppPortConfiguration::class));
    }

    public function testRepositoryIsCallable(): void
    {
        $this->assertInstanceOf(AppPortConfigurationRepository::class, $this->repository);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new AppPortConfigurationRepository($this->registry);
    }
}
