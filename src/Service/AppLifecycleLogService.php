<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;
use ServerApplicationBundle\Repository\AppLifecycleLogRepository;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * 应用生命周期日志服务
 */
#[Autoconfigure(public: true)]
class AppLifecycleLogService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AppLifecycleLogRepository $appLifecycleLogRepository,
    ) {
    }

    /**
     * 获取生命周期日志列表
     *
     * @return array<AppLifecycleLog>
     */
    public function findAll(): array
    {
        return $this->appLifecycleLogRepository->findAll();
    }

    /**
     * 根据ID获取生命周期日志
     */
    public function find(string $id): ?AppLifecycleLog
    {
        return $this->appLifecycleLogRepository->find($id);
    }

    /**
     * 保存生命周期日志
     */
    public function save(AppLifecycleLog $lifecycleLog, bool $flush = true): void
    {
        $this->entityManager->persist($lifecycleLog);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 删除生命周期日志
     */
    public function remove(AppLifecycleLog $lifecycleLog, bool $flush = true): void
    {
        $this->entityManager->remove($lifecycleLog);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 获取实例的所有生命周期日志
     *
     * @return array<AppLifecycleLog>
     */
    public function findByInstance(AppInstance $instance): array
    {
        return $this->appLifecycleLogRepository->findBy(
            ['instance' => $instance],
            ['createTime' => 'DESC']
        );
    }

    /**
     * 获取实例的特定操作类型日志
     *
     * @return array<AppLifecycleLog>
     */
    public function findByInstanceAndAction(AppInstance $instance, LifecycleActionType $action): array
    {
        return $this->appLifecycleLogRepository->findBy(
            ['instance' => $instance, 'action' => $action],
            ['createTime' => 'DESC']
        );
    }

    /**
     * 创建操作日志
     */
    public function createLog(
        AppInstance $instance,
        LifecycleActionType $action,
        LogStatus $status,
        ?string $message = null,
        ?string $commandOutput = null,
        ?int $exitCode = null,
        ?float $executionTime = null,
    ): AppLifecycleLog {
        $log = new AppLifecycleLog();
        $log->setInstance($instance);
        $log->setAction($action);
        $log->setStatus($status);
        $log->setMessage($message);
        $log->setCommandOutput($commandOutput);
        $log->setExitCode($exitCode);
        $log->setExecutionTime($executionTime);

        $this->save($log);

        return $log;
    }

    /**
     * 创建安装开始日志
     */
    public function logInstallStart(AppInstance $instance): AppLifecycleLog
    {
        return $this->createLog(
            $instance,
            LifecycleActionType::INSTALL,
            LogStatus::SUCCESS,
            '开始安装应用'
        );
    }

    /**
     * 创建安装完成日志
     */
    public function logInstallComplete(AppInstance $instance, bool $success): AppLifecycleLog
    {
        return $this->createLog(
            $instance,
            LifecycleActionType::INSTALL,
            $success ? LogStatus::SUCCESS : LogStatus::FAILED,
            $success ? '安装完成' : '安装失败'
        );
    }

    /**
     * 创建健康检查日志
     */
    public function logHealthCheck(AppInstance $instance, bool $healthy): AppLifecycleLog
    {
        return $this->createLog(
            $instance,
            LifecycleActionType::HEALTH_CHECK,
            $healthy ? LogStatus::SUCCESS : LogStatus::FAILED,
            $healthy ? '健康检查通过' : '健康检查失败'
        );
    }

    /**
     * 创建卸载开始日志
     */
    public function logUninstallStart(AppInstance $instance): AppLifecycleLog
    {
        return $this->createLog(
            $instance,
            LifecycleActionType::UNINSTALL,
            LogStatus::SUCCESS,
            '开始卸载应用'
        );
    }

    /**
     * 创建卸载完成日志
     */
    public function logUninstallComplete(AppInstance $instance, bool $success): AppLifecycleLog
    {
        return $this->createLog(
            $instance,
            LifecycleActionType::UNINSTALL,
            $success ? LogStatus::SUCCESS : LogStatus::FAILED,
            $success ? '卸载完成' : '卸载失败'
        );
    }
}
