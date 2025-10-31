<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\AppStatus;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;
use ServerApplicationBundle\Service\AppLifecycleLogService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * AppLifecycleLogService单元测试
 *
 * @internal
 */
#[CoversClass(AppLifecycleLogService::class)]
#[RunTestsInSeparateProcesses]
final class AppLifecycleLogServiceTest extends AbstractIntegrationTestCase
{
    private AppLifecycleLogService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(AppLifecycleLogService::class);
    }

    private function createAppInstance(): AppInstance
    {
        $template = new AppTemplate();
        $template->setName('test-template');
        $template->setVersion('1.0.0');

        $instance = new AppInstance();
        $instance->setName('test-instance');
        $instance->setTemplate($template);
        $instance->setTemplateVersion('1.0.0');
        $instance->setNodeId('test-node');
        $instance->setStatus(AppStatus::RUNNING);

        return $instance;
    }

    public function testFindAll(): void
    {
        $result = $this->service->findAll();
        $this->assertIsArray($result);
    }

    public function testFind(): void
    {
        $result = $this->service->find('test-id');
        $this->assertNull($result);
    }

    public function testFindNotFound(): void
    {
        $result = $this->service->find('non-existing-id');
        $this->assertNull($result);
    }

    public function testSave(): void
    {
        $instance = $this->createAppInstance();

        $log = new AppLifecycleLog();
        $log->setInstance($instance);
        $log->setAction(LifecycleActionType::INSTALL);
        $log->setStatus(LogStatus::SUCCESS);
        $log->setMessage('Test message');

        $this->service->save($log);
        $this->assertNotNull($log->getId());
    }

    public function testRemove(): void
    {
        $instance = $this->createAppInstance();

        $log = new AppLifecycleLog();
        $log->setInstance($instance);
        $log->setAction(LifecycleActionType::INSTALL);
        $log->setStatus(LogStatus::SUCCESS);
        $log->setMessage('Test message');

        $this->service->save($log);
        $id = $log->getId();
        $this->service->remove($log);

        $this->assertNull($this->service->find((string) $id));
    }

    public function testFindByInstance(): void
    {
        $instance = $this->createAppInstance();

        $result = $this->service->findByInstance($instance);
        $this->assertIsArray($result);
    }

    public function testFindByInstanceAndAction(): void
    {
        $instance = $this->createAppInstance();

        $result = $this->service->findByInstanceAndAction($instance, LifecycleActionType::INSTALL);
        $this->assertIsArray($result);
    }

    public function testCreateLog(): void
    {
        $instance = $this->createAppInstance();

        $log = $this->service->createLog(
            $instance,
            LifecycleActionType::INSTALL,
            LogStatus::SUCCESS,
            'Test message',
            'Test output',
            0,
            1.5
        );

        $this->assertSame($instance, $log->getInstance());
        $this->assertSame(LifecycleActionType::INSTALL, $log->getAction());
        $this->assertSame(LogStatus::SUCCESS, $log->getStatus());
        $this->assertSame('Test message', $log->getMessage());
        $this->assertSame('Test output', $log->getCommandOutput());
        $this->assertSame(0, $log->getExitCode());
        $this->assertSame(1.5, $log->getExecutionTime());
    }

    public function testLogInstallStart(): void
    {
        $instance = $this->createAppInstance();

        $log = $this->service->logInstallStart($instance);
        $this->assertSame($instance, $log->getInstance());
        $this->assertSame(LifecycleActionType::INSTALL, $log->getAction());
        $this->assertSame(LogStatus::SUCCESS, $log->getStatus());
        $this->assertSame('开始安装应用', $log->getMessage());
    }

    public function testLogInstallComplete(): void
    {
        $instance = $this->createAppInstance();

        $log = $this->service->logInstallComplete($instance, true);
        $this->assertSame($instance, $log->getInstance());
        $this->assertSame(LifecycleActionType::INSTALL, $log->getAction());
        $this->assertSame(LogStatus::SUCCESS, $log->getStatus());
        $this->assertSame('安装完成', $log->getMessage());
    }

    public function testLogHealthCheck(): void
    {
        $instance = $this->createAppInstance();

        $log = $this->service->logHealthCheck($instance, true);
        $this->assertSame($instance, $log->getInstance());
        $this->assertSame(LifecycleActionType::HEALTH_CHECK, $log->getAction());
        $this->assertSame(LogStatus::SUCCESS, $log->getStatus());
        $this->assertSame('健康检查通过', $log->getMessage());
    }

    public function testLogUninstallStart(): void
    {
        $instance = $this->createAppInstance();

        $log = $this->service->logUninstallStart($instance);
        $this->assertSame($instance, $log->getInstance());
        $this->assertSame(LifecycleActionType::UNINSTALL, $log->getAction());
        $this->assertSame(LogStatus::SUCCESS, $log->getStatus());
        $this->assertSame('开始卸载应用', $log->getMessage());
    }

    public function testLogUninstallComplete(): void
    {
        $instance = $this->createAppInstance();

        $log = $this->service->logUninstallComplete($instance, true);
        $this->assertSame($instance, $log->getInstance());
        $this->assertSame(LifecycleActionType::UNINSTALL, $log->getAction());
        $this->assertSame(LogStatus::SUCCESS, $log->getStatus());
        $this->assertSame('卸载完成', $log->getMessage());
    }
}
