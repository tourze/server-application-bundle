<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppPortMapping;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;
use ServerApplicationBundle\Repository\AppPortMappingRepository;
use ServerApplicationBundle\Repository\AppTemplateRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AppPortMappingRepository::class)]
#[RunTestsInSeparateProcesses]
final class AppPortMappingRepositoryTest extends AbstractRepositoryTestCase
{
    private AppPortMappingRepository $repository;

    private AppTemplateRepository $templateRepository;

    protected function onSetUp(): void
    {
        /** @var AppPortMappingRepository $repository */
        $repository = self::getContainer()->get(AppPortMappingRepository::class);
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

        // 创建 AppPortConfiguration 实例
        $configuration = new AppPortConfiguration();
        $configuration->setTemplate($template);
        $configuration->setPort(8080 + random_int(1, 9999));
        $configuration->setProtocol(ProtocolType::TCP);
        $configuration->setDescription('Test port configuration ' . uniqid());
        $configuration->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $configuration->setHealthCheckConfig(['timeout' => 5]);
        $configuration->setHealthCheckInterval(60);
        $configuration->setHealthCheckTimeout(5);
        $configuration->setHealthCheckRetries(3);
        self::getEntityManager()->persist($configuration);
        self::getEntityManager()->flush();

        // 创建新的 AppPortMapping 实例
        $entity = new AppPortMapping();
        $entity->setConfiguration($configuration);
        $entity->setActualPort(30000 + random_int(1, 9999));
        $entity->setHealthy(true);
        $entity->setLastHealthCheck(new \DateTimeImmutable());

        return $entity;
    }

    /**
     * @return AppPortMappingRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
