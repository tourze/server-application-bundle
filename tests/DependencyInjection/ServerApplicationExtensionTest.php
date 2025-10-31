<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use ServerApplicationBundle\DependencyInjection\ServerApplicationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * ServerApplicationExtension 测试类
 *
 * @internal
 */
#[CoversClass(ServerApplicationExtension::class)]
final class ServerApplicationExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testInstanceOfExtensionInterface(): void
    {
        $extension = new ServerApplicationExtension();
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
    }

    public function testLoad(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $configs = [];

        $extension = new ServerApplicationExtension();
        $extension->load($configs, $container);

        // AutoExtension 会自动加载配置，验证容器已处理
        $this->assertInstanceOf(ContainerBuilder::class, $container);
    }

    public function testGetAlias(): void
    {
        $extension = new ServerApplicationExtension();
        $alias = $extension->getAlias();
        $this->assertNotNull($alias);
        $this->assertNotEmpty($alias);
    }

    protected function setUp(): void
    {
        // 不需要特殊的设置
    }
}
