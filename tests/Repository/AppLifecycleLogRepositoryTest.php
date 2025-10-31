<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;
use ServerApplicationBundle\Repository\AppLifecycleLogRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AppLifecycleLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class AppLifecycleLogRepositoryTest extends AbstractRepositoryTestCase
{
    private AppLifecycleLogRepository $repository;

    protected function onSetUp(): void
    {
        /** @var AppLifecycleLogRepository $repository */
        $repository = self::getContainer()->get(AppLifecycleLogRepository::class);
        $this->repository = $repository;
    }

    public function testRepositoryIsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    protected function createNewEntity(): object
    {
        $entity = new AppLifecycleLog();
        $entity->setAction(LifecycleActionType::INSTALL);
        $entity->setStatus(LogStatus::SUCCESS);
        $entity->setMessage('Test lifecycle log message ' . uniqid());
        $entity->setCommandOutput('Test command output');
        $entity->setExitCode(0);
        $entity->setExecutionTime(1.5);

        return $entity;
    }

    /**
     * @return AppLifecycleLogRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
