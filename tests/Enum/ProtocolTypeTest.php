<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use ServerApplicationBundle\Enum\ProtocolType;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * ProtocolType枚举测试类
 *
 * @internal
 * */
#[CoversClass(ProtocolType::class)]
final class ProtocolTypeTest extends AbstractEnumTestCase
{
    public function testEnumValuesContainsAllExpectedValues(): void
    {
        $expectedValues = ['tcp', 'udp'];
        $actualValues = array_map(fn (ProtocolType $protocol) => $protocol->value, ProtocolType::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function testGetLabelTcpReturnsCorrectLabel(): void
    {
        $protocol = ProtocolType::TCP;
        $this->assertEquals('TCP', $protocol->getLabel());
    }

    public function testGetLabelUdpReturnsCorrectLabel(): void
    {
        $protocol = ProtocolType::UDP;
        $this->assertEquals('UDP', $protocol->getLabel());
    }

    public function testEnumCountReturnsCorrectNumber(): void
    {
        $this->assertCount(2, ProtocolType::cases());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $protocol = ProtocolType::TCP;

        $this->assertInstanceOf(Labelable::class, $protocol);
        $this->assertInstanceOf(Itemable::class, $protocol);
        $this->assertInstanceOf(Selectable::class, $protocol);
    }

    public function testAllEnumValuesHaveLabels(): void
    {
        foreach (ProtocolType::cases() as $protocol) {
            $label = $protocol->getLabel();
            $this->assertNotEmpty($label);
        }
    }

    public function testToSelectItemReturnsCorrectFormat(): void
    {
        $protocol = ProtocolType::TCP;
        $selectItem = $protocol->toSelectItem();

        $this->assertIsArray($selectItem);
        $this->assertArrayHasKey('value', $selectItem);
        $this->assertArrayHasKey('label', $selectItem);
        $this->assertEquals('tcp', $selectItem['value']);
        $this->assertEquals('TCP', $selectItem['label']);
    }

    public function testToArrayReturnsCorrectFormat(): void
    {
        $protocol = ProtocolType::TCP;
        $array = $protocol->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('tcp', $array['value']);
        $this->assertEquals('TCP', $array['label']);

        // 测试另一个枚举值
        $udpProtocol = ProtocolType::UDP;
        $udpArray = $udpProtocol->toArray();
        $this->assertEquals('udp', $udpArray['value']);
        $this->assertEquals('UDP', $udpArray['label']);
    }
}
