<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Repository\AppTemplateRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AppTemplateRepository::class)]
#[RunTestsInSeparateProcesses]
final class AppTemplateRepositoryTest extends AbstractRepositoryTestCase
{
    private AppTemplateRepository $repository;

    protected function onSetUp(): void
    {
        /** @var AppTemplateRepository $repository */
        $repository = self::getContainer()->get(AppTemplateRepository::class);
        $this->repository = $repository;
    }

    public function testRepositoryIsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    protected function createNewEntity(): object
    {
        $entity = new AppTemplate();
        $entity->setName('Test Template ' . uniqid());
        $entity->setDescription('Test template description for testing');
        $entity->setTags(['test', 'template']);
        $entity->setEnabled(true);
        $entity->setVersion('1.0.0');
        $entity->setIsLatest(false);
        $entity->setEnvironmentVariables(['ENV_VAR' => 'test_value']);

        return $entity;
    }

    /**
     * @return AppTemplateRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
