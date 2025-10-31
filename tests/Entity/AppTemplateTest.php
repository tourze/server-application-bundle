<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * AppTemplate实体测试类
 *
 * @internal
 */
#[CoversClass(AppTemplate::class)]
final class AppTemplateTest extends AbstractEntityTestCase
{
    private AppTemplate $appTemplate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appTemplate = $this->createEntity();
    }

    public function testConstructInitializesCollections(): void
    {
        $template = new AppTemplate();

        $this->assertInstanceOf(ArrayCollection::class, $template->getInstallSteps());
        $this->assertInstanceOf(ArrayCollection::class, $template->getUninstallSteps());
        $this->assertInstanceOf(ArrayCollection::class, $template->getPortConfigurations());
        $this->assertCount(0, $template->getInstallSteps());
        $this->assertCount(0, $template->getUninstallSteps());
        $this->assertCount(0, $template->getPortConfigurations());
    }

    public function testSetNameWithValidStringSetsNameCorrectly(): void
    {
        $name = 'Test Template';
        $this->appTemplate->setName($name);

        $this->assertEquals($name, $this->appTemplate->getName());
    }

    public function testSetDescriptionWithValidStringSetsDescriptionCorrectly(): void
    {
        $description = 'Test description';
        $this->appTemplate->setDescription($description);

        $this->assertEquals($description, $this->appTemplate->getDescription());
    }

    public function testSetDescriptionWithNullSetsDescriptionCorrectly(): void
    {
        $this->appTemplate->setDescription(null);

        $this->assertNull($this->appTemplate->getDescription());
    }

    public function testSetTagsWithArraySetsTagsCorrectly(): void
    {
        $tags = ['tag1', 'tag2', 'tag3'];
        $this->appTemplate->setTags($tags);

        $this->assertEquals($tags, $this->appTemplate->getTags());
    }

    public function testSetTagsWithNullSetsTagsCorrectly(): void
    {
        $this->appTemplate->setTags(null);

        $this->assertNull($this->appTemplate->getTags());
    }

    public function testSetEnabledWithTrueSetsEnabledCorrectly(): void
    {
        $this->appTemplate->setEnabled(true);

        $this->assertTrue($this->appTemplate->isEnabled());
    }

    public function testSetEnabledWithFalseSetsEnabledCorrectly(): void
    {
        $this->appTemplate->setEnabled(false);

        $this->assertFalse($this->appTemplate->isEnabled());
    }

    public function testSetVersionWithValidStringSetsVersionCorrectly(): void
    {
        $version = '1.0.0';
        $this->appTemplate->setVersion($version);

        $this->assertEquals($version, $this->appTemplate->getVersion());
    }

    public function testSetIsLatestWithTrueSetsIsLatestCorrectly(): void
    {
        $this->appTemplate->setIsLatest(true);

        $this->assertTrue($this->appTemplate->isLatest());
    }

    public function testSetIsLatestWithFalseSetsIsLatestCorrectly(): void
    {
        $this->appTemplate->setIsLatest(false);

        $this->assertFalse($this->appTemplate->isLatest());
    }

    public function testSetEnvironmentVariablesWithArraySetsCorrectly(): void
    {
        $envVars = ['NODE_ENV' => 'production', 'PORT' => '3000'];
        $this->appTemplate->setEnvironmentVariables($envVars);

        $this->assertEquals($envVars, $this->appTemplate->getEnvironmentVariables());
    }

    public function testSetEnvironmentVariablesWithNullSetsCorrectly(): void
    {
        $this->appTemplate->setEnvironmentVariables(null);

        $this->assertNull($this->appTemplate->getEnvironmentVariables());
    }

    public function testAddInstallStepWithNewStepAddsStepCorrectly(): void
    {
        // 注意：使用具体实体类 AppExecutionStep 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mockStep = $this->createMock(AppExecutionStep::class);
        $mockStep->expects($this->once())
            ->method('setTemplate')
            ->with($this->appTemplate)
        ;

        $this->appTemplate->addInstallStep($mockStep);

        $this->assertCount(1, $this->appTemplate->getInstallSteps());
        $this->assertTrue($this->appTemplate->getInstallSteps()->contains($mockStep));
    }

    public function testAddInstallStepWithExistingStepDoesNotAddDuplicate(): void
    {
        // 注意：使用具体实体类 AppExecutionStep 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mockStep = $this->createMock(AppExecutionStep::class);
        $mockStep->expects($this->once())
            ->method('setTemplate')
            ->with($this->appTemplate)
        ;

        $this->appTemplate->addInstallStep($mockStep);
        $this->appTemplate->addInstallStep($mockStep); // Add same step again

        $this->assertCount(1, $this->appTemplate->getInstallSteps());
    }

    public function testRemoveInstallStepWithExistingStepRemovesStepCorrectly(): void
    {
        // Arrange
        // 注意：使用具体实体类 AppExecutionStep 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mockStep = $this->createMock(AppExecutionStep::class);

        // 添加到集合中
        $this->appTemplate->addInstallStep($mockStep);

        // 设置mock期望：当调用getTemplate时应该返回当前的appTemplate
        $mockStep->expects($this->once())
            ->method('getTemplate')
            ->willReturn($this->appTemplate)
        ;

        // 设置mock期望：当调用setTemplate(null)时
        $mockStep->expects($this->once())
            ->method('setTemplate')
            ->with(null)
        ;

        // Act
        $this->appTemplate->removeInstallStep($mockStep);

        // Assert
        $this->assertFalse($this->appTemplate->getInstallSteps()->contains($mockStep));
    }

    public function testAddUninstallStepWithNewStepAddsStepCorrectly(): void
    {
        // 注意：使用具体实体类 AppExecutionStep 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mockStep = $this->createMock(AppExecutionStep::class);
        $mockStep->expects($this->once())
            ->method('setTemplate')
            ->with($this->appTemplate)
        ;

        $this->appTemplate->addUninstallStep($mockStep);

        $this->assertCount(1, $this->appTemplate->getUninstallSteps());
        $this->assertTrue($this->appTemplate->getUninstallSteps()->contains($mockStep));
    }

    public function testRemoveUninstallStepWithExistingStepRemovesStepCorrectly(): void
    {
        // Arrange
        // 注意：使用具体实体类 AppExecutionStep 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mockStep = $this->createMock(AppExecutionStep::class);

        // 添加到集合中
        $this->appTemplate->addUninstallStep($mockStep);

        // 设置mock期望：当调用getTemplate时应该返回当前的appTemplate
        $mockStep->expects($this->once())
            ->method('getTemplate')
            ->willReturn($this->appTemplate)
        ;

        // 设置mock期望：当调用setTemplate(null)时
        $mockStep->expects($this->once())
            ->method('setTemplate')
            ->with(null)
        ;

        // Act
        $this->appTemplate->removeUninstallStep($mockStep);

        // Assert
        $this->assertFalse($this->appTemplate->getUninstallSteps()->contains($mockStep));
    }

    public function testAddPortConfigurationWithNewConfigurationAddsCorrectly(): void
    {
        // 注意：使用具体实体类 AppPortConfiguration 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mockConfiguration = $this->createMock(AppPortConfiguration::class);
        $mockConfiguration->expects($this->once())
            ->method('setTemplate')
            ->with($this->appTemplate)
        ;

        $this->appTemplate->addPortConfiguration($mockConfiguration);

        $this->assertCount(1, $this->appTemplate->getPortConfigurations());
        $this->assertTrue($this->appTemplate->getPortConfigurations()->contains($mockConfiguration));
    }

    public function testRemovePortConfigurationWithExistingConfigurationRemovesCorrectly(): void
    {
        // Arrange
        // 注意：使用具体实体类 AppPortConfiguration 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $mockConfiguration = $this->createMock(AppPortConfiguration::class);

        // 添加到集合中
        $this->appTemplate->addPortConfiguration($mockConfiguration);

        // 设置mock期望：当调用getTemplate时应该返回当前的appTemplate
        $mockConfiguration->expects($this->once())
            ->method('getTemplate')
            ->willReturn($this->appTemplate)
        ;

        // 设置mock期望：当调用setTemplate(null)时
        $mockConfiguration->expects($this->once())
            ->method('setTemplate')
            ->with(null)
        ;

        // Act
        $this->appTemplate->removePortConfiguration($mockConfiguration);

        // Assert
        $this->assertFalse($this->appTemplate->getPortConfigurations()->contains($mockConfiguration));
    }

    public function testToStringWithNameReturnsName(): void
    {
        $name = 'Test Template';
        $version = '1.0.0';
        $this->appTemplate->setName($name);
        $this->appTemplate->setVersion($version);

        // AppTemplate的__toString方法返回 "name (version)" 格式
        $this->assertEquals($name . ' (' . $version . ')', (string) $this->appTemplate);
    }

    public function testToStringWithoutNameButWithVersionReturnsEmptyString(): void
    {
        // 需要设置必需的属性以避免未初始化错误
        $this->appTemplate->setName('');
        $this->appTemplate->setVersion('1.0.0');
        $this->assertEquals(' (1.0.0)', (string) $this->appTemplate);
    }

    public function testToAdminArrayWithCompleteDataReturnsCorrectArray(): void
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

    public function testRetrieveAdminArrayCallsToAdminArray(): void
    {
        $this->appTemplate->setName('Test Template');
        $this->appTemplate->setVersion('1.0.0');

        $adminArray = $this->appTemplate->retrieveAdminArray();
        $toAdminArray = $this->appTemplate->toAdminArray();

        $this->assertEquals($toAdminArray, $adminArray);
    }

    public function testToApiArrayWithCompleteDataReturnsCorrectArray(): void
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

    public function testRetrieveApiArrayCallsToApiArray(): void
    {
        $this->appTemplate->setName('Test Template');
        $this->appTemplate->setVersion('1.0.0');

        $apiArray = $this->appTemplate->retrieveApiArray();
        $toApiArray = $this->appTemplate->toApiArray();

        $this->assertEquals($toApiArray, $apiArray);
    }

    public function testTimeFieldsWithDateTimeSetsCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');

        $this->appTemplate->setCreateTime($createTime);
        $this->appTemplate->setUpdateTime($updateTime);

        $this->assertEquals($createTime, $this->appTemplate->getCreateTime());
        $this->assertEquals($updateTime, $this->appTemplate->getUpdateTime());
    }

    public function testUserFieldsWithStringsSetsCorrectly(): void
    {
        $createdBy = 'user1';
        $updatedBy = 'user2';

        $this->appTemplate->setCreatedBy($createdBy);
        $this->appTemplate->setUpdatedBy($updatedBy);

        $this->assertEquals($createdBy, $this->appTemplate->getCreatedBy());
        $this->assertEquals($updatedBy, $this->appTemplate->getUpdatedBy());
    }

    public function testIpFieldsWithValidIpsSetsCorrectly(): void
    {
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';

        $this->appTemplate->setCreatedFromIp($createdFromIp);
        $this->appTemplate->setUpdatedFromIp($updatedFromIp);

        $this->assertEquals($createdFromIp, $this->appTemplate->getCreatedFromIp());
        $this->assertEquals($updatedFromIp, $this->appTemplate->getUpdatedFromIp());
    }

    public function testImplementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->appTemplate);
        $this->assertInstanceOf(AdminArrayInterface::class, $this->appTemplate);
        $this->assertInstanceOf(ApiArrayInterface::class, $this->appTemplate);
    }

    protected function createEntity(): AppTemplate
    {
        return new AppTemplate();
    }

    /**
     * 提供属性及其样本值的 Data Provider。
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', 'Test Template'];
        yield 'description' => ['description', 'Test description'];
        yield 'tags' => ['tags', ['tag1', 'tag2']];
        yield 'enabled' => ['enabled', true];
        yield 'version' => ['version', '1.0.0'];
        yield 'environmentVariables' => ['environmentVariables', ['NODE_ENV' => 'production']];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
        yield 'createdBy' => ['createdBy', 'test_user'];
        yield 'updatedBy' => ['updatedBy', 'test_user2'];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }
}
