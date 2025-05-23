<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\ExecutionStepType;

/**
 * AppExecutionStep实体测试
 */
class AppExecutionStepTest extends TestCase
{
    private AppExecutionStep $appExecutionStep;

    protected function setUp(): void
    {
        parent::setUp();
        $this->appExecutionStep = new AppExecutionStep();
    }

    public function test_setTemplate_withValidTemplate_setsTemplateCorrectly(): void
    {
        $template = $this->createMock(AppTemplate::class);
        
        // @phpstan-ignore-next-line
        $result = $this->appExecutionStep->setTemplate($template);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertSame($template, $this->appExecutionStep->getTemplate());
    }

    public function test_setSequence_withValidInteger_setsSequenceCorrectly(): void
    {
        $sequence = 10;
        
        $result = $this->appExecutionStep->setSequence($sequence);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($sequence, $this->appExecutionStep->getSequence());
    }

    public function test_setName_withValidString_setsNameCorrectly(): void
    {
        $name = 'Test Step';
        
        $result = $this->appExecutionStep->setName($name);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($name, $this->appExecutionStep->getName());
    }

    public function test_setDescription_withValidString_setsDescriptionCorrectly(): void
    {
        $description = 'Test description';
        
        $result = $this->appExecutionStep->setDescription($description);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($description, $this->appExecutionStep->getDescription());
    }

    public function test_setDescription_withNull_setsDescriptionToNull(): void
    {
        $result = $this->appExecutionStep->setDescription(null);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertNull($this->appExecutionStep->getDescription());
    }

    public function test_setType_withValidEnum_setsTypeCorrectly(): void
    {
        $type = ExecutionStepType::COMMAND;
        
        $result = $this->appExecutionStep->setType($type);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($type, $this->appExecutionStep->getType());
    }

    public function test_setContent_withValidString_setsContentCorrectly(): void
    {
        $content = 'echo "Hello World"';
        
        $result = $this->appExecutionStep->setContent($content);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($content, $this->appExecutionStep->getContent());
    }

    public function test_setWorkingDirectory_withValidString_setsWorkingDirectoryCorrectly(): void
    {
        $workingDirectory = '/tmp';
        
        $result = $this->appExecutionStep->setWorkingDirectory($workingDirectory);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($workingDirectory, $this->appExecutionStep->getWorkingDirectory());
    }

    public function test_setWorkingDirectory_withNull_setsWorkingDirectoryToNull(): void
    {
        $result = $this->appExecutionStep->setWorkingDirectory(null);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertNull($this->appExecutionStep->getWorkingDirectory());
    }

    public function test_setUseSudo_withTrue_setsUseSudoCorrectly(): void
    {
        $result = $this->appExecutionStep->setUseSudo(true);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertTrue($this->appExecutionStep->getUseSudo());
    }

    public function test_setUseSudo_withFalse_setsUseSudoCorrectly(): void
    {
        $result = $this->appExecutionStep->setUseSudo(false);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertFalse($this->appExecutionStep->getUseSudo());
    }

    public function test_setUseSudo_withNull_setsUseSudoToNull(): void
    {
        $result = $this->appExecutionStep->setUseSudo(null);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertNull($this->appExecutionStep->getUseSudo());
    }

    public function test_setTimeout_withValidInteger_setsTimeoutCorrectly(): void
    {
        $timeout = 300;
        
        $result = $this->appExecutionStep->setTimeout($timeout);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($timeout, $this->appExecutionStep->getTimeout());
    }

    public function test_setTimeout_withNull_setsTimeoutToNull(): void
    {
        $result = $this->appExecutionStep->setTimeout(null);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertNull($this->appExecutionStep->getTimeout());
    }

    public function test_setParameters_withArray_setsParametersCorrectly(): void
    {
        $parameters = [
            ['name' => 'PORT', 'description' => 'Port number', 'default' => '8080']
        ];
        
        $result = $this->appExecutionStep->setParameters($parameters);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($parameters, $this->appExecutionStep->getParameters());
    }

    public function test_setParameters_withNull_setsParametersToNull(): void
    {
        $result = $this->appExecutionStep->setParameters(null);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertNull($this->appExecutionStep->getParameters());
    }

    public function test_setParameterPattern_withValidString_setsParameterPatternCorrectly(): void
    {
        $pattern = '${PARAM_NAME}';
        
        $result = $this->appExecutionStep->setParameterPattern($pattern);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($pattern, $this->appExecutionStep->getParameterPattern());
    }

    public function test_setStopOnError_withTrue_setsStopOnErrorCorrectly(): void
    {
        $result = $this->appExecutionStep->setStopOnError(true);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertTrue($this->appExecutionStep->isStopOnError());
    }

    public function test_setStopOnError_withFalse_setsStopOnErrorCorrectly(): void
    {
        $result = $this->appExecutionStep->setStopOnError(false);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertFalse($this->appExecutionStep->isStopOnError());
    }

    public function test_setRetryCount_withValidInteger_setsRetryCountCorrectly(): void
    {
        $retryCount = 3;
        
        $result = $this->appExecutionStep->setRetryCount($retryCount);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($retryCount, $this->appExecutionStep->getRetryCount());
    }

    public function test_setRetryInterval_withValidInteger_setsRetryIntervalCorrectly(): void
    {
        $retryInterval = 10;
        
        $result = $this->appExecutionStep->setRetryInterval($retryInterval);
        
        $this->assertSame($this->appExecutionStep, $result);
        $this->assertEquals($retryInterval, $this->appExecutionStep->getRetryInterval());
    }

    public function test_toString_withName_returnsName(): void
    {
        $name = 'Test Step';
        $this->appExecutionStep->setName($name);
        
        $this->assertEquals($name, (string) $this->appExecutionStep);
    }

    public function test_toString_withoutName_returnsEmptyString(): void
    {
        $this->appExecutionStep->setName('');
        $this->assertEquals('', (string) $this->appExecutionStep);
    }

    public function test_retrieveAdminArray_withCompleteData_returnsCorrectArray(): void
    {
        $template = $this->createMock(AppTemplate::class);
        $template->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        
        // @phpstan-ignore-next-line
        $this->appExecutionStep->setTemplate($template);
        $this->appExecutionStep->setSequence(1);
        $this->appExecutionStep->setName('Test Step');
        $this->appExecutionStep->setDescription('Test description');
        $this->appExecutionStep->setType(ExecutionStepType::COMMAND);
        $this->appExecutionStep->setContent('echo "test"');
        $this->appExecutionStep->setWorkingDirectory('/tmp');
        $this->appExecutionStep->setUseSudo(true);
        $this->appExecutionStep->setTimeout(300);
        $this->appExecutionStep->setParameters(['test' => 'value']);
        $this->appExecutionStep->setParameterPattern('{{PARAM}}');
        $this->appExecutionStep->setStopOnError(true);
        $this->appExecutionStep->setRetryCount(3);
        $this->appExecutionStep->setRetryInterval(10);
        
        $createTime = new \DateTime('2023-01-01 12:00:00');
        $updateTime = new \DateTime('2023-01-02 12:00:00');
        $this->appExecutionStep->setCreateTime($createTime);
        $this->appExecutionStep->setUpdateTime($updateTime);
        $this->appExecutionStep->setCreatedBy('test_user');
        $this->appExecutionStep->setUpdatedBy('test_user2');
        
        $result = $this->appExecutionStep->retrieveAdminArray();
        
        $this->assertEquals(1, $result['template']);
        $this->assertEquals(1, $result['sequence']);
        $this->assertEquals('Test Step', $result['name']);
        $this->assertEquals('Test description', $result['description']);
        $this->assertEquals('command', $result['type']);
        $this->assertEquals('echo "test"', $result['content']);
        $this->assertEquals('/tmp', $result['workingDirectory']);
        $this->assertTrue($result['useSudo']);
        $this->assertEquals(300, $result['timeout']);
        $this->assertEquals(['test' => 'value'], $result['parameters']);
        $this->assertEquals('{{PARAM}}', $result['parameterPattern']);
        $this->assertTrue($result['stopOnError']);
        $this->assertEquals(3, $result['retryCount']);
        $this->assertEquals(10, $result['retryInterval']);
        $this->assertEquals('2023-01-01 12:00:00', $result['createTime']);
        $this->assertEquals('2023-01-02 12:00:00', $result['updateTime']);
        $this->assertEquals('test_user', $result['createdBy']);
        $this->assertEquals('test_user2', $result['updatedBy']);
    }

    public function test_retrieveApiArray_withCompleteData_returnsCorrectArray(): void
    {
        $this->appExecutionStep->setSequence(1);
        $this->appExecutionStep->setName('Test Step');
        $this->appExecutionStep->setDescription('Test description');
        $this->appExecutionStep->setType(ExecutionStepType::SCRIPT);
        $this->appExecutionStep->setContent('#!/bin/bash\necho "test"');
        $this->appExecutionStep->setWorkingDirectory('/home/user');
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
        $this->assertEquals('/home/user', $result['workingDirectory']);
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

    public function test_timeFields_withDateTime_setsCorrectly(): void
    {
        $createTime = new \DateTime('2023-01-01 12:00:00');
        $updateTime = new \DateTime('2023-01-02 12:00:00');
        
        $this->appExecutionStep->setCreateTime($createTime);
        $this->appExecutionStep->setUpdateTime($updateTime);
        
        $this->assertEquals($createTime, $this->appExecutionStep->getCreateTime());
        $this->assertEquals($updateTime, $this->appExecutionStep->getUpdateTime());
    }

    public function test_userFields_withStrings_setsCorrectly(): void
    {
        $createdBy = 'user1';
        $updatedBy = 'user2';
        
        $this->appExecutionStep->setCreatedBy($createdBy);
        $this->appExecutionStep->setUpdatedBy($updatedBy);
        
        $this->assertEquals($createdBy, $this->appExecutionStep->getCreatedBy());
        $this->assertEquals($updatedBy, $this->appExecutionStep->getUpdatedBy());
    }

    public function test_ipFields_withValidIps_setsCorrectly(): void
    {
        $createdFromIp = '192.168.1.1';
        $updatedFromIp = '192.168.1.2';
        
        $this->appExecutionStep->setCreatedFromIp($createdFromIp);
        $this->appExecutionStep->setUpdatedFromIp($updatedFromIp);
        
        $this->assertEquals($createdFromIp, $this->appExecutionStep->getCreatedFromIp());
        $this->assertEquals($updatedFromIp, $this->appExecutionStep->getUpdatedFromIp());
    }

    public function test_implementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->appExecutionStep);
        $this->assertInstanceOf(\Tourze\Arrayable\AdminArrayInterface::class, $this->appExecutionStep);
        $this->assertInstanceOf(\Tourze\Arrayable\ApiArrayInterface::class, $this->appExecutionStep);
    }
} 