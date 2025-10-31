<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Service\AppTemplateService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * AppTemplateService单元测试
 *
 * @internal
 */
#[CoversClass(AppTemplateService::class)]
#[RunTestsInSeparateProcesses]
final class AppTemplateServiceTest extends AbstractIntegrationTestCase
{
    private AppTemplateService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(AppTemplateService::class);
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

    public function testSave(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setVersion('1.0.0');

        $this->service->save($template);
        $this->assertNotNull($template->getId());
    }

    public function testRemove(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setVersion('1.0.0');

        $this->service->save($template);
        $id = $template->getId();
        $this->service->remove($template);

        $this->assertNull($this->service->find((string) $id));
    }

    public function testFindLatestVersions(): void
    {
        $result = $this->service->findLatestVersions();
        $this->assertIsArray($result);
    }

    public function testEnable(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setVersion('1.0.0');
        $template->setEnabled(false);

        $this->service->save($template);
        $this->service->enable($template);
        $this->assertTrue($template->isEnabled());
    }

    public function testDisable(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setVersion('1.0.0');
        $template->setEnabled(true);

        $this->service->save($template);
        $this->service->disable($template);
        $this->assertFalse($template->isEnabled());
    }
}
