<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Enum\AppStatus;
use ServerApplicationBundle\Repository\AppInstanceRepository;

/**
 * 应用实例服务
 */
class AppInstanceService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AppInstanceRepository $appInstanceRepository,
    ) {
    }

    /**
     * 获取应用实例列表
     */
    public function findAll(): array
    {
        return $this->appInstanceRepository->findAll();
    }

    /**
     * 根据ID获取应用实例
     */
    public function find(string $id): ?AppInstance
    {
        return $this->appInstanceRepository->find($id);
    }

    /**
     * 保存应用实例
     */
    public function save(AppInstance $appInstance, bool $flush = true): void
    {
        $this->entityManager->persist($appInstance);
        
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 删除应用实例
     */
    public function remove(AppInstance $appInstance, bool $flush = true): void
    {
        $this->entityManager->remove($appInstance);
        
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 获取特定状态的应用实例
     */
    public function findByStatus(AppStatus $status): array
    {
        return $this->appInstanceRepository->findBy(['status' => $status]);
    }

    /**
     * 部署应用实例
     */
    public function deploy(AppInstance $appInstance): void
    {
        // 设置应用状态为安装中
        $appInstance->setStatus(AppStatus::INSTALLING);
        $this->save($appInstance);

        // TODO: 实际部署逻辑，调用AppExecutionService执行安装步骤
    }

    /**
     * 启动应用实例
     */
    public function start(AppInstance $appInstance): void
    {
        // 设置应用状态为运行中
        $appInstance->setStatus(AppStatus::RUNNING);
        $this->save($appInstance);

        // TODO: 实际启动逻辑
    }

    /**
     * 停止应用实例
     */
    public function stop(AppInstance $appInstance): void
    {
        // 设置应用状态为已停止
        $appInstance->setStatus(AppStatus::STOPPED);
        $this->save($appInstance);

        // TODO: 实际停止逻辑
    }

    /**
     * 卸载应用实例
     */
    public function uninstall(AppInstance $appInstance): void
    {
        // 设置应用状态为卸载中
        $appInstance->setStatus(AppStatus::UNINSTALLING);
        $this->save($appInstance);

        // TODO: 实际卸载逻辑，调用AppExecutionService执行卸载步骤
    }

    /**
     * 检查应用健康状态
     */
    public function checkHealth(AppInstance $appInstance): bool
    {
        // TODO: 实际健康检查逻辑，检查所有端口

        // 更新健康状态
        $appInstance->setLastHealthCheck(new \DateTime());
        $this->save($appInstance);

        return $appInstance->isHealthy();
    }
}
