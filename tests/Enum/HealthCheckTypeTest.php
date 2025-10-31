<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use ServerApplicationBundle\Enum\HealthCheckType;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * HealthCheckType枚举测试类
 *
 * @internal
 * */
#[CoversClass(HealthCheckType::class)]
final class HealthCheckTypeTest extends AbstractEnumTestCase
{
    public function testEnumValuesContainsAllExpectedValues(): void
    {
        $expectedValues = ['tcp_connect', 'udp_port_check', 'command'];
        $actualValues = array_map(fn (HealthCheckType $type) => $type->value, HealthCheckType::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function testGetLabelTcpConnectReturnsCorrectChineseLabel(): void
    {
        $type = HealthCheckType::TCP_CONNECT;
        $this->assertEquals('TCP连接检测', $type->getLabel());
    }

    public function testGetLabelUdpPortCheckReturnsCorrectChineseLabel(): void
    {
        $type = HealthCheckType::UDP_PORT_CHECK;
        $this->assertEquals('UDP端口检测', $type->getLabel());
    }

    public function testGetLabelCommandReturnsCorrectChineseLabel(): void
    {
        $type = HealthCheckType::COMMAND;
        $this->assertEquals('命令检测', $type->getLabel());
    }

    public function testEnumCountReturnsCorrectNumber(): void
    {
        $this->assertCount(3, HealthCheckType::cases());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $type = HealthCheckType::TCP_CONNECT;

        $this->assertInstanceOf(Labelable::class, $type);
        $this->assertInstanceOf(Itemable::class, $type);
        $this->assertInstanceOf(Selectable::class, $type);
    }

    public function testAllEnumValuesHaveLabels(): void
    {
        foreach (HealthCheckType::cases() as $type) {
            $label = $type->getLabel();
            $this->assertNotEmpty($label);
        }
    }

    public function testToSelectItemReturnsCorrectFormat(): void
    {
        $type = HealthCheckType::TCP_CONNECT;
        $selectItem = $type->toSelectItem();

        $this->assertIsArray($selectItem);
        $this->assertArrayHasKey('value', $selectItem);
        $this->assertArrayHasKey('label', $selectItem);
        $this->assertEquals('tcp_connect', $selectItem['value']);
        $this->assertEquals('TCP连接检测', $selectItem['label']);
    }

    public function testToArrayReturnsCorrectFormat(): void
    {
        $type = HealthCheckType::TCP_CONNECT;
        $array = $type->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('tcp_connect', $array['value']);
        $this->assertEquals('TCP连接检测', $array['label']);

        // 测试另一个枚举值
        $udpType = HealthCheckType::UDP_PORT_CHECK;
        $udpArray = $udpType->toArray();
        $this->assertEquals('udp_port_check', $udpArray['value']);
        $this->assertEquals('UDP端口检测', $udpArray['label']);

        // 测试第三个枚举值
        $commandType = HealthCheckType::COMMAND;
        $commandArray = $commandType->toArray();
        $this->assertEquals('command', $commandArray['value']);
        $this->assertEquals('命令检测', $commandArray['label']);
    }
}
