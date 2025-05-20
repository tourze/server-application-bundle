<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 执行步骤类型枚举
 */
enum ExecutionStepType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 命令
     */
    case COMMAND = 'command';

    /**
     * 脚本
     */
    case SCRIPT = 'script';

    /**
     * 获取枚举值标签
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::COMMAND => '命令',
            self::SCRIPT => '脚本',
        };
    }
}
