<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\ExecutionStepType;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * AppExecutionStep实体测试
 *
 * @internal
 */
#[CoversClass(AppExecutionStep::class)]
final class AppExecutionStepTest extends AbstractEntityTestCase
{
    private AppExecutionStep $appExecutionStep;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appExecutionStep = $this->createEntity();
    }

    public function testSetTemplateWithValidTemplateSetsTemplateCorrectly(): void
    {
        // 注意：使用具体实体类 AppTemplate 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $template = $this->createMock(AppTemplate::class);

        $this->appExecutionStep->setTemplate($template);
        $this->assertSame($template, $this->appExecutionStep->getTemplate());
    }

    public function testSetSequenceWithValidIntegerSetsSequenceCorrectly(): void
    {
        $sequence = 10;

        $this->appExecutionStep->setSequence($sequence);
        $this->assertEquals($sequence, $this->appExecutionStep->getSequence());
    }

    public function testSetNameWithValidStringSetsNameCorrectly(): void
    {
        $name = 'Test Step';

        $this->appExecutionStep->setName($name);
        $this->assertEquals($name, $this->appExecutionStep->getName());
    }

    public function testSetDescriptionWithValidStringSetsDescriptionCorrectly(): void
    {
        $description = 'Test description';

        $this->appExecutionStep->setDescription($description);
        $this->assertEquals($description, $this->appExecutionStep->getDescription());
    }

    public function testSetDescriptionWithNullSetsDescriptionToNull(): void
    {
        $this->appExecutionStep->setDescription(null);
        $this->assertNull($this->appExecutionStep->getDescription());
    }

    public function testSetTypeWithValidEnumSetsTypeCorrectly(): void
    {
        $type = ExecutionStepType::COMMAND;

        $this->appExecutionStep->setType($type);
        $this->assertEquals($type, $this->appExecutionStep->getType());
    }

    public function testSetContentWithValidStringSetsContentCorrectly(): void
    {
        $content = 'echo "Hello World"';

        $this->appExecutionStep->setContent($content);
        $this->assertEquals($content, $this->appExecutionStep->getContent());
    }

    public function testSetWorkingDirectoryWithValidStringSetsWorkingDirectoryCorrectly(): void
    {
        $workingDirectory = '/tmp';

        $this->appExecutionStep->setWorkingDirectory($workingDirectory);
        $this->assertEquals($workingDirectory, $this->appExecutionStep->getWorkingDirectory());
    }

    public function testSetWorkingDirectoryWithNullSetsWorkingDirectoryToNull(): void
    {
        $this->appExecutionStep->setWorkingDirectory(null);
        $this->assertNull($this->appExecutionStep->getWorkingDirectory());
    }

    public function testSetUseSudoWithTrueSetsUseSudoCorrectly(): void
    {
        $this->appExecutionStep->setUseSudo(true);
        $this->assertTrue($this->appExecutionStep->getUseSudo());
    }

    public function testSetUseSudoWithFalseSetsUseSudoCorrectly(): void
    {
        $this->appExecutionStep->setUseSudo(false);
        $this->assertFalse($this->appExecutionStep->getUseSudo());
    }

    public function testSetUseSudoWithNullSetsUseSudoToNull(): void
    {
        $this->appExecutionStep->setUseSudo(null);
        $this->assertNull($this->appExecutionStep->getUseSudo());
    }

    public function testSetTimeoutWithValidIntegerSetsTimeoutCorrectly(): void
    {
        $timeout = 300;

        $this->appExecutionStep->setTimeout($timeout);
        $this->assertEquals($timeout, $this->appExecutionStep->getTimeout());
    }

    public function testSetTimeoutWithNullSetsTimeoutToNull(): void
    {
        $this->appExecutionStep->setTimeout(null);
        $this->assertNull($this->appExecutionStep->getTimeout());
    }

    public function testSetParametersWithArraySetsParametersCorrectly(): void
    {
        $parameters = [
            'PORT' => ['description' => 'Port number', 'default' => '8080'],
        ];

        $this->appExecutionStep->setParameters($parameters);
        $this->assertEquals($parameters, $this->appExecutionStep->getParameters());
    }

    public function testSetParametersWithNullSetsParametersToNull(): void
    {
        $this->appExecutionStep->setParameters(null);
        $this->assertNull($this->appExecutionStep->getParameters());
    }

    public function testSetParameterPatternWithValidStringSetsParameterPatternCorrectly(): void
    {
        $pattern = '${PARAM_NAME}';

        $this->appExecutionStep->setParameterPattern($pattern);
        $this->assertEquals($pattern, $this->appExecutionStep->getParameterPattern());
    }

    public function testSetStopOnErrorWithTrueSetsStopOnErrorCorrectly(): void
    {
        $this->appExecutionStep->setStopOnError(true);
        $this->assertTrue($this->appExecutionStep->isStopOnError());
    }

    public function testSetStopOnErrorWithFalseSetsStopOnErrorCorrectly(): void
    {
        $this->appExecutionStep->setStopOnError(false);
        $this->assertFalse($this->appExecutionStep->isStopOnError());
    }

    public function testSetRetryCountWithValidIntegerSetsRetryCountCorrectly(): void
    {
        $retryCount = 3;

        $this->appExecutionStep->setRetryCount($retryCount);
        $this->assertEquals($retryCount, $this->appExecutionStep->getRetryCount());
    }

    public function testSetRetryIntervalWithValidIntegerSetsRetryIntervalCorrectly(): void
    {
        $retryInterval = 10;

        $this->appExecutionStep->setRetryInterval($retryInterval);
        $this->assertEquals($retryInterval, $this->appExecutionStep->getRetryInterval());
    }

    public function testToStringWithNameReturnsName(): void
    {
        $name = 'Test Step';
        $this->appExecutionStep->setName($name);

        $this->assertEquals($name, (string) $this->appExecutionStep);
    }

    public function testToStringWithoutNameReturnsEmptyString(): void
    {
        $this->appExecutionStep->setName('');
        $this->assertEquals('', (string) $this->appExecutionStep);
    }

    public function testRetrieveAdminArrayWithCompleteDataReturnsCorrectArray(): void
    {
        // 注意：使用具体实体类 AppTemplate 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->once())
            ->method('getId')
            ->willReturn(1)
        ;

        $this->appExecutionStep->setTemplate($template);
        $this->appExecutionStep->setSequence(1);
        $this->appExecutionStep->setName('Test Step');
        $this->appExecutionStep->setDescription('Test description');
        $this->appExecutionStep->setType(ExecutionStepType::COMMAND);
        $this->appExecutionStep->setContent('echo "test"');
        $this->appExecutionStep->setWorkingDirectory('/tmp');
        $this->appExecutionStep->setUseSudo(true);
        $this->appExecutionStep->setTimeout(300);
        $this->appExecutionStep->setParameters(['test' => ['value' => 'test_value']]);
        $this->appExecutionStep->setParameterPattern('{{PARAM}}');
        $this->appExecutionStep->setStopOnError(true);
        $this->appExecutionStep->setRetryCount(3);
        $this->appExecutionStep->setRetryInterval(10);

        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');
        $this->appExecutionStep->setCreateTime($createTime);
        $this->appExecutionStep->setUpdateTime($updateTime);
        $this->appExecutionStep->setCreatedBy('test_user');
        $this->appExecutionStep->setUpdatedBy('test_user2');

        $result = $this->appExecutionStep->retrieveAdminArray();

        $this->assertEquals(1, $result['templateId']);
        $this->assertEquals(1, $result['template']);
        $this->assertEquals(1, $result['sequence']);
        $this->assertEquals('Test Step', $result['name']);
        $this->assertEquals('Test description', $result['description']);
        $this->assertEquals('command', $result['type']);
        $this->assertEquals('echo "test"', $result['content']);
        $this->assertEquals('/tmp', $result['workingDirectory']);
        $this->assertTrue($result['useSudo']);
        $this->assertEquals(300, $result['timeout']);
        $this->assertEquals(['test' => ['value' => 'test_value']], $result['parameters']);
        $this->assertEquals('{{PARAM}}', $result['parameterPattern']);
        $this->assertTrue($result['stopOnError']);
        $this->assertEquals(3, $result['retryCount']);
        $this->assertEquals(10, $result['retryInterval']);
        $this->assertEquals('2023-01-01 12:00:00', $result['createTime']);
        $this->assertEquals('2023-01-02 12:00:00', $result['updateTime']);
        $this->assertEquals('test_user', $result['createdBy']);
        $this->assertEquals('test_user2', $result['updatedBy']);
    }

    public function testRetrieveApiArrayWithCompleteDataReturnsCorrectArray(): void
    {
        $this->appExecutionStep->setSequence(1);
        $this->appExecutionStep->setName('Test Step');
        $this->appExecutionStep->setDescription('Test description');
        $this->appExecutionStep->setType(ExecutionStepType::SCRIPT);
        $this->appExecutionStep->setContent('#!/bin/bash\necho "test"');
        $this->appExecutionStep->setWorkingDirectory('./test');
        $this->appExecutionStep->setUseSudo(false);
        $this->appExecutionStep->setTimeout(120);
        $this->appExecutionStep->setParameters(['param1' => 'value1']);
        $this->appExecutionStep->setParameterPattern('${PARAM}');
        $this->appExecutionStep->setStopOnError(false);
        $this->appExecutionStep->setRetryCount(1);
        $this->appExecutionStep->setRetryInterval(5);

        $result = $this->appExecutionStep->retrieveApiArray();

        $this->assertEquals(1, $result['sequence']);
        $this->assertEquals('Test Step', $result['name']);
        $this->assertEquals('Test description', $result['description']);
        $this->assertEquals('script', $result['type']);
        $this->assertEquals('#!/bin/bash\necho "test"', $result['content']);
        $this->assertEquals('./test', $result['workingDirectory']);
        $this->assertFalse($result['useSudo']);
        $this->assertEquals(120, $result['timeout']);
        $this->assertEquals(['param1' => 'value1'], $result['parameters']);
        $this->assertEquals('${PARAM}', $result['parameterPattern']);
        $this->assertFalse($result['stopOnError']);
        $this->assertEquals(1, $result['retryCount']);
        $this->assertEquals(5, $result['retryInterval']);
        $this->assertArrayNotHasKey('createTime', $result);
        $this->assertArrayNotHasKey('createdBy', $result);
    }

    public function testTimeFieldsWithDateTimeSetsCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2023-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 12:00:00');

        $this->appExecutionStep->setCreateTime($createTime);
        $this->appExecutionStep->setUpdateTime($updateTime);

        $this->assertEquals($createTime, $this->appExecutionStep->getCreateTime());
        $this->assertEquals($updateTime, $this->appExecutionStep->getUpdateTime());
    }

    public function testUserFieldsWithStringsSetsCorrectly(): void
    {
        $createdBy = 'user1';
        $updatedBy = 'user2';

        $this->appExecutionStep->setCreatedBy($createdBy);
        $this->appExecutionStep->setUpdatedBy($updatedBy);

        $this->assertEquals($createdBy, $this->appExecutionStep->getCreatedBy());
        $this->assertEquals($updatedBy, $this->appExecutionStep->getUpdatedBy());
    }

    public function testIpFieldsWithValidIpsSetsCorrectly(): void
    {
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';

        $this->appExecutionStep->setCreatedFromIp($createdFromIp);
        $this->appExecutionStep->setUpdatedFromIp($updatedFromIp);

        $this->assertEquals($createdFromIp, $this->appExecutionStep->getCreatedFromIp());
        $this->assertEquals($updatedFromIp, $this->appExecutionStep->getUpdatedFromIp());
    }

    public function testImplementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->appExecutionStep);
        $this->assertInstanceOf(AdminArrayInterface::class, $this->appExecutionStep);
        $this->assertInstanceOf(ApiArrayInterface::class, $this->appExecutionStep);
    }

    /**
     * 创建被测实体的实例。
     */
    protected function createEntity(): AppExecutionStep
    {
        return new AppExecutionStep();
    }

    /**
     * 提供属性及其样本值的 Data Provider。
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'templateId' => ['templateId', 1];
        yield 'sequence' => ['sequence', 1];
        yield 'name' => ['name', 'Test Step'];
        yield 'description' => ['description', 'Test Description'];
        yield 'type' => ['type', ExecutionStepType::COMMAND];
        yield 'content' => ['content', 'echo "test"'];
        yield 'workingDirectory' => ['workingDirectory', '/tmp'];
        yield 'useSudo' => ['useSudo', true];
        yield 'timeout' => ['timeout', 300];
        yield 'parameters' => ['parameters', ['param1' => 'value1']];
        yield 'parameterPattern' => ['parameterPattern', '${PARAM}'];
        yield 'stopOnError' => ['stopOnError', true];
        yield 'retryCount' => ['retryCount', 3];
        yield 'retryInterval' => ['retryInterval', 10];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
        yield 'createdBy' => ['createdBy', 'test_user'];
        yield 'updatedBy' => ['updatedBy', 'test_user2'];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }
}
