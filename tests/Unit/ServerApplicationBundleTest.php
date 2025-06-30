<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\ServerApplicationBundle;

class ServerApplicationBundleTest extends TestCase
{
    public function testBundleInstantiation(): void
    {
        $bundle = new ServerApplicationBundle();
        
        $this->assertInstanceOf(ServerApplicationBundle::class, $bundle);
    }

    public function testGetBundleDependencies(): void
    {
        $dependencies = ServerApplicationBundle::getBundleDependencies();
        
        $this->assertNotNull($dependencies);
        $this->assertArrayHasKey(\ServerNodeBundle\ServerNodeBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\DoctrineIpBundle\DoctrineIpBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\DoctrineTrackBundle\DoctrineTrackBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\DoctrineUserBundle\DoctrineUserBundle::class, $dependencies);
    }
}