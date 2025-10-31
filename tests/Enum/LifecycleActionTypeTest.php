<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use ServerApplicationBundle\Enum\LifecycleActionType;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * LifecycleActionType枚举测试类
 *
 * @internal
 * */
#[CoversClass(LifecycleActionType::class)]
final class LifecycleActionTypeTest extends AbstractEnumTestCase
{
    public function testEnumValuesContainsAllExpectedValues(): void
    {
        $expectedValues = ['install', 'health_check', 'uninstall'];
        $actualValues = array_map(fn (LifecycleActionType $type) => $type->value, LifecycleActionType::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function testGetLabelInstallReturnsCorrectChineseLabel(): void
    {
        $type = LifecycleActionType::INSTALL;
        $this->assertEquals('安装', $type->getLabel());
    }

    public function testGetLabelHealthCheckReturnsCorrectChineseLabel(): void
    {
        $type = LifecycleActionType::HEALTH_CHECK;
        $this->assertEquals('健康检测', $type->getLabel());
    }

    public function testGetLabelUninstallReturnsCorrectChineseLabel(): void
    {
        $type = LifecycleActionType::UNINSTALL;
        $this->assertEquals('卸载', $type->getLabel());
    }

    public function testEnumCountReturnsCorrectNumber(): void
    {
        $this->assertCount(3, LifecycleActionType::cases());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $type = LifecycleActionType::INSTALL;

        $this->assertInstanceOf(Labelable::class, $type);
        $this->assertInstanceOf(Itemable::class, $type);
        $this->assertInstanceOf(Selectable::class, $type);
    }

    public function testAllEnumValuesHaveLabels(): void
    {
        foreach (LifecycleActionType::cases() as $type) {
            $label = $type->getLabel();
            $this->assertNotEmpty($label);
        }
    }

    public function testToSelectItemReturnsCorrectFormat(): void
    {
        $type = LifecycleActionType::INSTALL;
        $selectItem = $type->toSelectItem();

        $this->assertIsArray($selectItem);
        $this->assertArrayHasKey('value', $selectItem);
        $this->assertArrayHasKey('label', $selectItem);
        $this->assertEquals('install', $selectItem['value']);
        $this->assertEquals('安装', $selectItem['label']);
    }

    public function testToArrayReturnsCorrectFormat(): void
    {
        $type = LifecycleActionType::INSTALL;
        $array = $type->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('install', $array['value']);
        $this->assertEquals('安装', $array['label']);

        // 测试另一个枚举值
        $healthCheckType = LifecycleActionType::HEALTH_CHECK;
        $healthCheckArray = $healthCheckType->toArray();
        $this->assertEquals('health_check', $healthCheckArray['value']);
        $this->assertEquals('健康检测', $healthCheckArray['label']);

        // 测试第三个枚举值
        $uninstallType = LifecycleActionType::UNINSTALL;
        $uninstallArray = $uninstallType->toArray();
        $this->assertEquals('uninstall', $uninstallArray['value']);
        $this->assertEquals('卸载', $uninstallArray['label']);
    }
}
