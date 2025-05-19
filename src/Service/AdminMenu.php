<?php

namespace ServerApplicationBundle\Service;

use Knp\Menu\ItemInterface;
use ServerApplicationBundle\Entity\Application;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('服务器管理')) {
            $item->addChild('服务器管理');
        }

        $item->getChild('服务器管理')
            ->addChild('应用管理')
            ->setUri($this->linkGenerator->getCurdListPage(Application::class))
            ->setAttribute('icon', 'fas fa-cogs');
    }
}
