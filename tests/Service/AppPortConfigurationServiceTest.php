<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;
use ServerApplicationBundle\Service\AppPortConfigurationService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * AppPortConfigurationService单元测试
 *
 * @internal
 */
#[CoversClass(AppPortConfigurationService::class)]
#[RunTestsInSeparateProcesses]
final class AppPortConfigurationServiceTest extends AbstractIntegrationTestCase
{
    private AppPortConfigurationService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(AppPortConfigurationService::class);
    }

    private function createAppTemplate(): AppTemplate
    {
        $template = new AppTemplate();
        $template->setName('test-template');
        $template->setVersion('1.0.0');

        return $template;
    }

    private function createAppPortConfiguration(): AppPortConfiguration
    {
        $template = $this->createAppTemplate();

        $config = new AppPortConfiguration();
        $config->setTemplate($template);
        $config->setPort(8080);
        $config->setProtocol(ProtocolType::TCP);
        $config->setHealthCheckType(HealthCheckType::TCP_CONNECT);

        return $config;
    }

    public function testFindAll(): void
    {
        $result = $this->service->findAll();
        $this->assertIsArray($result);
    }

    public function testFind(): void
    {
        $result = $this->service->find('test-id');
        $this->assertNull($result);
    }

    public function testFindNotFound(): void
    {
        $result = $this->service->find('non-existing-id');
        $this->assertNull($result);
    }

    public function testSave(): void
    {
        $config = $this->createAppPortConfiguration();

        $this->service->save($config);
        $this->assertNotNull($config->getId());
    }

    public function testRemove(): void
    {
        $config = $this->createAppPortConfiguration();

        $this->service->save($config);
        $id = $config->getId();
        $this->service->remove($config);

        $this->assertNull($this->service->find((string) $id));
    }

    public function testFindByTemplate(): void
    {
        $template = $this->createAppTemplate();

        $result = $this->service->findByTemplate($template);
        $this->assertIsArray($result);
    }

    public function testCheckHealth(): void
    {
        $config = new AppPortConfiguration();
        $config->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $config->setHealthCheckTimeout(1);
        $config->setHealthCheckRetries(1);

        $result = $this->service->checkHealth($config, 8080, 'localhost');
        $this->assertIsBool($result);
    }

    public function testCheckHealthWithCommandType(): void
    {
        $config = new AppPortConfiguration();
        $config->setHealthCheckType(HealthCheckType::COMMAND);
        $config->setHealthCheckConfig(['command' => 'echo "test"', 'successExitCode' => 0]);

        $result = $this->service->checkHealth($config, 8080, 'localhost');
        $this->assertIsBool($result);
    }

    public function testCheckHealthWithUdpType(): void
    {
        $config = new AppPortConfiguration();
        $config->setHealthCheckType(HealthCheckType::UDP_PORT_CHECK);

        $result = $this->service->checkHealth($config, 8080, 'localhost');
        $this->assertIsBool($result);
    }
}
