<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 应用状态枚举
 */
enum AppStatus: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 安装中
     */
    case INSTALLING = 'installing';

    /**
     * 运行中
     */
    case RUNNING = 'running';

    /**
     * 失败
     */
    case FAILED = 'failed';

    /**
     * 卸载中
     */
    case UNINSTALLING = 'uninstalling';

    /**
     * 已停止
     */
    case STOPPED = 'stopped';

    /**
     * 获取枚举值标签
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::INSTALLING => '安装中',
            self::RUNNING => '运行中',
            self::FAILED => '失败',
            self::UNINSTALLING => '卸载中',
            self::STOPPED => '已停止',
        };
    }
}
