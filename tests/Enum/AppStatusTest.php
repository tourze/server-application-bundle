<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use ServerApplicationBundle\Enum\AppStatus;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * AppStatus枚举测试类
 *
 * @internal
 * */
#[CoversClass(AppStatus::class)]
final class AppStatusTest extends AbstractEnumTestCase
{
    public function testEnumValuesContainsAllExpectedValues(): void
    {
        $expectedValues = [
            'installing',
            'running',
            'failed',
            'uninstalling',
            'stopped',
        ];

        $actualValues = array_map(fn (AppStatus $status) => $status->value, AppStatus::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function testGetLabelInstallingReturnsCorrectChineseLabel(): void
    {
        $status = AppStatus::INSTALLING;
        $this->assertEquals('安装中', $status->getLabel());
    }

    public function testGetLabelRunningReturnsCorrectChineseLabel(): void
    {
        $status = AppStatus::RUNNING;
        $this->assertEquals('运行中', $status->getLabel());
    }

    public function testGetLabelFailedReturnsCorrectChineseLabel(): void
    {
        $status = AppStatus::FAILED;
        $this->assertEquals('失败', $status->getLabel());
    }

    public function testGetLabelUninstallingReturnsCorrectChineseLabel(): void
    {
        $status = AppStatus::UNINSTALLING;
        $this->assertEquals('卸载中', $status->getLabel());
    }

    public function testGetLabelStoppedReturnsCorrectChineseLabel(): void
    {
        $status = AppStatus::STOPPED;
        $this->assertEquals('已停止', $status->getLabel());
    }

    public function testEnumCountReturnsCorrectNumber(): void
    {
        $this->assertCount(5, AppStatus::cases());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $status = AppStatus::INSTALLING;

        $this->assertInstanceOf(Labelable::class, $status);
        $this->assertInstanceOf(Itemable::class, $status);
        $this->assertInstanceOf(Selectable::class, $status);
    }

    /**
     * 测试每个枚举值都有对应的标签
     */
    public function testAllEnumValuesHaveLabels(): void
    {
        foreach (AppStatus::cases() as $status) {
            $label = $status->getLabel();
            $this->assertNotEmpty($label);
        }
    }

    public function testToSelectItemReturnsCorrectFormat(): void
    {
        $status = AppStatus::INSTALLING;
        $selectItem = $status->toSelectItem();

        $this->assertIsArray($selectItem);
        $this->assertArrayHasKey('value', $selectItem);
        $this->assertArrayHasKey('label', $selectItem);
        $this->assertEquals('installing', $selectItem['value']);
        $this->assertEquals('安装中', $selectItem['label']);
    }

    public function testToArrayReturnsCorrectFormat(): void
    {
        $status = AppStatus::INSTALLING;
        $array = $status->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('installing', $array['value']);
        $this->assertEquals('安装中', $array['label']);
    }
}
