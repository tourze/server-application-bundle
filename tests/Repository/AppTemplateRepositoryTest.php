<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Repository\AppTemplateRepository;

/**
 * AppTemplateRepository 测试类
 */
class AppTemplateRepositoryTest extends TestCase
{
    private AppTemplateRepository $repository;
    private ManagerRegistry $registry;

    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    public function testGetClassName(): void
    {
        $this->assertTrue(class_exists(AppTemplate::class));
    }

    public function testRepositoryIsCallable(): void
    {
        $this->assertInstanceOf(AppTemplateRepository::class, $this->repository);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new AppTemplateRepository($this->registry);
    }
}
