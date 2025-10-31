<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\AppStatus;
use ServerApplicationBundle\Service\AppInstanceService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * AppInstanceService单元测试
 *
 * @internal
 */
#[CoversClass(AppInstanceService::class)]
#[RunTestsInSeparateProcesses]
final class AppInstanceServiceTest extends AbstractIntegrationTestCase
{
    private AppInstanceService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(AppInstanceService::class);
    }

    public function testFindAll(): void
    {
        $result = $this->service->findAll();
        $this->assertIsArray($result);
    }

    public function testFind(): void
    {
        $result = $this->service->find('non-existing-id');
        $this->assertNull($result);
    }

    public function testFindNotFound(): void
    {
        $result = $this->service->find('definitely-non-existing-id');
        $this->assertNull($result);
    }

    public function testSave(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $instance = new AppInstance();
        $instance->setTemplate($template);
        $instance->setTemplateVersion('1.0.0');
        $instance->setName('Test Instance');
        $instance->setNodeId('test-node');
        $instance->setStatus(AppStatus::STOPPED);

        $this->service->save($instance);
        $this->assertNotNull($instance->getId());
    }

    public function testRemove(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $instance = new AppInstance();
        $instance->setTemplate($template);
        $instance->setTemplateVersion('1.0.0');
        $instance->setName('Test Instance');
        $instance->setNodeId('test-node');
        $instance->setStatus(AppStatus::STOPPED);

        $this->service->save($instance);
        $instanceId = $instance->getId();

        $this->service->remove($instance);

        $deletedInstance = $this->service->find((string) $instanceId);
        $this->assertNull($deletedInstance);
    }

    public function testFindByStatus(): void
    {
        $result = $this->service->findByStatus(AppStatus::RUNNING);
        $this->assertIsArray($result);
    }

    public function testDeploy(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $instance = new AppInstance();
        $instance->setTemplate($template);
        $instance->setTemplateVersion('1.0.0');
        $instance->setName('Test Instance');
        $instance->setNodeId('test-node');
        $instance->setStatus(AppStatus::STOPPED);

        $this->service->save($instance);
        $this->service->deploy($instance);
        $this->assertSame(AppStatus::INSTALLING, $instance->getStatus());
    }

    public function testStart(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $instance = new AppInstance();
        $instance->setTemplate($template);
        $instance->setTemplateVersion('1.0.0');
        $instance->setName('Test Instance');
        $instance->setNodeId('test-node');
        $instance->setStatus(AppStatus::STOPPED);

        $this->service->save($instance);
        $this->service->start($instance);
        $this->assertSame(AppStatus::RUNNING, $instance->getStatus());
    }

    public function testStop(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $instance = new AppInstance();
        $instance->setTemplate($template);
        $instance->setTemplateVersion('1.0.0');
        $instance->setName('Test Instance');
        $instance->setNodeId('test-node');
        $instance->setStatus(AppStatus::RUNNING);

        $this->service->save($instance);
        $this->service->stop($instance);
        $this->assertSame(AppStatus::STOPPED, $instance->getStatus());
    }

    public function testUninstall(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $instance = new AppInstance();
        $instance->setTemplate($template);
        $instance->setTemplateVersion('1.0.0');
        $instance->setName('Test Instance');
        $instance->setNodeId('test-node');
        $instance->setStatus(AppStatus::RUNNING);

        $this->service->save($instance);
        $this->service->uninstall($instance);
        $this->assertSame(AppStatus::UNINSTALLING, $instance->getStatus());
    }

    public function testCheckHealth(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $instance = new AppInstance();
        $instance->setTemplate($template);
        $instance->setTemplateVersion('1.0.0');
        $instance->setName('Test Instance');
        $instance->setNodeId('test-node');
        $instance->setStatus(AppStatus::RUNNING);
        $instance->setHealthy(true);

        $result = $this->service->checkHealth($instance);
        $this->assertTrue($result);
        $this->assertNotNull($instance->getLastHealthCheck());
    }
}
