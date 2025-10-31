# server-application-bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](https://github.com/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

Server application management bundle for managing application lifecycle, deployment, and monitoring.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Core Entities](#core-entities)
- [Services](#services)
- [Admin Interface](#admin-interface)
- [Testing](#testing)
- [License](#license)

## Features

- **Application Templates**: Define reusable templates for server applications
- **Instance Management**: Create and manage application instances from templates
- **Lifecycle Management**: Track application lifecycle events (install, health check, uninstall)
- **Port Configuration**: Manage application port mappings and configurations
- **Execution Steps**: Define and track custom execution steps for deployments
- **Logging**: Comprehensive logging of all lifecycle events and operations
- **Multi-node Support**: Deploy applications across multiple server nodes

## Installation

```bash
composer require tourze/server-application-bundle
```

## Configuration

No additional configuration is required. The bundle auto-configures itself using Symfony's autowiring.

## Usage

### Core Entities

#### AppTemplate
Defines reusable application templates with configuration, health checks, and execution steps.

```php
use ServerApplicationBundle\Entity\AppTemplate;

$template = new AppTemplate();
$template->setName('Web Server');
$template->setDescription('Nginx web server template');
$template->setImage('nginx:latest');
```

#### AppInstance
Represents an actual instance of an application deployed from a template.

```php
use ServerApplicationBundle\Entity\AppInstance;

$instance = new AppInstance();
$instance->setTemplate($template);
$instance->setName('production-web-server');
$instance->setNode($serverNode);
```

#### AppLifecycleLog
Tracks all lifecycle events and operations performed on application instances.

### Services

- **AppTemplateService**: Manage application templates
- **AppInstanceService**: Handle application instance operations
- **AppLifecycleLogService**: Log and query lifecycle events
- **AppPortConfigurationService**: Manage port configurations
- **AppPortMappingService**: Handle port mappings between host and container
- **AppExecutionStepService**: Manage custom execution steps

### Admin Interface

The bundle integrates with EasyAdmin to provide a comprehensive admin interface:

```php
// Access the admin interface at /admin
// Manage templates, instances, logs, and configurations
```

### Command Line Interface

```bash
# List all application templates
bin/console app:template:list

# Deploy an application instance
bin/console app:instance:deploy <template-id> <node-id>

# Check application health
bin/console app:instance:health-check <instance-id>
```

## Advanced Usage

### Custom Execution Steps

Create custom execution steps for complex deployment scenarios:

```php
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Enum\ExecutionStepType;

$step = new AppExecutionStep();
$step->setTemplate($template);
$step->setSequence(1);
$step->setName('Install Dependencies');
$step->setType(ExecutionStepType::COMMAND);
$step->setContent('apt-get update && apt-get install -y nginx');
$step->setWorkingDirectory('/tmp');
$step->setUseSudo(true);
$step->setTimeout(300);
$step->setRetryCount(3);
```

### Health Check Configuration

Configure health checks for your applications:

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

### Environment Variables

Manage environment variables for your applications:

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

### Lifecycle Event Handling

Track and respond to lifecycle events:

```php
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;

$log = new AppLifecycleLog();
$log->setInstance($instance);
$log->setAction(LifecycleActionType::INSTALL);
$log->setStatus(LogStatus::SUCCESS);
$log->setMessage('Application installed successfully');
$log->setExecutionTime(45.2);
```

## Architecture

The bundle follows a service-oriented architecture with:

- **Entities**: Domain models for templates, instances, logs, etc.
- **Repositories**: Data access layer with custom query methods
- **Services**: Business logic layer for operations
- **Controllers**: Admin interface controllers
- **Enums**: Type-safe enumerations for statuses and types

## Dependencies

- Symfony 6.4+
- PHP 8.1+
- Doctrine ORM 3.0+
- EasyAdmin Bundle 4+
- tourze/server-node-bundle
- tourze/doctrine-timestamp-bundle
- tourze/doctrine-ip-bundle
- tourze/doctrine-track-bundle
- tourze/doctrine-user-bundle

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/server-application-bundle/tests
```

## License

MIT License. See LICENSE file for details.