<?php

declare(strict_types=1);

namespace ServerApplicationBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\ExecutionStepType;

/**
 * 应用执行步骤数据填充
 */
class AppExecutionStepFixtures extends Fixture implements DependentFixtureInterface
{
    // 使用常量定义引用名称
    public const WEB_NGINX_SETUP_STEP_REFERENCE = 'web-nginx-setup-step';
    public const DB_MYSQL_INSTALL_STEP_REFERENCE = 'db-mysql-install-step';
    public const API_PHP_SETUP_STEP_REFERENCE = 'api-php-setup-step';

    public function load(ObjectManager $manager): void
    {
        // 为Web应用模板创建Nginx安装步骤
        $webNginxSetupStep = new AppExecutionStep();
        $webNginxSetupStep->setTemplate($this->getReference(AppTemplateFixtures::WEB_TEMPLATE_REFERENCE, AppTemplate::class));
        $webNginxSetupStep->setSequence(1);
        $webNginxSetupStep->setName('安装并配置Nginx');
        $webNginxSetupStep->setDescription('安装Nginx服务器并配置站点');
        $webNginxSetupStep->setType(ExecutionStepType::COMMAND);
        $webNginxSetupStep->setContent('apt-get update && apt-get install -y nginx && service nginx start');
        $webNginxSetupStep->setWorkingDirectory('/tmp');
        $webNginxSetupStep->setUseSudo(true);
        $webNginxSetupStep->setTimeout(300);
        $webNginxSetupStep->setParameters([
            [
                'name' => 'NGINX_VERSION',
                'description' => 'Nginx版本',
                'default' => 'latest',
                'required' => false,
            ],
        ]);
        $webNginxSetupStep->setParameterPattern('{{PARAM_NAME}}');
        $webNginxSetupStep->setStopOnError(true);
        $webNginxSetupStep->setRetryCount(3);
        $webNginxSetupStep->setRetryInterval(10);

        $manager->persist($webNginxSetupStep);

        // 为Web应用模板创建配置文件步骤
        $webConfigStep = new AppExecutionStep();
        $webConfigStep->setTemplate($this->getReference(AppTemplateFixtures::WEB_TEMPLATE_REFERENCE, AppTemplate::class));
        $webConfigStep->setSequence(2);
        $webConfigStep->setName('配置Nginx站点');
        $webConfigStep->setDescription('创建Nginx站点配置文件并启用');
        $webConfigStep->setType(ExecutionStepType::SCRIPT);
        $webConfigStep->setContent('#!/bin/bash
cat > /etc/nginx/sites-available/default <<EOF
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    root /var/www/html;
    index index.html index.htm;
    server_name _;
    location / {
        try_files \$uri \$uri/ =404;
    }
}
EOF
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
');
        $webConfigStep->setWorkingDirectory('/etc/nginx');
        $webConfigStep->setUseSudo(true);
        $webConfigStep->setTimeout(60);
        $webConfigStep->setStopOnError(true);
        $webConfigStep->setRetryCount(2);
        $webConfigStep->setRetryInterval(5);

        $manager->persist($webConfigStep);

        // 为数据库应用模板创建MySQL安装步骤
        $dbMysqlInstallStep = new AppExecutionStep();
        $dbMysqlInstallStep->setTemplate($this->getReference(AppTemplateFixtures::DATABASE_TEMPLATE_REFERENCE, AppTemplate::class));
        $dbMysqlInstallStep->setSequence(1);
        $dbMysqlInstallStep->setName('安装MySQL数据库');
        $dbMysqlInstallStep->setDescription('安装MySQL服务器并设置root密码');
        $dbMysqlInstallStep->setType(ExecutionStepType::COMMAND);
        $dbMysqlInstallStep->setContent('DEBIAN_FRONTEND=noninteractive apt-get update && apt-get install -y mysql-server && systemctl start mysql');
        $dbMysqlInstallStep->setWorkingDirectory('/tmp');
        $dbMysqlInstallStep->setUseSudo(true);
        $dbMysqlInstallStep->setTimeout(600);
        $dbMysqlInstallStep->setParameters([
            [
                'name' => 'MYSQL_VERSION',
                'description' => 'MySQL版本',
                'default' => '8.0',
                'required' => false,
            ],
        ]);
        $dbMysqlInstallStep->setStopOnError(true);
        $dbMysqlInstallStep->setRetryCount(2);
        $dbMysqlInstallStep->setRetryInterval(30);

        $manager->persist($dbMysqlInstallStep);

        // 为数据库应用模板创建数据库安全配置步骤
        $dbSecureStep = new AppExecutionStep();
        $dbSecureStep->setTemplate($this->getReference(AppTemplateFixtures::DATABASE_TEMPLATE_REFERENCE, AppTemplate::class));
        $dbSecureStep->setSequence(2);
        $dbSecureStep->setName('配置MySQL安全选项');
        $dbSecureStep->setDescription('设置MySQL root密码并删除测试数据库');
        $dbSecureStep->setType(ExecutionStepType::SCRIPT);
        $dbSecureStep->setContent('#!/bin/bash
mysql -e "UPDATE mysql.user SET Password=PASSWORD(\'{{ROOT_PASSWORD}}\') WHERE User=\'root\';"
mysql -e "DELETE FROM mysql.user WHERE User=\'\';"
mysql -e "DELETE FROM mysql.user WHERE User=\'root\' AND Host NOT IN (\'localhost\', \'127.0.0.1\', \'::1\');"
mysql -e "DROP DATABASE IF EXISTS test;"
mysql -e "DELETE FROM mysql.db WHERE Db=\'test\' OR Db=\'test\\_%\';"
mysql -e "FLUSH PRIVILEGES;"
');
        $dbSecureStep->setWorkingDirectory('/tmp');
        $dbSecureStep->setUseSudo(true);
        $dbSecureStep->setTimeout(120);
        $dbSecureStep->setParameters([
            [
                'name' => 'ROOT_PASSWORD',
                'description' => 'Root用户密码',
                'default' => 'root_password',
                'required' => true,
            ],
        ]);
        $dbSecureStep->setStopOnError(true);
        $dbSecureStep->setRetryCount(1);
        $dbSecureStep->setRetryInterval(10);

        $manager->persist($dbSecureStep);

        // 为API应用模板创建PHP设置步骤
        $apiPhpSetupStep = new AppExecutionStep();
        $apiPhpSetupStep->setTemplate($this->getReference(AppTemplateFixtures::API_TEMPLATE_REFERENCE, AppTemplate::class));
        $apiPhpSetupStep->setSequence(1);
        $apiPhpSetupStep->setName('安装PHP和扩展');
        $apiPhpSetupStep->setDescription('安装PHP及必要的扩展');
        $apiPhpSetupStep->setType(ExecutionStepType::COMMAND);
        $apiPhpSetupStep->setContent('apt-get update && apt-get install -y php8.1-fpm php8.1-mysql php8.1-curl php8.1-json php8.1-mbstring php8.1-xml');
        $apiPhpSetupStep->setWorkingDirectory('/tmp');
        $apiPhpSetupStep->setUseSudo(true);
        $apiPhpSetupStep->setTimeout(300);
        $apiPhpSetupStep->setParameters([
            [
                'name' => 'PHP_VERSION',
                'description' => 'PHP版本',
                'default' => '8.1',
                'required' => false,
            ],
        ]);
        $apiPhpSetupStep->setStopOnError(true);
        $apiPhpSetupStep->setRetryCount(2);
        $apiPhpSetupStep->setRetryInterval(15);

        $manager->persist($apiPhpSetupStep);

        // 为API应用模板创建Composer步骤
        $apiComposerStep = new AppExecutionStep();
        $apiComposerStep->setTemplate($this->getReference(AppTemplateFixtures::API_TEMPLATE_REFERENCE, AppTemplate::class));
        $apiComposerStep->setSequence(2);
        $apiComposerStep->setName('安装Composer');
        $apiComposerStep->setDescription('安装Composer包管理器');
        $apiComposerStep->setType(ExecutionStepType::SCRIPT);
        $apiComposerStep->setContent('#!/bin/bash
php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink(\'composer-setup.php\');"
composer --version
');
        $apiComposerStep->setWorkingDirectory('/tmp');
        $apiComposerStep->setUseSudo(true);
        $apiComposerStep->setTimeout(180);
        $apiComposerStep->setStopOnError(false);
        $apiComposerStep->setRetryCount(3);
        $apiComposerStep->setRetryInterval(20);

        $manager->persist($apiComposerStep);

        $manager->flush();

        // 添加引用以便其他Fixture使用
        $this->addReference(self::WEB_NGINX_SETUP_STEP_REFERENCE, $webNginxSetupStep);
        $this->addReference(self::DB_MYSQL_INSTALL_STEP_REFERENCE, $dbMysqlInstallStep);
        $this->addReference(self::API_PHP_SETUP_STEP_REFERENCE, $apiPhpSetupStep);
    }

    public function getDependencies(): array
    {
        return [
            AppTemplateFixtures::class,
        ];
    }
} 