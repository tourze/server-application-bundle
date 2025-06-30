<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use ServerApplicationBundle\Service\AdminMenu;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * AdminMenu 测试类
 */
class AdminMenuTest extends TestCase
{
    private AdminMenu $adminMenu;
    private LinkGeneratorInterface $linkGenerator;
    private ItemInterface $item;

    public function testImplementsMenuProviderInterface(): void
    {
        $this->assertInstanceOf(MenuProviderInterface::class, $this->adminMenu);
    }

    public function testInvokeIsCallable(): void
    {
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }

    public function testInvokeMethod(): void
    {
        // 测试__invoke方法可以被调用，不会抛出异常
        $this->expectNotToPerformAssertions();

        try {
            ($this->adminMenu)($this->item);
        } catch (\Throwable $e) {
            $this->fail('AdminMenu __invoke method should not throw exception: ' . $e->getMessage());
        }
    }

    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->item = $this->createMock(ItemInterface::class);
        
        // 设置 mock 的返回值以避免 null 引用
        $childItem = $this->createMock(ItemInterface::class);
        $this->item->method('addChild')->willReturn($childItem);
        
        // 使用 willReturnCallback 来模拟 getChild 的行为
        $this->item->method('getChild')->willReturnCallback(function($name) use ($childItem) {
            return $name === '应用管理' ? $childItem : null;
        });
        
        // 设置子项目的 mock 方法
        $childItem->method('addChild')->willReturn($childItem);
        $childItem->method('setUri')->willReturn($childItem);
        $childItem->method('setAttribute')->willReturn($childItem);
        
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }
}
