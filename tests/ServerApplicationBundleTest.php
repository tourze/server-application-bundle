<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use ServerApplicationBundle\ServerApplicationBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(ServerApplicationBundle::class)]
#[RunTestsInSeparateProcesses]
final class ServerApplicationBundleTest extends AbstractBundleTestCase
{
}
