<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 生命周期操作类型枚举
 */
enum LifecycleActionType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 安装
     */
    case INSTALL = 'install';

    /**
     * 健康检测
     */
    case HEALTH_CHECK = 'health_check';

    /**
     * 卸载
     */
    case UNINSTALL = 'uninstall';

    /**
     * 获取枚举值标签
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::INSTALL => '安装',
            self::HEALTH_CHECK => '健康检测',
            self::UNINSTALL => '卸载',
        };
    }
}
