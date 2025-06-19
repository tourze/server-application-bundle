<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Enum\AppStatus;

/**
 * AppStatus枚举测试类
 */
class AppStatusTest extends TestCase
{
    public function test_enumValues_containsAllExpectedValues(): void
    {
        $expectedValues = [
            'installing',
            'running', 
            'failed',
            'uninstalling',
            'stopped'
        ];

        $actualValues = array_map(fn(AppStatus $status) => $status->value, AppStatus::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_getLabel_installing_returnsCorrectChineseLabel(): void
    {
        $status = AppStatus::INSTALLING;
        $this->assertEquals('安装中', $status->getLabel());
    }

    public function test_getLabel_running_returnsCorrectChineseLabel(): void
    {
        $status = AppStatus::RUNNING;
        $this->assertEquals('运行中', $status->getLabel());
    }

    public function test_getLabel_failed_returnsCorrectChineseLabel(): void
    {
        $status = AppStatus::FAILED;
        $this->assertEquals('失败', $status->getLabel());
    }

    public function test_getLabel_uninstalling_returnsCorrectChineseLabel(): void
    {
        $status = AppStatus::UNINSTALLING;
        $this->assertEquals('卸载中', $status->getLabel());
    }

    public function test_getLabel_stopped_returnsCorrectChineseLabel(): void
    {
        $status = AppStatus::STOPPED;
        $this->assertEquals('已停止', $status->getLabel());
    }

    public function test_enumCount_returnsCorrectNumber(): void
    {
        $this->assertCount(5, AppStatus::cases());
    }

    public function test_enumImplementsExpectedInterfaces(): void
    {
        $status = AppStatus::INSTALLING;
        
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, $status);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, $status);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, $status);
    }

    /**
     * 测试每个枚举值都有对应的标签
     */
    public function test_allEnumValues_haveLabels(): void
    {
        foreach (AppStatus::cases() as $status) {
            $label = $status->getLabel();
            $this->assertNotEmpty($label);
        }
    }
} 