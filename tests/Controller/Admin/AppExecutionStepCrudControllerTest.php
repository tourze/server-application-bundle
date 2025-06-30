<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Controller\Admin\AppExecutionStepCrudController;
use ServerApplicationBundle\Entity\AppExecutionStep;

/**
 * AppExecutionStepCrudController 测试类
 */
class AppExecutionStepCrudControllerTest extends TestCase
{
    private AppExecutionStepCrudController $controller;

    public function testInstanceOfAbstractCrudController(): void
    {
        $this->assertInstanceOf(AbstractCrudController::class, $this->controller);
    }

    public function testGetEntityFqcn(): void
    {
        $this->assertSame(AppExecutionStep::class, AppExecutionStepCrudController::getEntityFqcn());
    }

    public function testConfigureCrud(): void
    {
        $crud = $this->controller->configureCrud(Crud::new());
        $this->assertInstanceOf(Crud::class, $crud);
    }

    public function testConfigureFields(): void
    {
        $fields = iterator_to_array($this->controller->configureFields(Crud::PAGE_INDEX));
        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureActions(): void
    {
        $actions = $this->controller->configureActions(
            \EasyCorp\Bundle\EasyAdminBundle\Config\Actions::new()
        );
        $this->assertInstanceOf(\EasyCorp\Bundle\EasyAdminBundle\Config\Actions::class, $actions);
    }

    public function testConfigureFilters(): void
    {
        $filters = $this->controller->configureFilters(
            \EasyCorp\Bundle\EasyAdminBundle\Config\Filters::new()
        );
        $this->assertInstanceOf(\EasyCorp\Bundle\EasyAdminBundle\Config\Filters::class, $filters);
    }

    protected function setUp(): void
    {
        // 创建必要的模拟依赖
        $appTemplateRepository = $this->createMock(\ServerApplicationBundle\Repository\AppTemplateRepository::class);

        $this->controller = new AppExecutionStepCrudController($appTemplateRepository);
    }
}
