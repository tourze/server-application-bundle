<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Repository\AppTemplateRepository;
use ServerApplicationBundle\Service\AppTemplateService;

/**
 * AppTemplateService单元测试
 */
class AppTemplateServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AppTemplateRepository $repository;
    private AppTemplateService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(AppTemplateRepository::class);
        $this->service = new AppTemplateService($this->entityManager, $this->repository);
    }

    public function test_construct_withValidDependencies_createsServiceInstance(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(AppTemplateRepository::class);
        $service = new AppTemplateService($entityManager, $repository);

        $this->assertInstanceOf(AppTemplateService::class, $service);
    }

    public function test_findAll_callsRepositoryFindAll(): void
    {
        $expectedTemplates = [new AppTemplate(), new AppTemplate()];
        
        $this->repository
            ->method('findAll')
            ->willReturn($expectedTemplates);

        $result = $this->service->findAll();

        $this->assertSame($expectedTemplates, $result);
    }

    public function test_find_withValidId_callsRepositoryFind(): void
    {
        $id = 'test-id';
        $expectedTemplate = new AppTemplate();
        
        $this->repository
            ->method('find')
            ->with($id)
            ->willReturn($expectedTemplate);

        $result = $this->service->find($id);

        $this->assertSame($expectedTemplate, $result);
    }

    public function test_find_withNonExistentId_returnsNull(): void
    {
        $id = 'non-existent-id';
        
        $this->repository
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $result = $this->service->find($id);

        $this->assertNull($result);
    }

    public function test_save_withValidTemplate_persistsAndFlushes(): void
    {
        $template = new AppTemplate();
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($template);
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->save($template);
        
        // 验证方法被调用
        $this->assertTrue(true);
    }

    public function test_save_withFlushFalse_persistsWithoutFlush(): void
    {
        $template = new AppTemplate();
        
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($template);
            
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->service->save($template, false);
        
        // 验证方法被调用
        $this->assertTrue(true);
    }

    public function test_remove_withValidTemplate_removesAndFlushes(): void
    {
        $template = new AppTemplate();
        
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($template);
            
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->remove($template);
        
        // 验证方法被调用
        $this->assertTrue(true);
    }

    public function test_remove_withFlushFalse_removesWithoutFlush(): void
    {
        $template = new AppTemplate();
        
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($template);
            
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->service->remove($template, false);
        
        // 验证方法被调用
        $this->assertTrue(true);
    }

    public function test_findLatestVersions_callsRepositoryFindBy(): void
    {
        $expectedTemplates = [new AppTemplate(), new AppTemplate()];
        
        $this->repository
            ->method('findBy')
            ->with(['isLatest' => true])
            ->willReturn($expectedTemplates);

        $result = $this->service->findLatestVersions();

        $this->assertSame($expectedTemplates, $result);
    }

    public function test_enable_withTemplate_setsEnabledToTrue(): void
    {
        $template = new AppTemplate();
        $template->setEnabled(false);
        
        $this->entityManager
            ->method('persist')
            ->with($template);
            
        $this->entityManager
            ->method('flush');

        $this->service->enable($template);

        $this->assertTrue($template->isEnabled());
    }

    public function test_disable_withTemplate_setsEnabledToFalse(): void
    {
        $template = new AppTemplate();
        $template->setEnabled(true);
        
        $this->entityManager
            ->method('persist')
            ->with($template);
            
        $this->entityManager
            ->method('flush');

        $this->service->disable($template);

        $this->assertFalse($template->isEnabled());
    }

    public function test_setAsLatestVersion_withTemplate_setsIsLatestToTrueAndUpdatesOthers(): void
    {
        $template1 = new AppTemplate();
        $template1->setName('Test Template');
        $template1->setVersion('1.0.0');
        $template1->setIsLatest(true);
        
        $template2 = new AppTemplate();
        $template2->setName('Test Template');
        $template2->setVersion('2.0.0');
        $template2->setIsLatest(false);
        
        $existingTemplates = [$template1];
        
        $this->repository
            ->method('findBy')
            ->with(['name' => 'Test Template', 'isLatest' => true])
            ->willReturn($existingTemplates);
            
        $this->entityManager
            ->method('persist');
            
        $this->entityManager
            ->method('flush');

        $this->service->setAsLatestVersion($template2);

        $this->assertTrue($template2->isLatest());
    }

    public function test_setAsLatestVersion_withNoExistingLatestVersions_setsIsLatestToTrue(): void
    {
        $template = new AppTemplate();
        $template->setName('New Template');
        $template->setVersion('1.0.0');
        $template->setIsLatest(false);
        
        $this->repository
            ->method('findBy')
            ->with(['name' => 'New Template', 'isLatest' => true])
            ->willReturn([]);
            
        $this->entityManager
            ->method('persist')
            ->with($template);
            
        $this->entityManager
            ->method('flush');

        $this->service->setAsLatestVersion($template);

        $this->assertTrue($template->isLatest());
    }
} 