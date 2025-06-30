<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Controller\Admin\AppTemplateCrudController;
use ServerApplicationBundle\Entity\AppTemplate;

/**
 * AppTemplateCrudController 测试类
 */
class AppTemplateCrudControllerTest extends TestCase
{
    public function testControllerExists(): void
    {
        $this->assertTrue(class_exists(AppTemplateCrudController::class));
    }

    public function testExtendsAbstractCrudController(): void
    {
        $reflection = new \ReflectionClass(AppTemplateCrudController::class);
        $this->assertTrue($reflection->isSubclassOf(AbstractCrudController::class));
    }

    public function testGetEntityFqcn(): void
    {
        $this->assertSame(AppTemplate::class, AppTemplateCrudController::getEntityFqcn());
    }
}
