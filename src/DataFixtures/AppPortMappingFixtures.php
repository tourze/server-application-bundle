<?php

declare(strict_types=1);

namespace ServerApplicationBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppPortMapping;

/**
 * 应用端口映射数据填充
 */
class AppPortMappingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // 为Web应用实例创建端口映射
        $webPortMapping = new AppPortMapping();
        $webPortMapping->setInstance($this->getReference(AppInstanceFixtures::WEB_INSTANCE_REFERENCE, AppInstance::class));
        $webPortMapping->setConfiguration($this->getReference(AppPortConfigurationFixtures::WEB_HTTP_PORT_REFERENCE, AppPortConfiguration::class));
        $webPortMapping->setActualPort(30080);
        $webPortMapping->setHealthy(true);
        $webPortMapping->setLastHealthCheck(new \DateTime());

        $manager->persist($webPortMapping);

        // 为数据库应用实例创建端口映射
        $dbPortMapping = new AppPortMapping();
        $dbPortMapping->setInstance($this->getReference(AppInstanceFixtures::DB_INSTANCE_REFERENCE, AppInstance::class));
        $dbPortMapping->setConfiguration($this->getReference(AppPortConfigurationFixtures::DB_MYSQL_PORT_REFERENCE, AppPortConfiguration::class));
        $dbPortMapping->setActualPort(33306);
        $dbPortMapping->setHealthy(true);
        $dbPortMapping->setLastHealthCheck(new \DateTime());

        $manager->persist($dbPortMapping);

        // 为API应用实例创建端口映射
        $apiPortMapping = new AppPortMapping();
        $apiPortMapping->setInstance($this->getReference(AppInstanceFixtures::API_INSTANCE_REFERENCE, AppInstance::class));
        $apiPortMapping->setConfiguration($this->getReference(AppPortConfigurationFixtures::API_HTTP_PORT_REFERENCE, AppPortConfiguration::class));
        $apiPortMapping->setActualPort(30081);
        $apiPortMapping->setHealthy(false);
        $apiPortMapping->setLastHealthCheck(new \DateTime());

        $manager->persist($apiPortMapping);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AppInstanceFixtures::class,
            AppPortConfigurationFixtures::class,
        ];
    }
}
