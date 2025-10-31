<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;
use ServerApplicationBundle\Repository\AppPortConfigurationRepository;
use ServerApplicationBundle\Repository\AppTemplateRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AppPortConfigurationRepository::class)]
#[RunTestsInSeparateProcesses]
final class AppPortConfigurationRepositoryTest extends AbstractRepositoryTestCase
{
    private AppPortConfigurationRepository $repository;

    private AppTemplateRepository $templateRepository;

    protected function onSetUp(): void
    {
        /** @var AppPortConfigurationRepository $repository */
        $repository = self::getContainer()->get(AppPortConfigurationRepository::class);
        $this->repository = $repository;

        /** @var AppTemplateRepository $templateRepository */
        $templateRepository = self::getContainer()->get(AppTemplateRepository::class);
        $this->templateRepository = $templateRepository;
    }

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

        // 创建新的 AppPortConfiguration 实例
        $entity = new AppPortConfiguration();
        $entity->setTemplate($template);
        $entity->setPort(8080 + random_int(1, 9999));
        $entity->setProtocol(ProtocolType::TCP);
        $entity->setDescription('Test port configuration ' . uniqid());
        $entity->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $entity->setHealthCheckConfig(['timeout' => 5]);
        $entity->setHealthCheckInterval(60);
        $entity->setHealthCheckTimeout(5);
        $entity->setHealthCheckRetries(3);

        return $entity;
    }

    /**
     * @return AppPortConfigurationRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
