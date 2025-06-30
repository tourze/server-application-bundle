<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Integration\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Repository\AppExecutionStepRepository;

class AppExecutionStepRepositoryTest extends TestCase
{
    public function testRepositoryIsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, new AppExecutionStepRepository($this->createMock(\Doctrine\Persistence\ManagerRegistry::class)));
    }

    public function testRepositoryExists(): void
    {
        $this->assertTrue(class_exists(AppExecutionStepRepository::class));
    }
}