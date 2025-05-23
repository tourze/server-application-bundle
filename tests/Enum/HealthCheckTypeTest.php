<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Enum\HealthCheckType;

/**
 * HealthCheckType枚举测试类
 */
class HealthCheckTypeTest extends TestCase
{
    public function test_enumValues_containsAllExpectedValues(): void
    {
        $expectedValues = ['tcp_connect', 'udp_port_check', 'command'];
        $actualValues = array_map(fn(HealthCheckType $type) => $type->value, HealthCheckType::cases());

        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_getLabel_tcpConnect_returnsCorrectChineseLabel(): void
    {
        $type = HealthCheckType::TCP_CONNECT;
        $this->assertEquals('TCP连接检测', $type->getLabel());
    }

    public function test_getLabel_udpPortCheck_returnsCorrectChineseLabel(): void
    {
        $type = HealthCheckType::UDP_PORT_CHECK;
        $this->assertEquals('UDP端口检测', $type->getLabel());
    }

    public function test_getLabel_command_returnsCorrectChineseLabel(): void
    {
        $type = HealthCheckType::COMMAND;
        $this->assertEquals('命令检测', $type->getLabel());
    }

    public function test_enumCount_returnsCorrectNumber(): void
    {
        $this->assertCount(3, HealthCheckType::cases());
    }

    public function test_enumImplementsExpectedInterfaces(): void
    {
        $type = HealthCheckType::TCP_CONNECT;
        
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, $type);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, $type);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, $type);
    }

    public function test_allEnumValues_haveLabels(): void
    {
        foreach (HealthCheckType::cases() as $type) {
            $label = $type->getLabel();
            $this->assertNotEmpty($label);
            $this->assertIsString($label);
        }
    }
} 