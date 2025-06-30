<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;

/**
 * AppLifecycleLog 测试类
 */
class AppLifecycleLogTest extends TestCase
{
    private AppLifecycleLog $lifecycleLog;

    public function testStringableInterface(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->lifecycleLog);
    }

    public function testSetAndGetInstance(): void
    {
        $instance = $this->createMock(AppInstance::class);

        $this->lifecycleLog->setInstance($instance);
        $this->assertSame($instance, $this->lifecycleLog->getInstance());
    }

    public function testSetAndGetCommandOutput(): void
    {
        $commandOutput = 'Command executed successfully';

        $this->lifecycleLog->setCommandOutput($commandOutput);
        $this->assertSame($commandOutput, $this->lifecycleLog->getCommandOutput());
    }

    public function testSetAndGetMessage(): void
    {
        $message = 'Test message';

        $this->lifecycleLog->setMessage($message);
        $this->assertSame($message, $this->lifecycleLog->getMessage());
    }

    public function testSetAndGetExitCode(): void
    {
        $exitCode = 0;

        $this->lifecycleLog->setExitCode($exitCode);
        $this->assertSame($exitCode, $this->lifecycleLog->getExitCode());
    }

    public function testToString(): void
    {
        $this->lifecycleLog->setMessage('Test message');
        $this->lifecycleLog->setAction(\ServerApplicationBundle\Enum\LifecycleActionType::INSTALL);
        $this->lifecycleLog->setStatus(\ServerApplicationBundle\Enum\LogStatus::SUCCESS);
        $result = (string) $this->lifecycleLog;

        $this->assertNotNull($result);
        $this->assertNotEmpty($result);
    }

    protected function setUp(): void
    {
        $this->lifecycleLog = new AppLifecycleLog();
    }
}
