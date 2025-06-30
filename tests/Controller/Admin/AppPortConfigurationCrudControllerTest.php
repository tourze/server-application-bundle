<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Controller\Admin\AppPortConfigurationCrudController;
use ServerApplicationBundle\Entity\AppPortConfiguration;

/**
 * AppPortConfigurationCrudController 测试类
 */
class AppPortConfigurationCrudControllerTest extends TestCase
{
    private AppPortConfigurationCrudController $controller;

    public function testInstanceOfAbstractCrudController(): void
    {
        $this->assertInstanceOf(AbstractCrudController::class, $this->controller);
    }

    public function testGetEntityFqcn(): void
    {
        $this->assertSame(AppPortConfiguration::class, AppPortConfigurationCrudController::getEntityFqcn());
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
        $this->controller = new AppPortConfigurationCrudController();
    }
}
