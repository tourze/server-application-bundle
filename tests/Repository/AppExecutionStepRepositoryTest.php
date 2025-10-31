<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\ExecutionStepType;
use ServerApplicationBundle\Repository\AppExecutionStepRepository;
use ServerApplicationBundle\Repository\AppTemplateRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AppExecutionStepRepository::class)]
#[RunTestsInSeparateProcesses]
final class AppExecutionStepRepositoryTest extends AbstractRepositoryTestCase
{
    private AppExecutionStepRepository $repository;

    private AppTemplateRepository $templateRepository;

    public function testRepositoryIsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    protected function createNewEntity(): object
    {
        // 首先获取或创建一个 AppTemplate 实例
        $templates = $this->templateRepository->findAll();

        if ([] === $templates) {
            // 如果没有模板，创建一个简单的模板
            $template = new AppTemplate();
            $template->setName('Test Template');
            $template->setDescription('Test template for testing');
            $template->setVersion('1.0.0');
            $template->setIsLatest(false);
            self::getEntityManager()->persist($template);
            self::getEntityManager()->flush();
        } else {
            $template = $templates[0];
        }

        // 创建新的 AppExecutionStep 实例
        $entity = new AppExecutionStep();
        $entity->setTemplate($template);
        $entity->setSequence(1);
        $entity->setName('Test Step ' . uniqid());
        $entity->setType(ExecutionStepType::COMMAND);
        $entity->setContent('echo "test"');
        $entity->setDescription('Test description');

        return $entity;
    }

    /**
     * @return AppExecutionStepRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function onSetUp(): void
    {
        /** @var AppExecutionStepRepository $repository */
        $repository = self::getContainer()->get(AppExecutionStepRepository::class);
        $this->repository = $repository;

        /** @var AppTemplateRepository $templateRepository */
        $templateRepository = self::getContainer()->get(AppTemplateRepository::class);
        $this->templateRepository = $templateRepository;
    }
}
