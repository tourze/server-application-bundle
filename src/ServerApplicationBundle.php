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
        ];
    }
}
