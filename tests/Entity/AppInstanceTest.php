<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Entity\AppPortMapping;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\AppStatus;

/**
 * AppInstance实体测试类
 */
class AppInstanceTest extends TestCase
{
    private AppInstance $appInstance;

    protected function setUp(): void
    {
        $this->appInstance = new AppInstance();
    }

    public function test_construct_initializesCollections(): void
    {
        $instance = new AppInstance();
        
        $this->assertInstanceOf(ArrayCollection::class, $instance->getPortMappings());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getLifecycleLogs());
        $this->assertCount(0, $instance->getPortMappings());
        $this->assertCount(0, $instance->getLifecycleLogs());
        $this->assertFalse($instance->isHealthy()); // Default healthy state
    }

    public function test_setTemplate_withValidTemplate_setsTemplateCorrectly(): void
    {
        $template = $this->createMock(AppTemplate::class);
        $this->appInstance->setTemplate($template);
        
        $this->assertEquals($template, $this->appInstance->getTemplate());
    }

    public function test_setTemplateVersion_withValidString_setsVersionCorrectly(): void
    {
        $version = '1.0.0';
        $this->appInstance->setTemplateVersion($version);
        
        $this->assertEquals($version, $this->appInstance->getTemplateVersion());
    }

    public function test_setNodeId_withValidString_setsNodeIdCorrectly(): void
    {
        $nodeId = 'node-001';
        $this->appInstance->setNodeId($nodeId);
        
        $this->assertEquals($nodeId, $this->appInstance->getNodeId());
    }

    public function test_setName_withValidString_setsNameCorrectly(): void
    {
        $name = 'Test Instance';
        $this->appInstance->setName($name);
        
        $this->assertEquals($name, $this->appInstance->getName());
    }

    public function test_setStatus_withValidStatus_setsStatusCorrectly(): void
    {
        $status = AppStatus::RUNNING;
        $this->appInstance->setStatus($status);
        
        $this->assertEquals($status, $this->appInstance->getStatus());
    }

    public function test_setEnvironmentVariables_withArray_setsCorrectly(): void
    {
        $envVars = ['NODE_ENV' => 'production', 'PORT' => '3000'];
        $this->appInstance->setEnvironmentVariables($envVars);
        
        $this->assertEquals($envVars, $this->appInstance->getEnvironmentVariables());
    }

    public function test_setEnvironmentVariables_withNull_setsCorrectly(): void
    {
        $this->appInstance->setEnvironmentVariables(null);
        
        $this->assertNull($this->appInstance->getEnvironmentVariables());
    }

    public function test_setHealthy_withTrue_setsHealthyCorrectly(): void
    {
        $this->appInstance->setHealthy(true);
        
        $this->assertTrue($this->appInstance->isHealthy());
    }

    public function test_setHealthy_withFalse_setsHealthyCorrectly(): void
    {
        $this->appInstance->setHealthy(false);
        
        $this->assertFalse($this->appInstance->isHealthy());
    }

    public function test_setLastHealthCheck_withDateTime_setsCorrectly(): void
    {
        $lastCheck = new \DateTime('2023-01-01 12:00:00');
        $this->appInstance->setLastHealthCheck($lastCheck);
        
        $this->assertEquals($lastCheck, $this->appInstance->getLastHealthCheck());
    }

    public function test_setLastHealthCheck_withNull_setsCorrectly(): void
    {
        $this->appInstance->setLastHealthCheck(null);
        
        $this->assertNull($this->appInstance->getLastHealthCheck());
    }

    public function test_addPortMapping_withNewMapping_addsMappingCorrectly(): void
    {
        $mapping = $this->createMock(AppPortMapping::class);
        $mapping->expects($this->once())
            ->method('setInstance')
            ->with($this->appInstance);
        
        $this->appInstance->addPortMapping($mapping);
        
        $this->assertCount(1, $this->appInstance->getPortMappings());
        $this->assertTrue($this->appInstance->getPortMappings()->contains($mapping));
    }

    public function test_addPortMapping_withExistingMapping_doesNotAddDuplicate(): void
    {
        $mapping = $this->createMock(AppPortMapping::class);
        $mapping->expects($this->once())
            ->method('setInstance')
            ->with($this->appInstance);
        
        $this->appInstance->addPortMapping($mapping);
        $this->appInstance->addPortMapping($mapping); // Add same mapping again
        
        $this->assertCount(1, $this->appInstance->getPortMappings());
    }

    public function test_removePortMapping_withExistingMapping_removesMappingCorrectly(): void
    {
        // Arrange
        $mockMapping = $this->createMock(AppPortMapping::class);
        
        // 模拟返回true表示元素存在并被移除
        $this->appInstance->addPortMapping($mockMapping);
        
        // 设置mock期望：当调用getInstance时应该返回当前的appInstance
        $mockMapping->expects($this->once())
            ->method('getInstance')
            ->willReturn($this->appInstance);
        
        // 设置mock期望：当调用setInstance(null)时
        $mockMapping->expects($this->once())
            ->method('setInstance')
            ->with(null);

        // Act
        $result = $this->appInstance->removePortMapping($mockMapping);

        // Assert
        $this->assertSame($this->appInstance, $result);
        $this->assertFalse($this->appInstance->getPortMappings()->contains($mockMapping));
    }

    public function test_addLifecycleLog_withNewLog_addsLogCorrectly(): void
    {
        $mockLog = $this->createMock(AppLifecycleLog::class);
        $mockLog->expects($this->once())
            ->method('setInstance')
            ->with($this->appInstance);
        
        $this->appInstance->addLifecycleLog($mockLog);
        
        $this->assertCount(1, $this->appInstance->getLifecycleLogs());
        $this->assertTrue($this->appInstance->getLifecycleLogs()->contains($mockLog));
    }

    public function test_removeLifecycleLog_withExistingLog_removesLogCorrectly(): void
    {
        // Arrange
        $mockLog = $this->createMock(AppLifecycleLog::class);
        
        // 添加到集合中
        $this->appInstance->addLifecycleLog($mockLog);
        
        // 设置mock期望：当调用getInstance时应该返回当前的appInstance
        $mockLog->expects($this->once())
            ->method('getInstance')
            ->willReturn($this->appInstance);
        
        // 设置mock期望：当调用setInstance(null)时
        $mockLog->expects($this->once())
            ->method('setInstance')
            ->with(null);

        // Act
        $result = $this->appInstance->removeLifecycleLog($mockLog);

        // Assert
        $this->assertSame($this->appInstance, $result);
        $this->assertFalse($this->appInstance->getLifecycleLogs()->contains($mockLog));
    }

    public function test_toString_withName_returnsName(): void
    {
        $name = 'Test Instance';
        $this->appInstance->setName($name);
        
        $this->assertEquals($name, (string) $this->appInstance);
    }

    public function test_toString_withoutName_returnsEmptyString(): void
    {
        // 需要设置必需的属性以避免未初始化错误
        $this->appInstance->setName('');
        $this->assertEquals('', (string) $this->appInstance);
    }

    public function test_toAdminArray_withCompleteData_returnsCorrectArray(): void
    {
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->once())
            ->method('getName')
            ->willReturn('Test Template');
        
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

    public function test_retrieveAdminArray_callsToAdminArray(): void
    {
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('Test Template');
        
        $this->appInstance->setTemplate($template);
        $this->appInstance->setTemplateVersion('1.0.0');
        $this->appInstance->setNodeId('node-001');
        $this->appInstance->setName('Test Instance');
        $this->appInstance->setStatus(AppStatus::RUNNING);
        
        $adminArray = $this->appInstance->retrieveAdminArray();
        $toAdminArray = $this->appInstance->toAdminArray();
        
        $this->assertEquals($toAdminArray, $adminArray);
    }

    public function test_toApiArray_withCompleteData_returnsCorrectArray(): void
    {
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $template->expects($this->once())
            ->method('getName')
            ->willReturn('Test Template');
        
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

    public function test_retrieveApiArray_callsToApiArray(): void
    {
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);
        $template->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('Test Template');
        
        $this->appInstance->setTemplate($template);
        $this->appInstance->setTemplateVersion('1.0.0');
        $this->appInstance->setNodeId('node-001');
        $this->appInstance->setName('Test Instance');
        $this->appInstance->setStatus(AppStatus::RUNNING);
        
        $apiArray = $this->appInstance->retrieveApiArray();
        $toApiArray = $this->appInstance->toApiArray();
        
        $this->assertEquals($toApiArray, $apiArray);
    }

    public function test_timeFields_withDateTime_setsCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');
        
        $this->appInstance->setCreateTime($createTime);
        $this->appInstance->setUpdateTime($updateTime);
        
        $this->assertEquals($createTime, $this->appInstance->getCreateTime());
        $this->assertEquals($updateTime, $this->appInstance->getUpdateTime());
    }

    public function test_userFields_withStrings_setsCorrectly(): void
    {
        $createdBy = 'user1';
        $updatedBy = 'user2';
        
        $this->appInstance->setCreatedBy($createdBy);
        $this->appInstance->setUpdatedBy($updatedBy);
        
        $this->assertEquals($createdBy, $this->appInstance->getCreatedBy());
        $this->assertEquals($updatedBy, $this->appInstance->getUpdatedBy());
    }

    public function test_ipFields_withValidIps_setsCorrectly(): void
    {
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';
        
        $this->appInstance->setCreatedFromIp($createdFromIp);
        $this->appInstance->setUpdatedFromIp($updatedFromIp);
        
        $this->assertEquals($createdFromIp, $this->appInstance->getCreatedFromIp());
        $this->assertEquals($updatedFromIp, $this->appInstance->getUpdatedFromIp());
    }

    public function test_implementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->appInstance);
        $this->assertInstanceOf(\Tourze\Arrayable\AdminArrayInterface::class, $this->appInstance);
        $this->assertInstanceOf(\Tourze\Arrayable\ApiArrayInterface::class, $this->appInstance);
    }
} 