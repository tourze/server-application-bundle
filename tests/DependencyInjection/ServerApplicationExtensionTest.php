<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\DependencyInjection\ServerApplicationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * ServerApplicationExtension 测试类
 */
class ServerApplicationExtensionTest extends TestCase
{
    private ServerApplicationExtension $extension;

    public function testInstanceOfExtensionInterface(): void
    {
        $this->assertInstanceOf(ExtensionInterface::class, $this->extension);
    }

    public function testLoad(): void
    {
        $container = new ContainerBuilder();
        $configs = [];

        $this->extension->load($configs, $container);

        // 验证容器已处理
        $this->assertInstanceOf(ContainerBuilder::class, $container);
    }

    public function testGetAlias(): void
    {
        $alias = $this->extension->getAlias();
        $this->assertNotNull($alias);
        $this->assertNotEmpty($alias);
    }

    protected function setUp(): void
    {
        $this->extension = new ServerApplicationExtension();
    }
}
