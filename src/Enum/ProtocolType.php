<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 协议类型枚举
 */
enum ProtocolType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * TCP协议
     */
    case TCP = 'tcp';

    /**
     * UDP协议
     */
    case UDP = 'udp';

    /**
     * 获取枚举值标签
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::TCP => 'TCP',
            self::UDP => 'UDP',
        };
    }
}
