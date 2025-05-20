<?php

declare(strict_types=1);

namespace ServerApplicationBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;

/**
 * 应用生命周期日志数据填充
 */
class AppLifecycleLogFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // 为Web应用实例创建安装成功的日志
        $webInstallLog = new AppLifecycleLog();
        $webInstallLog->setInstance($this->getReference(AppInstanceFixtures::WEB_INSTANCE_REFERENCE, AppInstance::class));
        $webInstallLog->setAction(LifecycleActionType::INSTALL);
        $webInstallLog->setStatus(LogStatus::SUCCESS);
        $webInstallLog->setMessage('Web应用成功安装');
        $webInstallLog->setCommandOutput('所有安装步骤已成功完成，应用已部署并运行。');
        $webInstallLog->setExitCode(0);
        $webInstallLog->setExecutionTime(123.45);

        $manager->persist($webInstallLog);

        // 为数据库应用实例创建安装成功的日志
        $dbInstallLog = new AppLifecycleLog();
        $dbInstallLog->setInstance($this->getReference(AppInstanceFixtures::DB_INSTANCE_REFERENCE, AppInstance::class));
        $dbInstallLog->setAction(LifecycleActionType::INSTALL);
        $dbInstallLog->setStatus(LogStatus::SUCCESS);
        $dbInstallLog->setMessage('数据库应用成功安装');
        $dbInstallLog->setCommandOutput('MySQL数据库已成功安装并正常运行。');
        $dbInstallLog->setExitCode(0);
        $dbInstallLog->setExecutionTime(45.67);

        $manager->persist($dbInstallLog);

        // 为API应用实例创建安装中的日志
        $apiInstallLog = new AppLifecycleLog();
        $apiInstallLog->setInstance($this->getReference(AppInstanceFixtures::API_INSTANCE_REFERENCE, AppInstance::class));
        $apiInstallLog->setAction(LifecycleActionType::INSTALL);
        $apiInstallLog->setStatus(LogStatus::FAILED);
        $apiInstallLog->setMessage('API应用安装失败');
        $apiInstallLog->setCommandOutput('安装过程中遇到错误，请检查日志。');
        $apiInstallLog->setExitCode(1);
        $apiInstallLog->setExecutionTime(67.89);

        $manager->persist($apiInstallLog);

        // 为Web应用实例创建健康检查成功的日志
        $webHealthLog = new AppLifecycleLog();
        $webHealthLog->setInstance($this->getReference(AppInstanceFixtures::WEB_INSTANCE_REFERENCE, AppInstance::class));
        $webHealthLog->setAction(LifecycleActionType::HEALTH_CHECK);
        $webHealthLog->setStatus(LogStatus::SUCCESS);
        $webHealthLog->setMessage('Web应用健康检查通过');
        $webHealthLog->setCommandOutput('所有端口检查通过。');
        $webHealthLog->setExitCode(0);
        $webHealthLog->setExecutionTime(1.23);

        $manager->persist($webHealthLog);

        // 为API应用实例创建健康检查失败的日志
        $apiHealthLog = new AppLifecycleLog();
        $apiHealthLog->setInstance($this->getReference(AppInstanceFixtures::API_INSTANCE_REFERENCE, AppInstance::class));
        $apiHealthLog->setAction(LifecycleActionType::HEALTH_CHECK);
        $apiHealthLog->setStatus(LogStatus::FAILED);
        $apiHealthLog->setMessage('API应用健康检查失败');
        $apiHealthLog->setCommandOutput('端口80检查失败。');
        $apiHealthLog->setExitCode(1);
        $apiHealthLog->setExecutionTime(1.23);

        $manager->persist($apiHealthLog);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AppInstanceFixtures::class,
        ];
    }
}
