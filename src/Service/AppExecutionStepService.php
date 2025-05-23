<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\ExecutionStepType;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;
use ServerApplicationBundle\Repository\AppExecutionStepRepository;

/**
 * 应用执行步骤服务
 */
class AppExecutionStepService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AppExecutionStepRepository $appExecutionStepRepository,
    ) {
    }

    /**
     * 获取执行步骤列表
     */
    public function findAll(): array
    {
        return $this->appExecutionStepRepository->findAll();
    }

    /**
     * 根据ID获取执行步骤
     */
    public function find(string $id): ?AppExecutionStep
    {
        return $this->appExecutionStepRepository->find($id);
    }

    /**
     * 保存执行步骤
     */
    public function save(AppExecutionStep $appExecutionStep, bool $flush = true): void
    {
        $this->entityManager->persist($appExecutionStep);
        
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 删除执行步骤
     */
    public function remove(AppExecutionStep $appExecutionStep, bool $flush = true): void
    {
        $this->entityManager->remove($appExecutionStep);
        
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 获取模板的安装步骤
     */
    public function findInstallSteps(AppTemplate $template): array
    {
        return $this->appExecutionStepRepository->findBy(
            ['template' => $template],
            ['sequence' => 'ASC']
        );
    }

    /**
     * 获取模板的卸载步骤
     */
    public function findUninstallSteps(AppTemplate $template): array
    {
        return $this->appExecutionStepRepository->findBy(
            ['template' => $template],
            ['sequence' => 'ASC']
        );
    }

    /**
     * 执行步骤
     */
    public function executeStep(AppExecutionStep $step, AppInstance $instance, array $parameters = []): AppLifecycleLog
    {
        // 创建执行日志
        $log = new AppLifecycleLog();
        $log->setInstance($instance);
        $log->setExecutionStep($step);
        $log->setAction(LifecycleActionType::INSTALL); // 默认为安装操作，实际应根据上下文设置
        
        $startTime = microtime(true);
        
        try {
            // 替换参数
            $content = $this->replaceParameters($step->getContent(), $parameters, $step->getParameterPattern());
            
            // 根据类型执行命令或脚本
            if ($step->getType() === ExecutionStepType::COMMAND) {
                // TODO: 调用 server-command-bundle 执行命令
                $result = ['output' => 'Command execution simulation', 'exitCode' => 0];
            } else {
                // TODO: 调用 server-shell-bundle 执行脚本
                $result = ['output' => 'Script execution simulation', 'exitCode' => 0];
            }
            
            $log->setCommandOutput($result['output']);
            $log->setExitCode($result['exitCode']);
            $log->setStatus($result['exitCode'] === 0 ? LogStatus::SUCCESS : LogStatus::FAILED);
            $log->setMessage('执行完成');
            
        } catch (\Throwable $e) {
            $log->setStatus(LogStatus::FAILED);
            $log->setMessage('执行失败: ' . $e->getMessage());
            $log->setExitCode(-1);
        }
        
        $endTime = microtime(true);
        $log->setExecutionTime($endTime - $startTime);
        
        // 保存日志
        $this->entityManager->persist($log);
        $this->entityManager->flush();
        
        return $log;
    }

    /**
     * 替换参数
     */
    private function replaceParameters(string $content, array $parameters, string $pattern): string
    {
        $pattern = str_replace('PARAM_NAME', '([A-Z0-9_]+)', preg_quote($pattern, '/'));
        
        return preg_replace_callback(
            '/' . $pattern . '/',
            function ($matches) use ($parameters) {
                $paramName = $matches[1];
                return $parameters[$paramName] ?? $matches[0];
            },
            $content
        );
    }
}
