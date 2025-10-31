<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppPortMapping;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\AppStatus;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;
use ServerApplicationBundle\Service\AppPortMappingService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * AppPortMappingService单元测试
 *
 * @internal
 */
#[CoversClass(AppPortMappingService::class)]
#[RunTestsInSeparateProcesses]
final class AppPortMappingServiceTest extends AbstractIntegrationTestCase
{
    private AppPortMappingService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(AppPortMappingService::class);
    }

    private function createAppInstance(): AppInstance
    {
        $template = new AppTemplate();
        $template->setName('test-template');
        $template->setVersion('1.0.0');

        $instance = new AppInstance();
        $instance->setName('test-instance');
        $instance->setTemplate($template);
        $instance->setTemplateVersion('1.0.0');
        $instance->setNodeId('test-node');
        $instance->setStatus(AppStatus::RUNNING);

        return $instance;
    }

    private function createAppPortConfiguration(): AppPortConfiguration
    {
        $template = new AppTemplate();
        $template->setName('test-template');
        $template->setVersion('1.0.0');

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
        $instance = $this->createAppInstance();
        $config = $this->createAppPortConfiguration();

        $mapping = new AppPortMapping();
        $mapping->setInstance($instance);
        $mapping->setConfiguration($config);
        $mapping->setActualPort(8080);

        $this->service->save($mapping);
        $this->assertNotNull($mapping->getId());
    }

    public function testRemove(): void
    {
        $instance = $this->createAppInstance();
        $config = $this->createAppPortConfiguration();

        $mapping = new AppPortMapping();
        $mapping->setInstance($instance);
        $mapping->setConfiguration($config);
        $mapping->setActualPort(8080);

        $this->service->save($mapping);
        $id = $mapping->getId();
        $this->service->remove($mapping);

        $this->assertNull($this->service->find((string) $id));
    }

    public function testFindByInstance(): void
    {
        $instance = $this->createAppInstance();

        $result = $this->service->findByInstance($instance);
        $this->assertIsArray($result);
    }

    public function testCreatePortMapping(): void
    {
        $instance = $this->createAppInstance();
        $config = $this->createAppPortConfiguration();

        $mapping = $this->service->createPortMapping($instance, $config, 8080);
        $this->assertSame($instance, $mapping->getInstance());
        $this->assertSame($config, $mapping->getConfiguration());
        $this->assertSame(8080, $mapping->getActualPort());
        $this->assertFalse($mapping->isHealthy());
    }

    public function testCreateAllPortMappings(): void
    {
        $instance = $this->createAppInstance();
        $template = $instance->getTemplate();
        $config = $this->createAppPortConfiguration();

        $templateReflection = new \ReflectionClass($template);
        $portConfigurationsProperty = $templateReflection->getProperty('portConfigurations');
        $portConfigurationsProperty->setAccessible(true);
        $portConfigurationsProperty->setValue($template, new ArrayCollection([$config]));

        $result = $this->service->createAllPortMappings($instance);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($instance, $result[0]->getInstance());
        $this->assertSame($config, $result[0]->getConfiguration());
        $this->assertSame(8080, $result[0]->getActualPort());
    }

    public function testCheckHealth(): void
    {
        $instance = $this->createAppInstance();
        $config = $this->createAppPortConfiguration();
        $config->setHealthCheckType(HealthCheckType::TCP_CONNECT);

        $mapping = new AppPortMapping();
        $mapping->setInstance($instance);
        $mapping->setConfiguration($config);
        $mapping->setActualPort(8080);

        $result = $this->service->checkHealth($mapping, 'localhost');
        $this->assertIsBool($result);
        $this->assertNotNull($mapping->getLastHealthCheck());
    }

    public function testCheckAllHealth(): void
    {
        $instance = $this->createAppInstance();

        $result = $this->service->checkAllHealth($instance, 'localhost');
        $this->assertIsBool($result);
    }
}
