<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Enum\ExecutionStepType;

/**
 * ExecutionStepType枚举测试类
 */
class ExecutionStepTypeTest extends TestCase
{
    public function test_enumValues_containsAllExpectedValues(): void
    {
        $expectedValues = ['command', 'script'];
        $actualValues = array_map(fn(ExecutionStepType $type) => $type->value, ExecutionStepType::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_getLabel_command_returnsCorrectChineseLabel(): void
    {
        $type = ExecutionStepType::COMMAND;
        $this->assertEquals('命令', $type->getLabel());
    }

    public function test_getLabel_script_returnsCorrectChineseLabel(): void
    {
        $type = ExecutionStepType::SCRIPT;
        $this->assertEquals('脚本', $type->getLabel());
    }

    public function test_enumCount_returnsCorrectNumber(): void
    {
        $this->assertCount(2, ExecutionStepType::cases());
    }

    public function test_enumImplementsExpectedInterfaces(): void
    {
        $type = ExecutionStepType::COMMAND;
        
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, $type);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, $type);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, $type);
    }

    public function test_allEnumValues_haveLabels(): void
    {
        foreach (ExecutionStepType::cases() as $type) {
            $label = $type->getLabel();
            $this->assertNotEmpty($label);
        }
    }
}
