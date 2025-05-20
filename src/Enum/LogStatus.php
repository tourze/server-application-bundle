<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 日志状态枚举
 */
enum LogStatus: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 成功
     */
    case SUCCESS = 'success';

    /**
     * 失败
     */
    case FAILED = 'failed';

    /**
     * 获取枚举值标签
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::SUCCESS => '成功',
            self::FAILED => '失败',
        };
    }
}
