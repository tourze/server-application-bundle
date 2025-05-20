<?php

namespace ServerApplicationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class ServerApplicationBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \ServerNodeBundle\ServerNodeBundle::class => ['all' => true],
            \Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle::class => ['all' => true],
            \Tourze\DoctrineIpBundle\DoctrineIpBundle::class => ['all' => true],
            \Tourze\DoctrineTrackBundle\DoctrineTrackBundle::class => ['all' => true],
            \Tourze\DoctrineUserBundle\DoctrineUserBundle::class => ['all' => true],
        ];
    }
}
