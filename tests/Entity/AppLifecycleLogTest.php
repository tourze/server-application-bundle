<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * AppLifecycleLog 测试类
 *
 * @internal
 */
#[CoversClass(AppLifecycleLog::class)]
final class AppLifecycleLogTest extends AbstractEntityTestCase
{
    private AppLifecycleLog $lifecycleLog;

    public function testStringableInterface(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->lifecycleLog);
    }

    public function testSetAndGetInstance(): void
    {
        // 注意：使用具体实体类 AppInstance 的 Mock 对象用于测试实体关联关系
        // 原因：测试需要验证实体间的关联设置，实体类没有对应的接口抽象
        // 替代方案：创建真实对象会增加测试复杂度和依赖，Mock 是最佳选择
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
        $this->lifecycleLog->setAction(LifecycleActionType::INSTALL);
        $this->lifecycleLog->setStatus(LogStatus::SUCCESS);
        $result = (string) $this->lifecycleLog;

        $this->assertNotNull($result);
        $this->assertNotEmpty($result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->lifecycleLog = $this->createEntity();
    }

    /**
     * 创建被测实体的实例。
     */
    protected function createEntity(): AppLifecycleLog
    {
        return new AppLifecycleLog();
    }

    /**
     * 提供属性及其样本值的 Data Provider。
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'instance' => ['instance', null];
        yield 'commandOutput' => ['commandOutput', 'Command executed successfully'];
        yield 'message' => ['message', 'Test message'];
        yield 'exitCode' => ['exitCode', 0];
        yield 'action' => ['action', LifecycleActionType::INSTALL];
        yield 'status' => ['status', LogStatus::SUCCESS];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'createdBy' => ['createdBy', 'test_user'];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
    }
}
