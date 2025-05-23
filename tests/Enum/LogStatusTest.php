<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Enum\LogStatus;

/**
 * LogStatus枚举测试类
 */
class LogStatusTest extends TestCase
{
    public function test_enumValues_containsAllExpectedValues(): void
    {
        $expectedValues = ['success', 'failed'];
        $actualValues = array_map(fn(LogStatus $status) => $status->value, LogStatus::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_getLabel_success_returnsCorrectChineseLabel(): void
    {
        $status = LogStatus::SUCCESS;
        $this->assertEquals('成功', $status->getLabel());
    }

    public function test_getLabel_failed_returnsCorrectChineseLabel(): void
    {
        $status = LogStatus::FAILED;
        $this->assertEquals('失败', $status->getLabel());
    }

    public function test_enumCount_returnsCorrectNumber(): void
    {
        $this->assertCount(2, LogStatus::cases());
    }

    public function test_enumImplementsExpectedInterfaces(): void
    {
        $status = LogStatus::SUCCESS;
        
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, $status);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, $status);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, $status);
    }

    public function test_allEnumValues_haveLabels(): void
    {
        foreach (LogStatus::cases() as $status) {
            $label = $status->getLabel();
            $this->assertNotEmpty($label);
            $this->assertIsString($label);
        }
    }
} 