<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppPortMapping;

/**
 * AppPortMapping 测试类
 */
class AppPortMappingTest extends TestCase
{
    private AppPortMapping $portMapping;

    public function testStringableInterface(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->portMapping);
    }

    public function testSetAndGetInstance(): void
    {
        $instance = $this->createMock(AppInstance::class);

        $this->portMapping->setInstance($instance);
        $this->assertSame($instance, $this->portMapping->getInstance());
    }

    public function testSetAndGetConfiguration(): void
    {
        $configuration = $this->createMock(AppPortConfiguration::class);

        $this->portMapping->setConfiguration($configuration);
        $this->assertSame($configuration, $this->portMapping->getConfiguration());
    }

    public function testSetAndGetActualPort(): void
    {
        $port = 8080;

        $this->portMapping->setActualPort($port);
        $this->assertSame($port, $this->portMapping->getActualPort());
    }

    public function testSetAndGetHealthy(): void
    {
        $healthy = true;

        $this->portMapping->setHealthy($healthy);
        $this->assertSame($healthy, $this->portMapping->isHealthy());
    }

    public function testSetAndGetLastHealthCheck(): void
    {
        $time = new \DateTimeImmutable();

        $this->portMapping->setLastHealthCheck($time);
        $this->assertSame($time, $this->portMapping->getLastHealthCheck());
    }

    public function testToString(): void
    {
        $configuration = $this->createMock(\ServerApplicationBundle\Entity\AppPortConfiguration::class);
        $configuration->method('getProtocol')->willReturn(\ServerApplicationBundle\Enum\ProtocolType::TCP);
        
        $this->portMapping->setConfiguration($configuration);
        $this->portMapping->setActualPort(8080);
        $result = (string) $this->portMapping;

        $this->assertNotNull($result);
        $this->assertNotEmpty($result);
    }

    protected function setUp(): void
    {
        $this->portMapping = new AppPortMapping();
    }
}
