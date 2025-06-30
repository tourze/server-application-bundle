<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Controller\Admin\AppInstanceCrudController;
use ServerApplicationBundle\Entity\AppInstance;

/**
 * AppInstanceCrudController 测试类
 */
class AppInstanceCrudControllerTest extends TestCase
{
    public function testExtendsAbstractCrudController(): void
    {
        $reflection = new \ReflectionClass(AppInstanceCrudController::class);
        $this->assertTrue($reflection->isSubclassOf(AbstractCrudController::class));
    }

    public function testGetEntityFqcn(): void
    {
        $this->assertSame(AppInstance::class, AppInstanceCrudController::getEntityFqcn());
    }

    public function testControllerExists(): void
    {
        $this->assertTrue(class_exists(AppInstanceCrudController::class));
    }
}
