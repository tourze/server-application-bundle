<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Enum\ProtocolType;

/**
 * ProtocolType枚举测试类
 */
class ProtocolTypeTest extends TestCase
{
    public function test_enumValues_containsAllExpectedValues(): void
    {
        $expectedValues = ['tcp', 'udp'];
        $actualValues = array_map(fn(ProtocolType $protocol) => $protocol->value, ProtocolType::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_getLabel_tcp_returnsCorrectLabel(): void
    {
        $protocol = ProtocolType::TCP;
        $this->assertEquals('TCP', $protocol->getLabel());
    }

    public function test_getLabel_udp_returnsCorrectLabel(): void
    {
        $protocol = ProtocolType::UDP;
        $this->assertEquals('UDP', $protocol->getLabel());
    }

    public function test_enumCount_returnsCorrectNumber(): void
    {
        $this->assertCount(2, ProtocolType::cases());
    }

    public function test_enumImplementsExpectedInterfaces(): void
    {
        $protocol = ProtocolType::TCP;
        
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, $protocol);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, $protocol);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, $protocol);
    }

    public function test_allEnumValues_haveLabels(): void
    {
        foreach (ProtocolType::cases() as $protocol) {
            $label = $protocol->getLabel();
            $this->assertNotEmpty($label);
        }
    }
} 