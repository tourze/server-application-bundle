<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * AppPortConfiguration实体测试
 *
 * @internal
 */
#[CoversClass(AppPortConfiguration::class)]
final class AppPortConfigurationTest extends AbstractEntityTestCase
{
    private AppPortConfiguration $appPortConfiguration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appPortConfiguration = $this->createEntity();
    }

    public function testSetTemplateWithValidTemplateSetsTemplateCorrectly(): void
    {
        // 注意：使用具体实体类 AppTemplate 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $template = $this->createMock(AppTemplate::class);

        $this->appPortConfiguration->setTemplate($template);
        $this->assertSame($template, $this->appPortConfiguration->getTemplate());
    }

    public function testSetPortWithValidIntegerSetsPortCorrectly(): void
    {
        $port = 8080;

        $this->appPortConfiguration->setPort($port);

        $this->assertEquals($port, $this->appPortConfiguration->getPort());
    }

    public function testSetProtocolWithValidEnumSetsProtocolCorrectly(): void
    {
        $protocol = ProtocolType::TCP;

        $this->appPortConfiguration->setProtocol($protocol);

        $this->assertEquals($protocol, $this->appPortConfiguration->getProtocol());
    }

    public function testSetDescriptionWithValidStringSetsDescriptionCorrectly(): void
    {
        $description = 'HTTP server port';

        $this->appPortConfiguration->setDescription($description);

        $this->assertEquals($description, $this->appPortConfiguration->getDescription());
    }

    public function testSetDescriptionWithNullSetsDescriptionToNull(): void
    {
        $this->appPortConfiguration->setDescription(null);

        $this->assertNull($this->appPortConfiguration->getDescription());
    }

    public function testSetHealthCheckTypeWithValidEnumSetsHealthCheckTypeCorrectly(): void
    {
        $healthCheckType = HealthCheckType::TCP_CONNECT;

        $this->appPortConfiguration->setHealthCheckType($healthCheckType);

        $this->assertEquals($healthCheckType, $this->appPortConfiguration->getHealthCheckType());
    }

    public function testSetHealthCheckConfigWithArraySetsHealthCheckConfigCorrectly(): void
    {
        $config = ['timeout' => 5, 'retries' => 3];

        $this->appPortConfiguration->setHealthCheckConfig($config);

        $this->assertEquals($config, $this->appPortConfiguration->getHealthCheckConfig());
    }

    public function testSetHealthCheckConfigWithNullSetsHealthCheckConfigToNull(): void
    {
        $this->appPortConfiguration->setHealthCheckConfig(null);

        $this->assertNull($this->appPortConfiguration->getHealthCheckConfig());
    }

    public function testSetHealthCheckIntervalWithValidIntegerSetsHealthCheckIntervalCorrectly(): void
    {
        $interval = 60;

        $this->appPortConfiguration->setHealthCheckInterval($interval);

        $this->assertEquals($interval, $this->appPortConfiguration->getHealthCheckInterval());
    }

    public function testSetHealthCheckTimeoutWithValidIntegerSetsHealthCheckTimeoutCorrectly(): void
    {
        $timeout = 10;

        $this->appPortConfiguration->setHealthCheckTimeout($timeout);

        $this->assertEquals($timeout, $this->appPortConfiguration->getHealthCheckTimeout());
    }

    public function testSetHealthCheckRetriesWithValidIntegerSetsHealthCheckRetriesCorrectly(): void
    {
        $retries = 3;

        $this->appPortConfiguration->setHealthCheckRetries($retries);

        $this->assertEquals($retries, $this->appPortConfiguration->getHealthCheckRetries());
    }

    public function testToStringWithPortAndProtocolReturnsCorrectFormat(): void
    {
        $this->appPortConfiguration->setPort(8080);
        $this->appPortConfiguration->setProtocol(ProtocolType::TCP);

        $this->assertEquals('tcp/8080', (string) $this->appPortConfiguration);
    }

    public function testToStringWithPortProtocolAndDescriptionReturnsCorrectFormat(): void
    {
        $this->appPortConfiguration->setPort(443);
        $this->appPortConfiguration->setProtocol(ProtocolType::TCP);
        $this->appPortConfiguration->setDescription('HTTPS');

        $this->assertEquals('tcp/443 (HTTPS)', (string) $this->appPortConfiguration);
    }

    public function testToAdminArrayWithCompleteDataReturnsCorrectArray(): void
    {
        // 注意：使用具体实体类 AppTemplate 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->once())
            ->method('getId')
            ->willReturn(1)
        ;

        $this->appPortConfiguration->setTemplate($template);
        $this->appPortConfiguration->setPort(8080);
        $this->appPortConfiguration->setProtocol(ProtocolType::TCP);
        $this->appPortConfiguration->setDescription('HTTP server');
        $this->appPortConfiguration->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $this->appPortConfiguration->setHealthCheckConfig(['timeout' => 5]);
        $this->appPortConfiguration->setHealthCheckInterval(60);
        $this->appPortConfiguration->setHealthCheckTimeout(10);
        $this->appPortConfiguration->setHealthCheckRetries(3);

        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');
        $this->appPortConfiguration->setCreateTime($createTime);
        $this->appPortConfiguration->setUpdateTime($updateTime);
        $this->appPortConfiguration->setCreatedBy('test_user');
        $this->appPortConfiguration->setUpdatedBy('test_user2');

        $result = $this->appPortConfiguration->toAdminArray();

        $this->assertEquals(1, $result['template']);
        $this->assertEquals(8080, $result['port']);
        $this->assertEquals('tcp', $result['protocol']);
        $this->assertEquals('HTTP server', $result['description']);
        $this->assertEquals('tcp_connect', $result['healthCheckType']);
        $this->assertEquals(['timeout' => 5], $result['healthCheckConfig']);
        $this->assertEquals(60, $result['healthCheckInterval']);
        $this->assertEquals(10, $result['healthCheckTimeout']);
        $this->assertEquals(3, $result['healthCheckRetries']);
        $this->assertEquals('2023-01-01 12:00:00', $result['createTime']);
        $this->assertEquals('2023-01-02 12:00:00', $result['updateTime']);
        $this->assertEquals('test_user', $result['createdBy']);
        $this->assertEquals('test_user2', $result['updatedBy']);
    }

    public function testRetrieveAdminArrayCallsToAdminArray(): void
    {
        // 注意：使用具体实体类 AppTemplate 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1)
        ;

        $this->appPortConfiguration->setTemplate($template);
        $this->appPortConfiguration->setPort(8080);
        $this->appPortConfiguration->setProtocol(ProtocolType::TCP);
        $this->appPortConfiguration->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $this->appPortConfiguration->setHealthCheckInterval(60);
        $this->appPortConfiguration->setHealthCheckTimeout(10);
        $this->appPortConfiguration->setHealthCheckRetries(3);

        $adminArray = $this->appPortConfiguration->retrieveAdminArray();
        $toAdminArray = $this->appPortConfiguration->toAdminArray();

        $this->assertEquals($toAdminArray, $adminArray);
    }

    public function testToApiArrayWithCompleteDataReturnsCorrectArray(): void
    {
        $this->appPortConfiguration->setPort(9090);
        $this->appPortConfiguration->setProtocol(ProtocolType::UDP);
        $this->appPortConfiguration->setDescription('UDP service');
        $this->appPortConfiguration->setHealthCheckType(HealthCheckType::UDP_PORT_CHECK);
        $this->appPortConfiguration->setHealthCheckConfig(['command' => 'netstat -ln']);
        $this->appPortConfiguration->setHealthCheckInterval(120);
        $this->appPortConfiguration->setHealthCheckTimeout(15);
        $this->appPortConfiguration->setHealthCheckRetries(2);

        $result = $this->appPortConfiguration->toApiArray();

        $this->assertEquals(9090, $result['port']);
        $this->assertEquals('udp', $result['protocol']);
        $this->assertEquals('UDP service', $result['description']);
        $this->assertEquals('udp_port_check', $result['healthCheckType']);
        $this->assertEquals(['command' => 'netstat -ln'], $result['healthCheckConfig']);
        $this->assertEquals(120, $result['healthCheckInterval']);
        $this->assertEquals(15, $result['healthCheckTimeout']);
        $this->assertEquals(2, $result['healthCheckRetries']);
        $this->assertArrayNotHasKey('createTime', $result);
        $this->assertArrayNotHasKey('createdBy', $result);
    }

    public function testRetrieveApiArrayCallsToApiArray(): void
    {
        $this->appPortConfiguration->setPort(8080);
        $this->appPortConfiguration->setProtocol(ProtocolType::TCP);
        $this->appPortConfiguration->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $this->appPortConfiguration->setHealthCheckInterval(60);
        $this->appPortConfiguration->setHealthCheckTimeout(10);
        $this->appPortConfiguration->setHealthCheckRetries(3);

        $apiArray = $this->appPortConfiguration->retrieveApiArray();
        $toApiArray = $this->appPortConfiguration->toApiArray();

        $this->assertEquals($toApiArray, $apiArray);
    }

    public function testTimeFieldsWithDateTimeSetsCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');

        $this->appPortConfiguration->setCreateTime($createTime);
        $this->appPortConfiguration->setUpdateTime($updateTime);

        $this->assertEquals($createTime, $this->appPortConfiguration->getCreateTime());
        $this->assertEquals($updateTime, $this->appPortConfiguration->getUpdateTime());
    }

    public function testUserFieldsWithStringsSetsCorrectly(): void
    {
        $createdBy = 'user1';
        $updatedBy = 'user2';

        $this->appPortConfiguration->setCreatedBy($createdBy);
        $this->appPortConfiguration->setUpdatedBy($updatedBy);

        $this->assertEquals($createdBy, $this->appPortConfiguration->getCreatedBy());
        $this->assertEquals($updatedBy, $this->appPortConfiguration->getUpdatedBy());
    }

    public function testIpFieldsWithValidIpsSetsCorrectly(): void
    {
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';

        $this->appPortConfiguration->setCreatedFromIp($createdFromIp);
        $this->appPortConfiguration->setUpdatedFromIp($updatedFromIp);

        $this->assertEquals($createdFromIp, $this->appPortConfiguration->getCreatedFromIp());
        $this->assertEquals($updatedFromIp, $this->appPortConfiguration->getUpdatedFromIp());
    }

    public function testImplementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->appPortConfiguration);
        $this->assertInstanceOf(AdminArrayInterface::class, $this->appPortConfiguration);
        $this->assertInstanceOf(ApiArrayInterface::class, $this->appPortConfiguration);
    }

    protected function createEntity(): AppPortConfiguration
    {
        return new AppPortConfiguration();
    }

    /**
     * 提供属性及其样本值的 Data Provider。
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'port' => ['port', 8080];
        yield 'protocol' => ['protocol', ProtocolType::TCP];
        yield 'description' => ['description', 'HTTP server port'];
        yield 'healthCheckType' => ['healthCheckType', HealthCheckType::TCP_CONNECT];
        yield 'healthCheckConfig' => ['healthCheckConfig', ['timeout' => 5, 'retries' => 3]];
        yield 'healthCheckInterval' => ['healthCheckInterval', 60];
        yield 'healthCheckTimeout' => ['healthCheckTimeout', 10];
        yield 'healthCheckRetries' => ['healthCheckRetries', 3];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
        yield 'createdBy' => ['createdBy', 'test_user'];
        yield 'updatedBy' => ['updatedBy', 'test_user2'];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }
}
