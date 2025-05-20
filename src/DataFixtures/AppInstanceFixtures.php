<?php

declare(strict_types=1);

namespace ServerApplicationBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\AppStatus;

/**
 * 应用实例数据填充
 */
class AppInstanceFixtures extends Fixture implements DependentFixtureInterface
{
    // 使用常量定义引用名称
    public const WEB_INSTANCE_REFERENCE = 'web-instance';
    public const DB_INSTANCE_REFERENCE = 'db-instance';
    public const API_INSTANCE_REFERENCE = 'api-instance';

    public function load(ObjectManager $manager): void
    {
        // 创建Web应用实例
        $webInstance = new AppInstance();
        $webInstance->setTemplate($this->getReference(AppTemplateFixtures::WEB_TEMPLATE_REFERENCE, AppTemplate::class));
        $webInstance->setTemplateVersion('1.0.0');
        $webInstance->setNodeId('node-001');
        $webInstance->setName('前端应用实例-生产环境');
        $webInstance->setStatus(AppStatus::RUNNING);
        $webInstance->setEnvironmentVariables([
            'NODE_ENV' => 'production',
            'PORT' => '8080',
        ]);
        $webInstance->setHealthy(true);
        $webInstance->setLastHealthCheck(new \DateTime());

        $manager->persist($webInstance);

        // 创建数据库应用实例
        $dbInstance = new AppInstance();
        $dbInstance->setTemplate($this->getReference(AppTemplateFixtures::DATABASE_TEMPLATE_REFERENCE, AppTemplate::class));
        $dbInstance->setTemplateVersion('1.0.0');
        $dbInstance->setNodeId('node-002');
        $dbInstance->setName('MySQL数据库实例-生产环境');
        $dbInstance->setStatus(AppStatus::RUNNING);
        $dbInstance->setEnvironmentVariables([
            'MYSQL_ROOT_PASSWORD' => 'secure_root_password',
            'MYSQL_DATABASE' => 'production_db',
            'MYSQL_USER' => 'production_user',
            'MYSQL_PASSWORD' => 'secure_password',
        ]);
        $dbInstance->setHealthy(true);
        $dbInstance->setLastHealthCheck(new \DateTime());

        $manager->persist($dbInstance);

        // 创建API应用实例
        $apiInstance = new AppInstance();
        $apiInstance->setTemplate($this->getReference(AppTemplateFixtures::API_TEMPLATE_REFERENCE, AppTemplate::class));
        $apiInstance->setTemplateVersion('1.0.0');
        $apiInstance->setNodeId('node-001');
        $apiInstance->setName('API服务实例-生产环境');
        $apiInstance->setStatus(AppStatus::INSTALLING);
        $apiInstance->setEnvironmentVariables([
            'APP_ENV' => 'prod',
            'APP_DEBUG' => '0',
            'DATABASE_URL' => 'mysql://production_user:secure_password@database:3306/production_db',
        ]);
        $apiInstance->setHealthy(false);

        $manager->persist($apiInstance);

        // 创建一个已停止的Web应用实例
        $stoppedWebInstance = new AppInstance();
        $stoppedWebInstance->setTemplate($this->getReference(AppTemplateFixtures::WEB_TEMPLATE_REFERENCE, AppTemplate::class));
        $stoppedWebInstance->setTemplateVersion('1.0.0');
        $stoppedWebInstance->setNodeId('node-003');
        $stoppedWebInstance->setName('前端应用实例-测试环境');
        $stoppedWebInstance->setStatus(AppStatus::STOPPED);
        $stoppedWebInstance->setEnvironmentVariables([
            'NODE_ENV' => 'test',
            'PORT' => '3000',
        ]);
        $stoppedWebInstance->setHealthy(false);
        $stoppedWebInstance->setLastHealthCheck(new \DateTime('-1 day'));

        $manager->persist($stoppedWebInstance);

        $manager->flush();

        // 添加引用以便其他Fixture使用
        $this->addReference(self::WEB_INSTANCE_REFERENCE, $webInstance);
        $this->addReference(self::DB_INSTANCE_REFERENCE, $dbInstance);
        $this->addReference(self::API_INSTANCE_REFERENCE, $apiInstance);
    }

    public function getDependencies(): array
    {
        return [
            AppTemplateFixtures::class,
        ];
    }
}
