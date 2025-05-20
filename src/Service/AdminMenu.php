<?php

namespace ServerApplicationBundle\Service;

use Knp\Menu\ItemInterface;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('应用管理')) {
            $item->addChild('应用管理');
        }

        $item->getChild('应用管理')
            ->addChild('应用实例')
            ->setUri($this->linkGenerator->getCurdListPage(AppInstance::class))
            ->setAttribute('icon', 'fas fa-cube');

        $item->getChild('应用管理')
            ->addChild('应用模板')
            ->setUri($this->linkGenerator->getCurdListPage(AppTemplate::class))
            ->setAttribute('icon', 'fas fa-copy');

        $item->getChild('应用管理')
            ->addChild('执行步骤')
            ->setUri($this->linkGenerator->getCurdListPage(AppExecutionStep::class))
            ->setAttribute('icon', 'fas fa-list-check');

        $item->getChild('应用管理')
            ->addChild('端口配置')
            ->setUri($this->linkGenerator->getCurdListPage(AppPortConfiguration::class))
            ->setAttribute('icon', 'fas fa-network-wired');
    }
}
