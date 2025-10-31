<?php

namespace ServerApplicationBundle\Service;

use Knp\Menu\ItemInterface;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppPortMapping;
use ServerApplicationBundle\Entity\AppTemplate;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('应用管理')) {
            $item->addChild('应用管理');
        }

        $appManagementMenu = $item->getChild('应用管理');
        if (null === $appManagementMenu) {
            return;
        }

        $appManagementMenu
            ->addChild('应用实例')
            ->setUri($this->linkGenerator->getCurdListPage(AppInstance::class))
            ->setAttribute('icon', 'fas fa-cube')
        ;

        $appManagementMenu
            ->addChild('应用模板')
            ->setUri($this->linkGenerator->getCurdListPage(AppTemplate::class))
            ->setAttribute('icon', 'fas fa-copy')
        ;

        $appManagementMenu
            ->addChild('执行步骤')
            ->setUri($this->linkGenerator->getCurdListPage(AppExecutionStep::class))
            ->setAttribute('icon', 'fas fa-list-check')
        ;

        $appManagementMenu
            ->addChild('端口配置')
            ->setUri($this->linkGenerator->getCurdListPage(AppPortConfiguration::class))
            ->setAttribute('icon', 'fas fa-network-wired')
        ;

        $appManagementMenu
            ->addChild('端口映射')
            ->setUri($this->linkGenerator->getCurdListPage(AppPortMapping::class))
            ->setAttribute('icon', 'fas fa-route')
        ;

        $appManagementMenu
            ->addChild('生命周期日志')
            ->setUri($this->linkGenerator->getCurdListPage(AppLifecycleLog::class))
            ->setAttribute('icon', 'fas fa-history')
        ;
    }
}
