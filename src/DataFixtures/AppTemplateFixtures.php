<?php

declare(strict_types=1);

namespace ServerApplicationBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use ServerApplicationBundle\Entity\AppTemplate;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 应用模板数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class AppTemplateFixtures extends Fixture
{
    // 使用常量定义引用名称
    public const WEB_TEMPLATE_REFERENCE = 'web-template';
    public const DATABASE_TEMPLATE_REFERENCE = 'database-template';
    public const API_TEMPLATE_REFERENCE = 'api-template';

    public function load(ObjectManager $manager): void
    {
        // 创建Web应用模板
        $webTemplate = new AppTemplate();
        $webTemplate->setName('Web前端应用');
        $webTemplate->setDescription('通用的Web前端应用模板，适用于Vue、React等前端框架');
        $webTemplate->setEnabled(true);
        $webTemplate->setVersion('1.0.0');
        $webTemplate->setIsLatest(true);
        $webTemplate->setEnvironmentVariables([
            'NODE_ENV' => 'production',
            'PORT' => '80',
        ]);

        $manager->persist($webTemplate);

        // 创建数据库应用模板
        $dbTemplate = new AppTemplate();
        $dbTemplate->setName('MySQL数据库');
        $dbTemplate->setDescription('MySQL数据库服务，提供高性能的关系型数据库服务');
        $dbTemplate->setEnabled(true);
        $dbTemplate->setVersion('1.0.0');
        $dbTemplate->setIsLatest(true);
        $dbTemplate->setEnvironmentVariables([
            'MYSQL_ROOT_PASSWORD' => 'root_password',
            'MYSQL_DATABASE' => 'default_db',
            'MYSQL_USER' => 'user',
            'MYSQL_PASSWORD' => 'password',
        ]);

        $manager->persist($dbTemplate);

        // 创建API应用模板
        $apiTemplate = new AppTemplate();
        $apiTemplate->setName('PHP API服务');
        $apiTemplate->setDescription('基于PHP的API服务模板，适用于RESTful API开发');
        $apiTemplate->setEnabled(true);
        $apiTemplate->setVersion('1.0.0');
        $apiTemplate->setIsLatest(true);
        $apiTemplate->setEnvironmentVariables([
            'APP_ENV' => 'prod',
            'APP_DEBUG' => '0',
            'DATABASE_URL' => 'mysql://user:password@db:3306/default_db',
        ]);

        $manager->persist($apiTemplate);
        $manager->flush();

        // 添加引用以便其他Fixture使用
        $this->addReference(self::WEB_TEMPLATE_REFERENCE, $webTemplate);
        $this->addReference(self::DATABASE_TEMPLATE_REFERENCE, $dbTemplate);
        $this->addReference(self::API_TEMPLATE_REFERENCE, $apiTemplate);
    }
}
