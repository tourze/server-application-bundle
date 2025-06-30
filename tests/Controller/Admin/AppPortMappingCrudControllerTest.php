<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Controller\Admin\AppPortMappingCrudController;
use ServerApplicationBundle\Entity\AppPortMapping;

/**
 * AppPortMappingCrudController 测试类
 */
class AppPortMappingCrudControllerTest extends TestCase
{
    public function testControllerExists(): void
    {
        $this->assertTrue(class_exists(AppPortMappingCrudController::class));
    }

    public function testExtendsAbstractCrudController(): void
    {
        $reflection = new \ReflectionClass(AppPortMappingCrudController::class);
        $this->assertTrue($reflection->isSubclassOf(AbstractCrudController::class));
    }

    public function testGetEntityFqcn(): void
    {
        $this->assertSame(AppPortMapping::class, AppPortMappingCrudController::getEntityFqcn());
    }
}
