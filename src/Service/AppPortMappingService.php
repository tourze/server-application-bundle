<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppPortMapping;
use ServerApplicationBundle\Repository\AppPortMappingRepository;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * 应用端口映射服务
 */
#[Autoconfigure(public: true)]
class AppPortMappingService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AppPortMappingRepository $appPortMappingRepository,
        private readonly AppPortConfigurationService $portConfigurationService,
    ) {
    }

    /**
     * 获取端口映射列表
     *
     * @return array<AppPortMapping>
     */
    public function findAll(): array
    {
        return $this->appPortMappingRepository->findAll();
    }

    /**
     * 根据ID获取端口映射
     */
    public function find(string $id): ?AppPortMapping
    {
        return $this->appPortMappingRepository->find($id);
    }

    /**
     * 保存端口映射
     */
    public function save(AppPortMapping $portMapping, bool $flush = true): void
    {
        $this->entityManager->persist($portMapping);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 删除端口映射
     */
    public function remove(AppPortMapping $portMapping, bool $flush = true): void
    {
        $this->entityManager->remove($portMapping);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 获取实例的所有端口映射
     *
     * @return array<AppPortMapping>
     */
    public function findByInstance(AppInstance $instance): array
    {
        return $this->appPortMappingRepository->findBy(['instance' => $instance]);
    }

    /**
     * 创建端口映射
     */
    public function createPortMapping(AppInstance $instance, AppPortConfiguration $configuration, int $actualPort): AppPortMapping
    {
        $portMapping = new AppPortMapping();
        $portMapping->setInstance($instance);
        $portMapping->setConfiguration($configuration);
        $portMapping->setActualPort($actualPort);
        $portMapping->setHealthy(false);

        $this->save($portMapping);

        return $portMapping;
    }

    /**
     * 为实例创建所有端口映射
     *
     * @return array<AppPortMapping>
     */
    public function createAllPortMappings(AppInstance $instance): array
    {
        $portConfigurations = $instance->getTemplate()->getPortConfigurations();
        $mappings = [];

        foreach ($portConfigurations as $config) {
            // 简单实现：使用配置的端口号作为实际端口号
            // 实际应用中可能需要检查端口可用性并动态分配
            $actualPort = $config->getPort();
            $mapping = $this->createPortMapping($instance, $config, $actualPort);
            $mappings[] = $mapping;
        }

        return $mappings;
    }

    /**
     * 检查端口映射健康状态
     */
    public function checkHealth(AppPortMapping $portMapping, string $host = 'localhost'): bool
    {
        $healthy = $this->portConfigurationService->checkHealth(
            $portMapping->getConfiguration(),
            $portMapping->getActualPort(),
            $host
        );

        $portMapping->setHealthy($healthy);
        $portMapping->setLastHealthCheck(new \DateTimeImmutable());
        $this->save($portMapping);

        return $healthy;
    }

    /**
     * 检查实例的所有端口映射健康状态
     */
    public function checkAllHealth(AppInstance $instance, string $host = 'localhost'): bool
    {
        $mappings = $this->findByInstance($instance);
        $allHealthy = true;

        foreach ($mappings as $mapping) {
            $healthy = $this->checkHealth($mapping, $host);
            if (!$healthy) {
                $allHealthy = false;
            }
        }

        return $allHealthy;
    }
}
