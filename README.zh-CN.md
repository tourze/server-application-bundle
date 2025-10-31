# server-application-bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](https://github.com/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

用于管理应用程序生命周期、部署和监控的服务器应用程序管理包。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [配置](#配置)
- [使用方法](#使用方法)
- [核心实体](#核心实体)
- [服务](#服务)
- [管理界面](#管理界面)
- [测试](#测试)
- [许可证](#许可证)

## 功能特性

- **应用模板**: 定义可重用的服务器应用程序模板
- **实例管理**: 从模板创建和管理应用程序实例
- **生命周期管理**: 跟踪应用程序生命周期事件（安装、健康检查、卸载）
- **端口配置**: 管理应用程序端口映射和配置
- **执行步骤**: 定义和跟踪部署的自定义执行步骤
- **日志记录**: 全面记录所有生命周期事件和操作
- **多节点支持**: 跨多个服务器节点部署应用程序

## 安装

```bash
composer require tourze/server-application-bundle
```

## 配置

无需额外配置。该包使用 Symfony 的自动装配功能进行自动配置。

## 使用方法

### 核心实体

#### AppTemplate（应用模板）
定义具有配置、健康检查和执行步骤的可重用应用程序模板。

```php
use ServerApplicationBundle\Entity\AppTemplate;

$template = new AppTemplate();
$template->setName('Web 服务器');
$template->setDescription('Nginx Web 服务器模板');
$template->setImage('nginx:latest');
```

#### AppInstance（应用实例）
表示从模板部署的应用程序的实际实例。

```php
use ServerApplicationBundle\Entity\AppInstance;

$instance = new AppInstance();
$instance->setTemplate($template);
$instance->setName('生产环境-Web服务器');
$instance->setNode($serverNode);
```

#### AppLifecycleLog（生命周期日志）
跟踪应用程序实例上执行的所有生命周期事件和操作。

### 服务

- **AppTemplateService**: 管理应用程序模板
- **AppInstanceService**: 处理应用程序实例操作
- **AppLifecycleLogService**: 记录和查询生命周期事件
- **AppPortConfigurationService**: 管理端口配置
- **AppPortMappingService**: 处理主机和容器之间的端口映射
- **AppExecutionStepService**: 管理自定义执行步骤

### 管理界面

该包与 EasyAdmin 集成，提供全面的管理界面：

```php
// 在 /admin 访问管理界面
// 管理模板、实例、日志和配置
```

### 命令行界面

```bash
# 列出所有应用程序模板
bin/console app:template:list

# 部署应用程序实例
bin/console app:instance:deploy <模板ID> <节点ID>

# 检查应用程序健康状态
bin/console app:instance:health-check <实例ID>
```

## 高级用法

### 自定义执行步骤

为复杂的部署场景创建自定义执行步骤：

```php
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Enum\ExecutionStepType;

$step = new AppExecutionStep();
$step->setTemplate($template);
$step->setSequence(1);
$step->setName('安装依赖');
$step->setType(ExecutionStepType::COMMAND);
$step->setContent('apt-get update && apt-get install -y nginx');
$step->setWorkingDirectory('/tmp');
$step->setUseSudo(true);
$step->setTimeout(300);
$step->setRetryCount(3);
```

### 健康检查配置

为您的应用程序配置健康检查：

```php
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;

$portConfig = new AppPortConfiguration();
$portConfig->setTemplate($template);
$portConfig->setPort(80);
$portConfig->setProtocol(ProtocolType::TCP);
$portConfig->setHealthCheckType(HealthCheckType::TCP_CONNECT);
$portConfig->setHealthCheckInterval(60);
$portConfig->setHealthCheckTimeout(5);
$portConfig->setHealthCheckRetries(3);
```

### 环境变量

管理应用程序的环境变量：

```php
$template->setEnvironmentVariables([
    'NODE_ENV' => 'production',
    'PORT' => '8080',
    'DATABASE_URL' => 'mysql://user:pass@localhost/db'
]);

$instance->setEnvironmentVariables([
    'NODE_ENV' => 'production',
    'PORT' => '8080'
]);
```

### 生命周期事件处理

跟踪和响应生命周期事件：

```php
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;

$log = new AppLifecycleLog();
$log->setInstance($instance);
$log->setAction(LifecycleActionType::INSTALL);
$log->setStatus(LogStatus::SUCCESS);
$log->setMessage('应用程序安装成功');
$log->setExecutionTime(45.2);
```

## 架构设计

该包遵循面向服务的架构：

- **实体（Entities）**: 模板、实例、日志等的领域模型
- **仓储（Repositories）**: 具有自定义查询方法的数据访问层
- **服务（Services）**: 操作的业务逻辑层
- **控制器（Controllers）**: 管理界面控制器
- **枚举（Enums）**: 状态和类型的类型安全枚举

## 依赖

- Symfony 6.4+
- PHP 8.1+
- Doctrine ORM 3.0+
- EasyAdmin Bundle 4+
- tourze/server-node-bundle
- tourze/doctrine-timestamp-bundle
- tourze/doctrine-ip-bundle
- tourze/doctrine-track-bundle
- tourze/doctrine-user-bundle

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/server-application-bundle/tests
```

## 许可证

MIT 许可证。详见 LICENSE 文件。