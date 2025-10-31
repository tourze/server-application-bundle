<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use ServerApplicationBundle\Enum\LogStatus;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * LogStatus枚举测试类
 *
 * @internal
 * */
#[CoversClass(LogStatus::class)]
final class LogStatusTest extends AbstractEnumTestCase
{
    public function testEnumValuesContainsAllExpectedValues(): void
    {
        $expectedValues = ['success', 'failed'];
        $actualValues = array_map(fn (LogStatus $status) => $status->value, LogStatus::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function testGetLabelSuccessReturnsCorrectChineseLabel(): void
    {
        $status = LogStatus::SUCCESS;
        $this->assertEquals('成功', $status->getLabel());
    }

    public function testGetLabelFailedReturnsCorrectChineseLabel(): void
    {
        $status = LogStatus::FAILED;
        $this->assertEquals('失败', $status->getLabel());
    }

    public function testEnumCountReturnsCorrectNumber(): void
    {
        $this->assertCount(2, LogStatus::cases());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $status = LogStatus::SUCCESS;

        $this->assertInstanceOf(Labelable::class, $status);
        $this->assertInstanceOf(Itemable::class, $status);
        $this->assertInstanceOf(Selectable::class, $status);
    }

    public function testAllEnumValuesHaveLabels(): void
    {
        foreach (LogStatus::cases() as $status) {
            $label = $status->getLabel();
            $this->assertNotEmpty($label);
        }
    }

    public function testToSelectItemReturnsCorrectFormat(): void
    {
        $status = LogStatus::SUCCESS;
        $selectItem = $status->toSelectItem();

        $this->assertIsArray($selectItem);
        $this->assertArrayHasKey('value', $selectItem);
        $this->assertArrayHasKey('label', $selectItem);
        $this->assertEquals('success', $selectItem['value']);
        $this->assertEquals('成功', $selectItem['label']);
    }

    public function testToArrayReturnsCorrectFormat(): void
    {
        $status = LogStatus::SUCCESS;
        $array = $status->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('success', $array['value']);
        $this->assertEquals('成功', $array['label']);

        // 测试另一个枚举值
        $failedStatus = LogStatus::FAILED;
        $failedArray = $failedStatus->toArray();
        $this->assertEquals('failed', $failedArray['value']);
        $this->assertEquals('失败', $failedArray['label']);
    }
}
