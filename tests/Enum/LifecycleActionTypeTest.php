<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Enum\LifecycleActionType;

/**
 * LifecycleActionType枚举测试类
 */
class LifecycleActionTypeTest extends TestCase
{
    public function test_enumValues_containsAllExpectedValues(): void
    {
        $expectedValues = ['install', 'health_check', 'uninstall'];
        $actualValues = array_map(fn(LifecycleActionType $type) => $type->value, LifecycleActionType::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_getLabel_install_returnsCorrectChineseLabel(): void
    {
        $type = LifecycleActionType::INSTALL;
        $this->assertEquals('安装', $type->getLabel());
    }

    public function test_getLabel_healthCheck_returnsCorrectChineseLabel(): void
    {
        $type = LifecycleActionType::HEALTH_CHECK;
        $this->assertEquals('健康检测', $type->getLabel());
    }

    public function test_getLabel_uninstall_returnsCorrectChineseLabel(): void
    {
        $type = LifecycleActionType::UNINSTALL;
        $this->assertEquals('卸载', $type->getLabel());
    }

    public function test_enumCount_returnsCorrectNumber(): void
    {
        $this->assertCount(3, LifecycleActionType::cases());
    }

    public function test_enumImplementsExpectedInterfaces(): void
    {
        $type = LifecycleActionType::INSTALL;
        
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, $type);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, $type);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, $type);
    }

    public function test_allEnumValues_haveLabels(): void
    {
        foreach (LifecycleActionType::cases() as $type) {
            $label = $type->getLabel();
            $this->assertNotEmpty($label);
        }
    }
} 