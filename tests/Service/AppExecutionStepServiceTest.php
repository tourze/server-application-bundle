<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\AppStatus;
use ServerApplicationBundle\Enum\ExecutionStepType;
use ServerApplicationBundle\Service\AppExecutionStepService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * AppExecutionStepService单元测试
 *
 * @internal
 */
#[CoversClass(AppExecutionStepService::class)]
#[RunTestsInSeparateProcesses]
final class AppExecutionStepServiceTest extends AbstractIntegrationTestCase
{
    private AppExecutionStepService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(AppExecutionStepService::class);
    }

    public function testFindAll(): void
    {
        $result = $this->service->findAll();
        $this->assertIsArray($result);
    }

    public function testFind(): void
    {
        $result = $this->service->find('non-existing-id');
        $this->assertNull($result);
    }

    public function testFindNotFound(): void
    {
        $result = $this->service->find('definitely-non-existing-id');
        $this->assertNull($result);
    }

    public function testSave(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $step = new AppExecutionStep();
        $step->setTemplate($template);
        $step->setSequence(1);
        $step->setName('Test Step');
        $step->setType(ExecutionStepType::COMMAND);
        $step->setContent('echo "test"');

        $this->service->save($step);
        $this->assertNotNull($step->getId());
    }

    public function testRemove(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $step = new AppExecutionStep();
        $step->setTemplate($template);
        $step->setSequence(1);
        $step->setName('Test Step');
        $step->setType(ExecutionStepType::COMMAND);
        $step->setContent('echo "test"');

        $this->service->save($step);
        $stepId = $step->getId();

        $this->service->remove($step);

        $deletedStep = $this->service->find((string) $stepId);
        $this->assertNull($deletedStep);
    }

    public function testFindInstallSteps(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $result = $this->service->findInstallSteps($template);
        $this->assertIsArray($result);
    }

    public function testFindUninstallSteps(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $result = $this->service->findUninstallSteps($template);
        $this->assertIsArray($result);
    }

    public function testExecuteStep(): void
    {
        $template = new AppTemplate();
        $template->setName('Test Template');
        $template->setDescription('Test Description');
        $template->setVersion('1.0.0');

        $step = new AppExecutionStep();
        $step->setTemplate($template);
        $step->setSequence(1);
        $step->setName('Test Step');
        $step->setType(ExecutionStepType::COMMAND);
        $step->setContent('echo "test"');
        $step->setParameterPattern('PARAM_NAME');

        $instance = new AppInstance();
        $instance->setTemplate($template);
        $instance->setTemplateVersion('1.0.0');
        $instance->setName('Test Instance');
        $instance->setNodeId('test-node');
        $instance->setStatus(AppStatus::RUNNING);

        $result = $this->service->executeStep($step, $instance, []);

        $this->assertInstanceOf(AppLifecycleLog::class, $result);
        $this->assertSame($instance, $result->getInstance());
        $this->assertSame($step, $result->getExecutionStep());
        $this->assertIsString($result->getCommandOutput());
        $this->assertIsInt($result->getExitCode());
    }
}
