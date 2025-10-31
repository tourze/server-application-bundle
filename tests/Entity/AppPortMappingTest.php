<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppPortMapping;
use ServerApplicationBundle\Enum\ProtocolType;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * AppPortMapping 测试类
 *
 * @internal
 */
#[CoversClass(AppPortMapping::class)]
final class AppPortMappingTest extends AbstractEntityTestCase
{
    private AppPortMapping $portMapping;

    public function testStringableInterface(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->portMapping);
    }

    public function testSetAndGetInstance(): void
    {
        // 注意：使用具体实体类 AppInstance 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $instance = $this->createMock(AppInstance::class);

        $this->portMapping->setInstance($instance);
        $this->assertSame($instance, $this->portMapping->getInstance());
    }

    public function testSetAndGetConfiguration(): void
    {
        // 注意：使用具体实体类 AppPortConfiguration 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
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
        // 注意：使用具体实体类 AppPortConfiguration 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $configuration = $this->createMock(AppPortConfiguration::class);
        $configuration->method('getProtocol')->willReturn(ProtocolType::TCP);

        $this->portMapping->setConfiguration($configuration);
        $this->portMapping->setActualPort(8080);
        $result = (string) $this->portMapping;

        $this->assertNotNull($result);
        $this->assertNotEmpty($result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->portMapping = $this->createEntity();
    }

    /**
     * 创建被测实体的实例。
     */
    protected function createEntity(): AppPortMapping
    {
        return new AppPortMapping();
    }

    /**
     * 提供属性及其样本值的 Data Provider。
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'instance' => ['instance', null];
        yield 'actualPort' => ['actualPort', 8080];
        yield 'healthy' => ['healthy', true];
        yield 'lastHealthCheck' => ['lastHealthCheck', new \DateTimeImmutable()];
    }
}
