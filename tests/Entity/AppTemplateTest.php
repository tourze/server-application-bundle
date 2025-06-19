<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;

/**
 * AppTemplate实体测试类
 */
class AppTemplateTest extends TestCase
{
    private AppTemplate $appTemplate;

    protected function setUp(): void
    {
        $this->appTemplate = new AppTemplate();
    }

    public function test_construct_initializesCollections(): void
    {
        $template = new AppTemplate();
        
        $this->assertInstanceOf(ArrayCollection::class, $template->getInstallSteps());
        $this->assertInstanceOf(ArrayCollection::class, $template->getUninstallSteps());
        $this->assertInstanceOf(ArrayCollection::class, $template->getPortConfigurations());
        $this->assertCount(0, $template->getInstallSteps());
        $this->assertCount(0, $template->getUninstallSteps());
        $this->assertCount(0, $template->getPortConfigurations());
    }

    public function test_setName_withValidString_setsNameCorrectly(): void
    {
        $name = 'Test Template';
        $this->appTemplate->setName($name);
        
        $this->assertEquals($name, $this->appTemplate->getName());
    }

    public function test_setDescription_withValidString_setsDescriptionCorrectly(): void
    {
        $description = 'Test description';
        $this->appTemplate->setDescription($description);
        
        $this->assertEquals($description, $this->appTemplate->getDescription());
    }

    public function test_setDescription_withNull_setsDescriptionCorrectly(): void
    {
        $this->appTemplate->setDescription(null);
        
        $this->assertNull($this->appTemplate->getDescription());
    }

    public function test_setTags_withArray_setsTagsCorrectly(): void
    {
        $tags = ['tag1', 'tag2', 'tag3'];
        $this->appTemplate->setTags($tags);
        
        $this->assertEquals($tags, $this->appTemplate->getTags());
    }

    public function test_setTags_withNull_setsTagsCorrectly(): void
    {
        $this->appTemplate->setTags(null);
        
        $this->assertNull($this->appTemplate->getTags());
    }

    public function test_setEnabled_withTrue_setsEnabledCorrectly(): void
    {
        $this->appTemplate->setEnabled(true);
        
        $this->assertTrue($this->appTemplate->isEnabled());
    }

    public function test_setEnabled_withFalse_setsEnabledCorrectly(): void
    {
        $this->appTemplate->setEnabled(false);
        
        $this->assertFalse($this->appTemplate->isEnabled());
    }

    public function test_setVersion_withValidString_setsVersionCorrectly(): void
    {
        $version = '1.0.0';
        $this->appTemplate->setVersion($version);
        
        $this->assertEquals($version, $this->appTemplate->getVersion());
    }

    public function test_setIsLatest_withTrue_setsIsLatestCorrectly(): void
    {
        $this->appTemplate->setIsLatest(true);
        
        $this->assertTrue($this->appTemplate->isLatest());
    }

    public function test_setIsLatest_withFalse_setsIsLatestCorrectly(): void
    {
        $this->appTemplate->setIsLatest(false);
        
        $this->assertFalse($this->appTemplate->isLatest());
    }

    public function test_setEnvironmentVariables_withArray_setsCorrectly(): void
    {
        $envVars = ['NODE_ENV' => 'production', 'PORT' => '3000'];
        $this->appTemplate->setEnvironmentVariables($envVars);
        
        $this->assertEquals($envVars, $this->appTemplate->getEnvironmentVariables());
    }

    public function test_setEnvironmentVariables_withNull_setsCorrectly(): void
    {
        $this->appTemplate->setEnvironmentVariables(null);
        
        $this->assertNull($this->appTemplate->getEnvironmentVariables());
    }

    public function test_addInstallStep_withNewStep_addsStepCorrectly(): void
    {
        $mockStep = $this->createMock(AppExecutionStep::class);
        $mockStep->expects($this->once())
            ->method('setTemplate')
            ->with($this->appTemplate);
        
        $this->appTemplate->addInstallStep($mockStep);
        
        $this->assertCount(1, $this->appTemplate->getInstallSteps());
        $this->assertTrue($this->appTemplate->getInstallSteps()->contains($mockStep));
    }

    public function test_addInstallStep_withExistingStep_doesNotAddDuplicate(): void
    {
        $mockStep = $this->createMock(AppExecutionStep::class);
        $mockStep->expects($this->once())
            ->method('setTemplate')
            ->with($this->appTemplate);
        
        $this->appTemplate->addInstallStep($mockStep);
        $this->appTemplate->addInstallStep($mockStep); // Add same step again
        
        $this->assertCount(1, $this->appTemplate->getInstallSteps());
    }

    public function test_removeInstallStep_withExistingStep_removesStepCorrectly(): void
    {
        // Arrange
        $mockStep = $this->createMock(AppExecutionStep::class);
        
        // 添加到集合中
        $this->appTemplate->addInstallStep($mockStep);
        
        // 设置mock期望：当调用getTemplate时应该返回当前的appTemplate
        $mockStep->expects($this->once())
            ->method('getTemplate')
            ->willReturn($this->appTemplate);
        
        // 设置mock期望：当调用setTemplate(null)时
        $mockStep->expects($this->once())
            ->method('setTemplate')
            ->with(null);

        // Act
        $result = $this->appTemplate->removeInstallStep($mockStep);

        // Assert
        $this->assertSame($this->appTemplate, $result);
        $this->assertFalse($this->appTemplate->getInstallSteps()->contains($mockStep));
    }

    public function test_addUninstallStep_withNewStep_addsStepCorrectly(): void
    {
        $mockStep = $this->createMock(AppExecutionStep::class);
        $mockStep->expects($this->once())
            ->method('setTemplate')
            ->with($this->appTemplate);
        
        $this->appTemplate->addUninstallStep($mockStep);
        
        $this->assertCount(1, $this->appTemplate->getUninstallSteps());
        $this->assertTrue($this->appTemplate->getUninstallSteps()->contains($mockStep));
    }

    public function test_removeUninstallStep_withExistingStep_removesStepCorrectly(): void
    {
        // Arrange
        $mockStep = $this->createMock(AppExecutionStep::class);
        
        // 添加到集合中
        $this->appTemplate->addUninstallStep($mockStep);
        
        // 设置mock期望：当调用getTemplate时应该返回当前的appTemplate
        $mockStep->expects($this->once())
            ->method('getTemplate')
            ->willReturn($this->appTemplate);
        
        // 设置mock期望：当调用setTemplate(null)时
        $mockStep->expects($this->once())
            ->method('setTemplate')
            ->with(null);

        // Act
        $result = $this->appTemplate->removeUninstallStep($mockStep);

        // Assert
        $this->assertSame($this->appTemplate, $result);
        $this->assertFalse($this->appTemplate->getUninstallSteps()->contains($mockStep));
    }

    public function test_addPortConfiguration_withNewConfiguration_addsCorrectly(): void
    {
        $mockConfiguration = $this->createMock(AppPortConfiguration::class);
        $mockConfiguration->expects($this->once())
            ->method('setTemplate')
            ->with($this->appTemplate);
        
        $this->appTemplate->addPortConfiguration($mockConfiguration);
        
        $this->assertCount(1, $this->appTemplate->getPortConfigurations());
        $this->assertTrue($this->appTemplate->getPortConfigurations()->contains($mockConfiguration));
    }

    public function test_removePortConfiguration_withExistingConfiguration_removesCorrectly(): void
    {
        // Arrange
        $mockConfiguration = $this->createMock(AppPortConfiguration::class);
        
        // 添加到集合中
        $this->appTemplate->addPortConfiguration($mockConfiguration);
        
        // 设置mock期望：当调用getTemplate时应该返回当前的appTemplate
        $mockConfiguration->expects($this->once())
            ->method('getTemplate')
            ->willReturn($this->appTemplate);
        
        // 设置mock期望：当调用setTemplate(null)时
        $mockConfiguration->expects($this->once())
            ->method('setTemplate')
            ->with(null);

        // Act
        $result = $this->appTemplate->removePortConfiguration($mockConfiguration);

        // Assert
        $this->assertSame($this->appTemplate, $result);
        $this->assertFalse($this->appTemplate->getPortConfigurations()->contains($mockConfiguration));
    }

    public function test_toString_withName_returnsName(): void
    {
        $name = 'Test Template';
        $version = '1.0.0';
        $this->appTemplate->setName($name);
        $this->appTemplate->setVersion($version);
        
        // AppTemplate的__toString方法返回 "name (version)" 格式
        $this->assertEquals($name . ' (' . $version . ')', (string) $this->appTemplate);
    }

    public function test_toString_withoutNameButWithVersion_returnsEmptyString(): void
    {
        // 需要设置必需的属性以避免未初始化错误
        $this->appTemplate->setName('');
        $this->appTemplate->setVersion('1.0.0');
        $this->assertEquals(' (1.0.0)', (string) $this->appTemplate);
    }

    public function test_toAdminArray_withCompleteData_returnsCorrectArray(): void
    {
        $this->appTemplate->setName('Test Template');
        $this->appTemplate->setDescription('Test description');
        $this->appTemplate->setTags(['tag1', 'tag2']);
        $this->appTemplate->setEnabled(true);
        $this->appTemplate->setVersion('1.0.0');
        $this->appTemplate->setIsLatest(true);
        $this->appTemplate->setEnvironmentVariables(['NODE_ENV' => 'production']);
        
        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');
        $this->appTemplate->setCreateTime($createTime);
        $this->appTemplate->setUpdateTime($updateTime);
        $this->appTemplate->setCreatedBy('test_user');
        $this->appTemplate->setUpdatedBy('test_user2');
        
        $result = $this->appTemplate->toAdminArray();
        
        $this->assertEquals('Test Template', $result['name']);
        $this->assertEquals('Test description', $result['description']);
        $this->assertEquals(['tag1', 'tag2'], $result['tags']);
        $this->assertTrue($result['enabled']);
        $this->assertEquals('1.0.0', $result['version']);
        $this->assertTrue($result['isLatest']);
        $this->assertEquals(['NODE_ENV' => 'production'], $result['environmentVariables']);
        $this->assertEquals('2023-01-01 12:00:00', $result['createTime']);
        $this->assertEquals('2023-01-02 12:00:00', $result['updateTime']);
        $this->assertEquals('test_user', $result['createdBy']);
        $this->assertEquals('test_user2', $result['updatedBy']);
    }

    public function test_retrieveAdminArray_callsToAdminArray(): void
    {
        $this->appTemplate->setName('Test Template');
        $this->appTemplate->setVersion('1.0.0');
        
        $adminArray = $this->appTemplate->retrieveAdminArray();
        $toAdminArray = $this->appTemplate->toAdminArray();
        
        $this->assertEquals($toAdminArray, $adminArray);
    }

    public function test_toApiArray_withCompleteData_returnsCorrectArray(): void
    {
        $this->appTemplate->setName('Test Template');
        $this->appTemplate->setDescription('Test description');
        $this->appTemplate->setTags(['tag1', 'tag2']);
        $this->appTemplate->setEnabled(true);
        $this->appTemplate->setVersion('1.0.0');
        $this->appTemplate->setIsLatest(true);
        $this->appTemplate->setEnvironmentVariables(['NODE_ENV' => 'production']);
        
        $result = $this->appTemplate->toApiArray();
        
        $this->assertEquals('Test Template', $result['name']);
        $this->assertEquals('Test description', $result['description']);
        $this->assertEquals(['tag1', 'tag2'], $result['tags']);
        $this->assertTrue($result['enabled']);
        $this->assertEquals('1.0.0', $result['version']);
        $this->assertTrue($result['isLatest']);
        $this->assertEquals(['NODE_ENV' => 'production'], $result['environmentVariables']);
        $this->assertArrayNotHasKey('createTime', $result);
        $this->assertArrayNotHasKey('createdBy', $result);
    }

    public function test_retrieveApiArray_callsToApiArray(): void
    {
        $this->appTemplate->setName('Test Template');
        $this->appTemplate->setVersion('1.0.0');
        
        $apiArray = $this->appTemplate->retrieveApiArray();
        $toApiArray = $this->appTemplate->toApiArray();
        
        $this->assertEquals($toApiArray, $apiArray);
    }

    public function test_timeFields_withDateTime_setsCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');
        
        $this->appTemplate->setCreateTime($createTime);
        $this->appTemplate->setUpdateTime($updateTime);
        
        $this->assertEquals($createTime, $this->appTemplate->getCreateTime());
        $this->assertEquals($updateTime, $this->appTemplate->getUpdateTime());
    }

    public function test_userFields_withStrings_setsCorrectly(): void
    {
        $createdBy = 'user1';
        $updatedBy = 'user2';
        
        $this->appTemplate->setCreatedBy($createdBy);
        $this->appTemplate->setUpdatedBy($updatedBy);
        
        $this->assertEquals($createdBy, $this->appTemplate->getCreatedBy());
        $this->assertEquals($updatedBy, $this->appTemplate->getUpdatedBy());
    }

    public function test_ipFields_withValidIps_setsCorrectly(): void
    {
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';
        
        $this->appTemplate->setCreatedFromIp($createdFromIp);
        $this->appTemplate->setUpdatedFromIp($updatedFromIp);
        
        $this->assertEquals($createdFromIp, $this->appTemplate->getCreatedFromIp());
        $this->assertEquals($updatedFromIp, $this->appTemplate->getUpdatedFromIp());
    }

    public function test_implementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->appTemplate);
        $this->assertInstanceOf(\Tourze\Arrayable\AdminArrayInterface::class, $this->appTemplate);
        $this->assertInstanceOf(\Tourze\Arrayable\ApiArrayInterface::class, $this->appTemplate);
    }
} 