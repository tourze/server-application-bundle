<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use ServerApplicationBundle\Enum\ExecutionStepType;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * ExecutionStepType枚举测试类
 *
 * @internal
 * */
#[CoversClass(ExecutionStepType::class)]
final class ExecutionStepTypeTest extends AbstractEnumTestCase
{
    public function testEnumValuesContainsAllExpectedValues(): void
    {
        $expectedValues = ['command', 'script'];
        $actualValues = array_map(fn (ExecutionStepType $type) => $type->value, ExecutionStepType::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function testGetLabelCommandReturnsCorrectChineseLabel(): void
    {
        $type = ExecutionStepType::COMMAND;
        $this->assertEquals('命令', $type->getLabel());
    }

    public function testGetLabelScriptReturnsCorrectChineseLabel(): void
    {
        $type = ExecutionStepType::SCRIPT;
        $this->assertEquals('脚本', $type->getLabel());
    }

    public function testEnumCountReturnsCorrectNumber(): void
    {
        $this->assertCount(2, ExecutionStepType::cases());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $type = ExecutionStepType::COMMAND;

        $this->assertInstanceOf(Labelable::class, $type);
        $this->assertInstanceOf(Itemable::class, $type);
        $this->assertInstanceOf(Selectable::class, $type);
    }

    public function testAllEnumValuesHaveLabels(): void
    {
        foreach (ExecutionStepType::cases() as $type) {
            $label = $type->getLabel();
            $this->assertNotEmpty($label);
        }
    }

    public function testToSelectItemReturnsCorrectFormat(): void
    {
        $type = ExecutionStepType::COMMAND;
        $selectItem = $type->toSelectItem();

        $this->assertIsArray($selectItem);
        $this->assertArrayHasKey('value', $selectItem);
        $this->assertArrayHasKey('label', $selectItem);
        $this->assertEquals('command', $selectItem['value']);
        $this->assertEquals('命令', $selectItem['label']);
    }

    public function testToArrayReturnsCorrectFormat(): void
    {
        $type = ExecutionStepType::COMMAND;
        $array = $type->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('command', $array['value']);
        $this->assertEquals('命令', $array['label']);

        // 测试另一个枚举值
        $scriptType = ExecutionStepType::SCRIPT;
        $scriptArray = $scriptType->toArray();
        $this->assertEquals('script', $scriptArray['value']);
        $this->assertEquals('脚本', $scriptArray['label']);
    }
}
