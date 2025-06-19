<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;

/**
 * AppPortConfiguration实体测试
 */
class AppPortConfigurationTest extends TestCase
{
    private AppPortConfiguration $appPortConfiguration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->appPortConfiguration = new AppPortConfiguration();
    }

    public function test_setTemplate_withValidTemplate_setsTemplateCorrectly(): void
    {
        $template = $this->createMock(AppTemplate::class);
        
        $result = $this->appPortConfiguration->setTemplate($template);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertSame($template, $this->appPortConfiguration->getTemplate());
    }

    public function test_setPort_withValidInteger_setsPortCorrectly(): void
    {
        $port = 8080;
        
        $result = $this->appPortConfiguration->setPort($port);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertEquals($port, $this->appPortConfiguration->getPort());
    }

    public function test_setProtocol_withValidEnum_setsProtocolCorrectly(): void
    {
        $protocol = ProtocolType::TCP;
        
        $result = $this->appPortConfiguration->setProtocol($protocol);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertEquals($protocol, $this->appPortConfiguration->getProtocol());
    }

    public function test_setDescription_withValidString_setsDescriptionCorrectly(): void
    {
        $description = 'HTTP server port';
        
        $result = $this->appPortConfiguration->setDescription($description);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertEquals($description, $this->appPortConfiguration->getDescription());
    }

    public function test_setDescription_withNull_setsDescriptionToNull(): void
    {
        $result = $this->appPortConfiguration->setDescription(null);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertNull($this->appPortConfiguration->getDescription());
    }

    public function test_setHealthCheckType_withValidEnum_setsHealthCheckTypeCorrectly(): void
    {
        $healthCheckType = HealthCheckType::TCP_CONNECT;
        
        $result = $this->appPortConfiguration->setHealthCheckType($healthCheckType);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertEquals($healthCheckType, $this->appPortConfiguration->getHealthCheckType());
    }

    public function test_setHealthCheckConfig_withArray_setsHealthCheckConfigCorrectly(): void
    {
        $config = ['timeout' => 5, 'retries' => 3];
        
        $result = $this->appPortConfiguration->setHealthCheckConfig($config);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertEquals($config, $this->appPortConfiguration->getHealthCheckConfig());
    }

    public function test_setHealthCheckConfig_withNull_setsHealthCheckConfigToNull(): void
    {
        $result = $this->appPortConfiguration->setHealthCheckConfig(null);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertNull($this->appPortConfiguration->getHealthCheckConfig());
    }

    public function test_setHealthCheckInterval_withValidInteger_setsHealthCheckIntervalCorrectly(): void
    {
        $interval = 60;
        
        $result = $this->appPortConfiguration->setHealthCheckInterval($interval);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertEquals($interval, $this->appPortConfiguration->getHealthCheckInterval());
    }

    public function test_setHealthCheckTimeout_withValidInteger_setsHealthCheckTimeoutCorrectly(): void
    {
        $timeout = 10;
        
        $result = $this->appPortConfiguration->setHealthCheckTimeout($timeout);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertEquals($timeout, $this->appPortConfiguration->getHealthCheckTimeout());
    }

    public function test_setHealthCheckRetries_withValidInteger_setsHealthCheckRetriesCorrectly(): void
    {
        $retries = 3;
        
        $result = $this->appPortConfiguration->setHealthCheckRetries($retries);
        
        $this->assertSame($this->appPortConfiguration, $result);
        $this->assertEquals($retries, $this->appPortConfiguration->getHealthCheckRetries());
    }

    public function test_toString_withPortAndProtocol_returnsCorrectFormat(): void
    {
        $this->appPortConfiguration->setPort(8080);
        $this->appPortConfiguration->setProtocol(ProtocolType::TCP);
        
        $this->assertEquals('tcp/8080', (string) $this->appPortConfiguration);
    }

    public function test_toString_withPortProtocolAndDescription_returnsCorrectFormat(): void
    {
        $this->appPortConfiguration->setPort(443);
        $this->appPortConfiguration->setProtocol(ProtocolType::TCP);
        $this->appPortConfiguration->setDescription('HTTPS');
        
        $this->assertEquals('tcp/443 (HTTPS)', (string) $this->appPortConfiguration);
    }

    public function test_toAdminArray_withCompleteData_returnsCorrectArray(): void
    {
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        
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

    public function test_retrieveAdminArray_callsToAdminArray(): void
    {
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);
        
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

    public function test_toApiArray_withCompleteData_returnsCorrectArray(): void
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

    public function test_retrieveApiArray_callsToApiArray(): void
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

    public function test_timeFields_withDateTime_setsCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');
        
        $this->appPortConfiguration->setCreateTime($createTime);
        $this->appPortConfiguration->setUpdateTime($updateTime);
        
        $this->assertEquals($createTime, $this->appPortConfiguration->getCreateTime());
        $this->assertEquals($updateTime, $this->appPortConfiguration->getUpdateTime());
    }

    public function test_userFields_withStrings_setsCorrectly(): void
    {
        $createdBy = 'user1';
        $updatedBy = 'user2';
        
        $this->appPortConfiguration->setCreatedBy($createdBy);
        $this->appPortConfiguration->setUpdatedBy($updatedBy);
        
        $this->assertEquals($createdBy, $this->appPortConfiguration->getCreatedBy());
        $this->assertEquals($updatedBy, $this->appPortConfiguration->getUpdatedBy());
    }

    public function test_ipFields_withValidIps_setsCorrectly(): void
    {
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';
        
        $this->appPortConfiguration->setCreatedFromIp($createdFromIp);
        $this->appPortConfiguration->setUpdatedFromIp($updatedFromIp);
        
        $this->assertEquals($createdFromIp, $this->appPortConfiguration->getCreatedFromIp());
        $this->assertEquals($updatedFromIp, $this->appPortConfiguration->getUpdatedFromIp());
    }

    public function test_implementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->appPortConfiguration);
        $this->assertInstanceOf(\Tourze\Arrayable\AdminArrayInterface::class, $this->appPortConfiguration);
        $this->assertInstanceOf(\Tourze\Arrayable\ApiArrayInterface::class, $this->appPortConfiguration);
    }
} 