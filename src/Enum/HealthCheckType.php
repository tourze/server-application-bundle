<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 健康检测类型枚举
 */
enum HealthCheckType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * TCP连接检测
     */
    case TCP_CONNECT = 'tcp_connect';

    /**
     * UDP端口检测
     */
    case UDP_PORT_CHECK = 'udp_port_check';

    /**
     * 命令检测
     */
    case COMMAND = 'command';

    /**
     * 获取枚举值标签
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::TCP_CONNECT => 'TCP连接检测',
            self::UDP_PORT_CHECK => 'UDP端口检测',
            self::COMMAND => '命令检测',
        };
    }
}
