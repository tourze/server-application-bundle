<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Entity\AppPortMapping;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\AppStatus;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * AppInstance实体测试类
 *
 * @internal
 */
#[CoversClass(AppInstance::class)]
final class AppInstanceTest extends AbstractEntityTestCase
{
    private AppInstance $appInstance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appInstance = $this->createEntity();
    }

    public function testConstructInitializesCollections(): void
    {
        $instance = new AppInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getPortMappings());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getLifecycleLogs());
        $this->assertCount(0, $instance->getPortMappings());
        $this->assertCount(0, $instance->getLifecycleLogs());
        $this->assertFalse($instance->isHealthy()); // Default healthy state
    }

    public function testSetTemplateWithValidTemplateSetsTemplateCorrectly(): void
    {
        // 注意：使用具体实体类 AppTemplate 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $template = $this->createMock(AppTemplate::class);
        $this->appInstance->setTemplate($template);

        $this->assertEquals($template, $this->appInstance->getTemplate());
    }

    public function testSetTemplateVersionWithValidStringSetsVersionCorrectly(): void
    {
        $version = '1.0.0';
        $this->appInstance->setTemplateVersion($version);

        $this->assertEquals($version, $this->appInstance->getTemplateVersion());
    }

    public function testSetNodeIdWithValidStringSetsNodeIdCorrectly(): void
    {
        $nodeId = 'node-001';
        $this->appInstance->setNodeId($nodeId);

        $this->assertEquals($nodeId, $this->appInstance->getNodeId());
    }

    public function testSetNameWithValidStringSetsNameCorrectly(): void
    {
        $name = 'Test Instance';
        $this->appInstance->setName($name);

        $this->assertEquals($name, $this->appInstance->getName());
    }

    public function testSetStatusWithValidStatusSetsStatusCorrectly(): void
    {
        $status = AppStatus::RUNNING;
        $this->appInstance->setStatus($status);

        $this->assertEquals($status, $this->appInstance->getStatus());
    }

    public function testSetEnvironmentVariablesWithArraySetsCorrectly(): void
    {
        $envVars = ['NODE_ENV' => 'production', 'PORT' => '3000'];
        $this->appInstance->setEnvironmentVariables($envVars);

        $this->assertEquals($envVars, $this->appInstance->getEnvironmentVariables());
    }

    public function testSetEnvironmentVariablesWithNullSetsCorrectly(): void
    {
        $this->appInstance->setEnvironmentVariables(null);

        $this->assertNull($this->appInstance->getEnvironmentVariables());
    }

    public function testSetHealthyWithTrueSetsHealthyCorrectly(): void
    {
        $this->appInstance->setHealthy(true);

        $this->assertTrue($this->appInstance->isHealthy());
    }

    public function testSetHealthyWithFalseSetsHealthyCorrectly(): void
    {
        $this->appInstance->setHealthy(false);

        $this->assertFalse($this->appInstance->isHealthy());
    }

    public function testSetLastHealthCheckWithDateTimeSetsCorrectly(): void
    {
        $lastCheck = new \DateTime('2023-01-01 12:00:00');
        $this->appInstance->setLastHealthCheck($lastCheck);

        $this->assertEquals($lastCheck, $this->appInstance->getLastHealthCheck());
    }

    public function testSetLastHealthCheckWithNullSetsCorrectly(): void
    {
        $this->appInstance->setLastHealthCheck(null);

        $this->assertNull($this->appInstance->getLastHealthCheck());
    }

    public function testAddPortMappingWithNewMappingAddsMappingCorrectly(): void
    {
        // 注意：使用具体实体类 AppPortMapping 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mapping = $this->createMock(AppPortMapping::class);
        $mapping->expects($this->once())
            ->method('setInstance')
            ->with($this->appInstance)
        ;

        $this->appInstance->addPortMapping($mapping);

        $this->assertCount(1, $this->appInstance->getPortMappings());
        $this->assertTrue($this->appInstance->getPortMappings()->contains($mapping));
    }

    public function testAddPortMappingWithExistingMappingDoesNotAddDuplicate(): void
    {
        // 注意：使用具体实体类 AppPortMapping 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mapping = $this->createMock(AppPortMapping::class);
        $mapping->expects($this->once())
            ->method('setInstance')
            ->with($this->appInstance)
        ;

        $this->appInstance->addPortMapping($mapping);
        $this->appInstance->addPortMapping($mapping); // Add same mapping again

        $this->assertCount(1, $this->appInstance->getPortMappings());
    }

    public function testRemovePortMappingWithExistingMappingRemovesMappingCorrectly(): void
    {
        // Arrange
        // 注意：使用具体实体类 AppPortMapping 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mockMapping = $this->createMock(AppPortMapping::class);

        // 模拟返回true表示元素存在并被移除
        $this->appInstance->addPortMapping($mockMapping);

        // 设置mock期望：当调用getInstance时应该返回当前的appInstance
        $mockMapping->expects($this->once())
            ->method('getInstance')
            ->willReturn($this->appInstance)
        ;

        // 设置mock期望：当调用setInstance(null)时
        $mockMapping->expects($this->once())
            ->method('setInstance')
            ->with(null)
        ;

        // Act
        $this->appInstance->removePortMapping($mockMapping);

        // Assert
        $this->assertFalse($this->appInstance->getPortMappings()->contains($mockMapping));
    }

    public function testAddLifecycleLogWithNewLogAddsLogCorrectly(): void
    {
        // 注意：使用具体实体类 AppLifecycleLog 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mockLog = $this->createMock(AppLifecycleLog::class);
        $mockLog->expects($this->once())
            ->method('setInstance')
            ->with($this->appInstance)
        ;

        $this->appInstance->addLifecycleLog($mockLog);

        $this->assertCount(1, $this->appInstance->getLifecycleLogs());
        $this->assertTrue($this->appInstance->getLifecycleLogs()->contains($mockLog));
    }

    public function testRemoveLifecycleLogWithExistingLogRemovesLogCorrectly(): void
    {
        // Arrange
        // 注意：使用具体实体类 AppLifecycleLog 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mockLog = $this->createMock(AppLifecycleLog::class);

        // 添加到集合中
        $this->appInstance->addLifecycleLog($mockLog);

        // 设置mock期望：当调用getInstance时应该返回当前的appInstance
        $mockLog->expects($this->once())
            ->method('getInstance')
            ->willReturn($this->appInstance)
        ;

        // 设置mock期望：当调用setInstance(null)时
        $mockLog->expects($this->once())
            ->method('setInstance')
            ->with(null)
        ;

        // Act
        $this->appInstance->removeLifecycleLog($mockLog);

        // Assert
        $this->assertFalse($this->appInstance->getLifecycleLogs()->contains($mockLog));
    }

    public function testToStringWithNameReturnsName(): void
    {
        $name = 'Test Instance';
        $this->appInstance->setName($name);

        $this->assertEquals($name, (string) $this->appInstance);
    }

    public function testToStringWithoutNameReturnsEmptyString(): void
    {
        // 需要设置必需的属性以避免未初始化错误
        $this->appInstance->setName('');
        $this->assertEquals('', (string) $this->appInstance);
    }

    public function testToAdminArrayWithCompleteDataReturnsCorrectArray(): void
    {
        // 注意：使用具体实体类 AppTemplate 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->once())
            ->method('getName')
            ->willReturn('Test Template')
        ;

        $this->appInstance->setTemplate($template);
        $this->appInstance->setTemplateVersion('1.0.0');
        $this->appInstance->setNodeId('node-001');
        $this->appInstance->setName('Test Instance');
        $this->appInstance->setStatus(AppStatus::RUNNING);
        $this->appInstance->setEnvironmentVariables(['NODE_ENV' => 'production']);
        $this->appInstance->setHealthy(true);

        $lastHealthCheck = new \DateTimeImmutable('2023-01-01 10:00:00');
        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');
        $this->appInstance->setLastHealthCheck($lastHealthCheck);
        $this->appInstance->setCreateTime($createTime);
        $this->appInstance->setUpdateTime($updateTime);
        $this->appInstance->setCreatedBy('test_user');
        $this->appInstance->setUpdatedBy('test_user2');

        $result = $this->appInstance->toAdminArray();

        $this->assertEquals('Test Template', $result['template']);
        $this->assertEquals('1.0.0', $result['templateVersion']);
        $this->assertEquals('node-001', $result['nodeId']);
        $this->assertEquals('Test Instance', $result['name']);
        $this->assertEquals('running', $result['status']);
        $this->assertEquals(['NODE_ENV' => 'production'], $result['environmentVariables']);
        $this->assertTrue($result['healthy']);
        $this->assertEquals('2023-01-01 10:00:00', $result['lastHealthCheck']);
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
            ->method('getName')
            ->willReturn('Test Template')
        ;

        $this->appInstance->setTemplate($template);
        $this->appInstance->setTemplateVersion('1.0.0');
        $this->appInstance->setNodeId('node-001');
        $this->appInstance->setName('Test Instance');
        $this->appInstance->setStatus(AppStatus::RUNNING);

        $adminArray = $this->appInstance->retrieveAdminArray();
        $toAdminArray = $this->appInstance->toAdminArray();

        $this->assertEquals($toAdminArray, $adminArray);
    }

    public function testToApiArrayWithCompleteDataReturnsCorrectArray(): void
    {
        // 注意：使用具体实体类 AppTemplate 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1)
        ;
        $template->expects($this->once())
            ->method('getName')
            ->willReturn('Test Template')
        ;

        $this->appInstance->setTemplate($template);
        $this->appInstance->setTemplateVersion('1.0.0');
        $this->appInstance->setNodeId('node-001');
        $this->appInstance->setName('Test Instance');
        $this->appInstance->setStatus(AppStatus::RUNNING);
        $this->appInstance->setEnvironmentVariables(['NODE_ENV' => 'production']);
        $this->appInstance->setHealthy(true);

        $lastHealthCheck = new \DateTime('2023-01-01 10:00:00');
        $this->appInstance->setLastHealthCheck($lastHealthCheck);

        $result = $this->appInstance->toApiArray();

        $this->assertIsArray($result['template']);
        $this->assertEquals(1, $result['template']['id']);
        $this->assertEquals('Test Template', $result['template']['name']);
        $this->assertEquals('1.0.0', $result['template']['version']);
        $this->assertEquals('node-001', $result['nodeId']);
        $this->assertEquals('Test Instance', $result['name']);
        $this->assertEquals('running', $result['status']);
        $this->assertEquals(['NODE_ENV' => 'production'], $result['environmentVariables']);
        $this->assertTrue($result['healthy']);
        $this->assertEquals('2023-01-01 10:00:00', $result['lastHealthCheck']);
        $this->assertArrayNotHasKey('createTime', $result);
        $this->assertArrayNotHasKey('createdBy', $result);
    }

    public function testRetrieveApiArrayCallsToApiArray(): void
    {
        // 注意：使用具体实体类 AppTemplate 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->exactly(3))
            ->method('getId')
            ->willReturn(1)
        ;
        $template->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('Test Template')
        ;

        $this->appInstance->setTemplate($template);
        $this->appInstance->setTemplateVersion('1.0.0');
        $this->appInstance->setNodeId('node-001');
        $this->appInstance->setName('Test Instance');
        $this->appInstance->setStatus(AppStatus::RUNNING);

        $apiArray = $this->appInstance->retrieveApiArray();
        $toApiArray = $this->appInstance->toApiArray();

        $this->assertEquals($toApiArray, $apiArray);
    }

    public function testTimeFieldsWithDateTimeSetsCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');

        $this->appInstance->setCreateTime($createTime);
        $this->appInstance->setUpdateTime($updateTime);

        $this->assertEquals($createTime, $this->appInstance->getCreateTime());
        $this->assertEquals($updateTime, $this->appInstance->getUpdateTime());
    }

    public function testUserFieldsWithStringsSetsCorrectly(): void
    {
        $createdBy = 'user1';
        $updatedBy = 'user2';

        $this->appInstance->setCreatedBy($createdBy);
        $this->appInstance->setUpdatedBy($updatedBy);

        $this->assertEquals($createdBy, $this->appInstance->getCreatedBy());
        $this->assertEquals($updatedBy, $this->appInstance->getUpdatedBy());
    }

    public function testIpFieldsWithValidIpsSetsCorrectly(): void
    {
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';

        $this->appInstance->setCreatedFromIp($createdFromIp);
        $this->appInstance->setUpdatedFromIp($updatedFromIp);

        $this->assertEquals($createdFromIp, $this->appInstance->getCreatedFromIp());
        $this->assertEquals($updatedFromIp, $this->appInstance->getUpdatedFromIp());
    }

    public function testImplementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->appInstance);
        $this->assertInstanceOf(AdminArrayInterface::class, $this->appInstance);
        $this->assertInstanceOf(ApiArrayInterface::class, $this->appInstance);
    }

    /**
     * 创建被测实体的实例。
     */
    protected function createEntity(): AppInstance
    {
        return new AppInstance();
    }

    /**
     * 提供属性及其样本值的 Data Provider。
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'templateId' => ['templateId', 1];
        yield 'templateVersion' => ['templateVersion', '1.0.0'];
        yield 'nodeId' => ['nodeId', 'node-001'];
        yield 'name' => ['name', 'Test Instance'];
        yield 'status' => ['status', AppStatus::RUNNING];
        yield 'environmentVariables' => ['environmentVariables', ['NODE_ENV' => 'production']];
        yield 'healthy' => ['healthy', true];
        yield 'lastHealthCheck' => ['lastHealthCheck', new \DateTimeImmutable()];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
        yield 'createdBy' => ['createdBy', 'test_user'];
        yield 'updatedBy' => ['updatedBy', 'test_user2'];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }
}
