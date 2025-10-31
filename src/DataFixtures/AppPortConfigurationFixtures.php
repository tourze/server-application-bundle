<?php

declare(strict_types=1);

namespace ServerApplicationBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 应用端口配置数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class AppPortConfigurationFixtures extends Fixture implements DependentFixtureInterface
{
    // 使用常量定义引用名称
    public const WEB_HTTP_PORT_REFERENCE = 'web-http-port';
    public const DB_MYSQL_PORT_REFERENCE = 'db-mysql-port';
    public const API_HTTP_PORT_REFERENCE = 'api-http-port';

    public function load(ObjectManager $manager): void
    {
        // 为Web应用模板添加HTTP端口配置
        $webHttpPort = new AppPortConfiguration();
        $webHttpPort->setTemplate($this->getReference(AppTemplateFixtures::WEB_TEMPLATE_REFERENCE, AppTemplate::class));
        $webHttpPort->setPort(80);
        $webHttpPort->setProtocol(ProtocolType::TCP);
        $webHttpPort->setDescription('Web应用HTTP服务端口');
        $webHttpPort->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $webHttpPort->setHealthCheckConfig(['timeout' => 5]);
        $webHttpPort->setHealthCheckInterval(60);
        $webHttpPort->setHealthCheckTimeout(5);
        $webHttpPort->setHealthCheckRetries(3);

        $manager->persist($webHttpPort);

        // 为数据库应用模板添加MySQL端口配置
        $dbMysqlPort = new AppPortConfiguration();
        $dbMysqlPort->setTemplate($this->getReference(AppTemplateFixtures::DATABASE_TEMPLATE_REFERENCE, AppTemplate::class));
        $dbMysqlPort->setPort(3306);
        $dbMysqlPort->setProtocol(ProtocolType::TCP);
        $dbMysqlPort->setDescription('MySQL数据库服务端口');
        $dbMysqlPort->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $dbMysqlPort->setHealthCheckConfig(['timeout' => 5]);
        $dbMysqlPort->setHealthCheckInterval(60);
        $dbMysqlPort->setHealthCheckTimeout(5);
        $dbMysqlPort->setHealthCheckRetries(3);

        $manager->persist($dbMysqlPort);

        // 为API应用模板添加HTTP端口配置
        $apiHttpPort = new AppPortConfiguration();
        $apiHttpPort->setTemplate($this->getReference(AppTemplateFixtures::API_TEMPLATE_REFERENCE, AppTemplate::class));
        $apiHttpPort->setPort(80);
        $apiHttpPort->setProtocol(ProtocolType::TCP);
        $apiHttpPort->setDescription('API服务HTTP端口');
        $apiHttpPort->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $apiHttpPort->setHealthCheckConfig(['timeout' => 5]);
        $apiHttpPort->setHealthCheckInterval(60);
        $apiHttpPort->setHealthCheckTimeout(5);
        $apiHttpPort->setHealthCheckRetries(3);

        $manager->persist($apiHttpPort);

        // 为API应用模板添加HTTPS端口配置
        $apiHttpsPort = new AppPortConfiguration();
        $apiHttpsPort->setTemplate($this->getReference(AppTemplateFixtures::API_TEMPLATE_REFERENCE, AppTemplate::class));
        $apiHttpsPort->setPort(443);
        $apiHttpsPort->setProtocol(ProtocolType::TCP);
        $apiHttpsPort->setDescription('API服务HTTPS端口');
        $apiHttpsPort->setHealthCheckType(HealthCheckType::TCP_CONNECT);
        $apiHttpsPort->setHealthCheckConfig(['timeout' => 5]);
        $apiHttpsPort->setHealthCheckInterval(60);
        $apiHttpsPort->setHealthCheckTimeout(5);
        $apiHttpsPort->setHealthCheckRetries(3);

        $manager->persist($apiHttpsPort);

        $manager->flush();

        // 添加引用以便其他Fixture使用
        $this->addReference(self::WEB_HTTP_PORT_REFERENCE, $webHttpPort);
        $this->addReference(self::DB_MYSQL_PORT_REFERENCE, $dbMysqlPort);
        $this->addReference(self::API_HTTP_PORT_REFERENCE, $apiHttpPort);
    }

    public function getDependencies(): array
    {
        return [
            AppTemplateFixtures::class,
        ];
    }
}
