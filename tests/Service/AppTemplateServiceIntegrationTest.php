<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Service\AppTemplateService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * AppTemplateService集成测试
 *
 * @internal
 */
#[CoversClass(AppTemplateService::class)]
#[RunTestsInSeparateProcesses]
final class AppTemplateServiceIntegrationTest extends AbstractIntegrationTestCase
{
    private AppTemplateService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(AppTemplateService::class);
    }

    public function testServiceCanBeRetrieved(): void
    {
        $this->assertInstanceOf(AppTemplateService::class, $this->service);
    }

    public function testSaveAndFind(): void
    {
        $template = new AppTemplate();
        $template->setName('Integration Test');
        $template->setVersion('1.0.0');
        $template->setEnabled(true);

        $this->service->save($template);

        $this->assertNotNull($template->getId());
        $this->assertEntityPersisted($template);
    }

    public function testFindAllReturnsTemplates(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setVersion('1.0.0');
        $template->setEnabled(true);

        $this->service->save($template);

        $templates = $this->service->findAll();
        $this->assertIsArray($templates);
        $this->assertGreaterThanOrEqual(1, count($templates));

        $found = false;
        foreach ($templates as $t) {
            if ('Test Template' === $t->getName()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, '应该找到刚刚创建的模板');
    }

    public function testEnableAndDisable(): void
    {
        $template = new AppTemplate();
        $template->setName('Enable/Disable Test');
        $template->setVersion('1.0.0');
        $template->setEnabled(false);

        $this->service->save($template);
        $this->assertFalse($template->isEnabled());

        $this->service->enable($template);
        $this->assertTrue($template->isEnabled());

        $this->service->disable($template);
        $this->assertFalse($template->isEnabled());
    }

    public function testDisable(): void
    {
        $template = new AppTemplate();
        $template->setName('Disable Test');
        $template->setVersion('1.0.0');
        $template->setEnabled(true);

        $this->service->save($template);
        $this->assertTrue($template->isEnabled());

        $this->service->disable($template);
        $this->assertFalse($template->isEnabled());

        // 验证持久化到数据库
        $templateId = $template->getId();
        $this->assertNotNull($templateId);
        $found = $this->service->find((string) $templateId);
        $this->assertNotNull($found);
        $this->assertFalse($found->isEnabled());
    }

    public function testRemoveTemplate(): void
    {
        $template = new AppTemplate();
        $template->setName('Remove Test');
        $template->setVersion('1.0.0');
        $template->setEnabled(true);

        $this->service->save($template);
        $templateId = $template->getId();
        $this->assertNotNull($templateId);

        $this->service->remove($template);

        // 验证模板已被删除
        $found = $this->service->find((string) $templateId);
        $this->assertNull($found);
    }

    public function testFindLatestVersions(): void
    {
        // 创建两个不同版本的模板
        $template1 = new AppTemplate();
        $template1->setName('Test Template Latest');
        $template1->setVersion('1.0.0');
        $template1->setEnabled(true);
        $template1->setIsLatest(false);

        $template2 = new AppTemplate();
        $template2->setName('Test Template Latest');
        $template2->setVersion('2.0.0');
        $template2->setEnabled(true);
        $template2->setIsLatest(true);

        $template3 = new AppTemplate();
        $template3->setName('Another Template');
        $template3->setVersion('1.0.0');
        $template3->setEnabled(true);
        $template3->setIsLatest(true);

        $this->service->save($template1);
        $this->service->save($template2);
        $this->service->save($template3);

        $latestVersions = $this->service->findLatestVersions();

        $this->assertIsArray($latestVersions);
        $this->assertGreaterThanOrEqual(2, count($latestVersions));

        // 验证返回的都是最新版本
        foreach ($latestVersions as $template) {
            $this->assertTrue($template->isLatest(), '返回的模板应该都是最新版本');
        }

        // 验证包含我们创建的最新版本模板
        $foundTemplate2 = false;
        $foundTemplate3 = false;
        foreach ($latestVersions as $template) {
            if ('Test Template Latest' === $template->getName() && '2.0.0' === $template->getVersion()) {
                $foundTemplate2 = true;
            }
            if ('Another Template' === $template->getName() && '1.0.0' === $template->getVersion()) {
                $foundTemplate3 = true;
            }
        }
        $this->assertTrue($foundTemplate2, '应该找到Test Template Latest v2.0.0');
        $this->assertTrue($foundTemplate3, '应该找到Another Template v1.0.0');
    }

    public function testSetAsLatestVersion(): void
    {
        // 创建三个同名不同版本的模板
        $template1 = new AppTemplate();
        $template1->setName('Version Test Template');
        $template1->setVersion('1.0.0');
        $template1->setEnabled(true);
        $template1->setIsLatest(true); // 初始设置为最新版

        $template2 = new AppTemplate();
        $template2->setName('Version Test Template');
        $template2->setVersion('2.0.0');
        $template2->setEnabled(true);
        $template2->setIsLatest(false);

        $template3 = new AppTemplate();
        $template3->setName('Version Test Template');
        $template3->setVersion('3.0.0');
        $template3->setEnabled(true);
        $template3->setIsLatest(false);

        // 创建一个不同名的模板作为对照
        $otherTemplate = new AppTemplate();
        $otherTemplate->setName('Other Template');
        $otherTemplate->setVersion('1.0.0');
        $otherTemplate->setEnabled(true);
        $otherTemplate->setIsLatest(true);

        $this->service->save($template1);
        $this->service->save($template2);
        $this->service->save($template3);
        $this->service->save($otherTemplate);

        // 验证初始状态
        $this->assertTrue($template1->isLatest());
        $this->assertFalse($template2->isLatest());
        $this->assertFalse($template3->isLatest());
        $this->assertTrue($otherTemplate->isLatest());

        // 将template2设为最新版
        $this->service->setAsLatestVersion($template2);

        // 重新从数据库获取实体以验证更新
        self::getEntityManager()->refresh($template1);
        self::getEntityManager()->refresh($template2);
        self::getEntityManager()->refresh($template3);
        self::getEntityManager()->refresh($otherTemplate);

        // 验证同名模板中只有template2是最新版
        $this->assertFalse($template1->isLatest(), 'template1应该不再是最新版');
        $this->assertTrue($template2->isLatest(), 'template2应该是最新版');
        $this->assertFalse($template3->isLatest(), 'template3应该不是最新版');

        // 验证不同名模板不受影响
        $this->assertTrue($otherTemplate->isLatest(), '不同名模板的最新版状态不应受影响');

        // 再将template3设为最新版
        $this->service->setAsLatestVersion($template3);

        // 重新从数据库获取实体
        self::getEntityManager()->refresh($template1);
        self::getEntityManager()->refresh($template2);
        self::getEntityManager()->refresh($template3);
        self::getEntityManager()->refresh($otherTemplate);

        // 验证现在只有template3是最新版
        $this->assertFalse($template1->isLatest(), 'template1应该不是最新版');
        $this->assertFalse($template2->isLatest(), 'template2应该不再是最新版');
        $this->assertTrue($template3->isLatest(), 'template3应该是最新版');

        // 验证不同名模板仍不受影响
        $this->assertTrue($otherTemplate->isLatest(), '不同名模板的最新版状态不应受影响');
    }
}
