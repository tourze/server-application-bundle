# 服务器应用管理系统 (Server Application Bundle) 重构计划

## 1. 项目概述

Server Application Bundle 旨在提供一个统一的服务器应用模板与实例管理系统，实现应用的统一部署、管理和监控。重构后，该系统将具备以下核心功能：

- 应用模板管理：创建和维护可复用的应用模板
- 应用实例部署：基于模板在特定节点上部署应用实例
- 端口管理：管理应用使用的各种端口(TCP/UDP)及其健康检测策略
- 生命周期管理：记录应用从安装、运行到卸载的全生命周期
- 健康检测：监控应用实例的在线状态和健康状况

## 2. 核心实体设计

### 2.1 AppTemplate (应用模板)

应用模板是一个可复用的配置集合，代表了一种可部署的应用类型。

```php
class AppTemplate
{
    // 基本信息
    private string $id;             // 唯一标识符
    private string $name;           // 模板名称
    private string $description;    // 模板描述
    private array $tags;            // 标签列表
    private bool $enabled;          // 是否启用
    private string $version;        // 模板版本号（如1.0.0）
    private bool $isLatest;         // 是否为最新版本

    // 执行步骤（按顺序）
    private Collection $installSteps;     // 安装步骤列表（关联AppExecutionStep）
    private Collection $uninstallSteps;   // 卸载步骤列表（关联AppExecutionStep）

    // 端口配置
    private Collection $portConfigurations; // 端口配置列表（关联AppPortConfiguration）

    // 环境变量默认值
    private array $environmentVariables;    // 环境变量默认值

    // 审计信息
    private string $createdBy;
    private string $updatedBy;
    private \DateTimeInterface $createTime;
    private \DateTimeInterface $updateTime;
}
```

### 2.2 AppExecutionStep (应用执行步骤)

应用执行步骤定义了安装或卸载过程中的具体步骤，每个步骤可以是命令或脚本，并且有明确的执行顺序。

```php
class AppExecutionStep
{
    // 基本信息
    private string $id;               // 唯一标识符
    private AppTemplate $template;    // 所属应用模板
    private int $sequence;            // 执行顺序
    private string $name;             // 步骤名称
    private string $description;      // 步骤描述
    private string $type;             // 步骤类型(COMMAND/SCRIPT)

    // 执行内容
    private string $content;          // 命令内容或脚本内容
    private ?string $workingDirectory; // 工作目录
    private ?bool $useSudo;           // 是否使用sudo执行
    private ?int $timeout;            // 超时时间(秒)

    // 参数替换机制
    private array $parameters;        // 参数定义列表，例如 {"PORT": "应用端口号", "CONFIG_PATH": "配置文件路径"}
    private string $parameterPattern; // 参数替换模式，默认为 "{{PARAM_NAME}}"

    // 执行控制
    private bool $stopOnError;        // 失败时是否停止后续步骤
    private int $retryCount;          // 失败重试次数
    private int $retryInterval;       // 重试间隔(秒)

    // 审计信息
    private string $createdBy;
    private string $updatedBy;
    private \DateTimeInterface $createTime;
    private \DateTimeInterface $updateTime;
}
```

### 2.3 AppPortConfiguration (应用端口配置)

每个应用模板可能会使用多个端口，每个端口都有特定的协议和健康检测策略。

```php
class AppPortConfiguration
{
    // 基本信息
    private string $id;             // 唯一标识符
    private AppTemplate $template;  // 所属应用模板
    private int $port;              // 端口号
    private string $protocol;       // 协议(TCP/UDP)
    private string $description;    // 描述

    // 健康检测
    private string $healthCheckType;        // 健康检测类型(TCP_CONNECT/UDP_PORT_CHECK/COMMAND)
    private string $healthCheckConfig;      // 健康检测配置(JSON格式)
    private int $healthCheckInterval;       // 健康检测间隔(秒)
    private int $healthCheckTimeout;        // 健康检测超时(秒)
    private int $healthCheckRetries;        // 健康检测重试次数

    // 审计信息
    private string $createdBy;
    private string $updatedBy;
    private \DateTimeInterface $createTime;
    private \DateTimeInterface $updateTime;
}
```

### 2.4 AppInstance (应用实例)

应用实例是应用模板在特定节点上的具体部署。

```php
class AppInstance
{
    // 基本信息
    private string $id;                 // 唯一标识符
    private AppTemplate $template;      // 关联的应用模板
    private string $templateVersion;    // 应用时的模板版本号
    private Node $node;                 // 部署的服务器节点
    private string $name;               // 实例名称
    private string $status;             // 状态(INSTALLING/RUNNING/FAILED/UNINSTALLING/STOPPED)
    private array $environmentVariables; // 环境变量

    // 端口映射
    private Collection $portMappings;   // 端口映射(关联AppPortMapping)

    // 生命周期日志
    private Collection $lifecycleLogs;  // 生命周期日志(关联AppLifecycleLog)

    // 健康检测结果
    private bool $healthy;              // 是否健康
    private \DateTimeInterface $lastHealthCheck; // 上次健康检测时间

    // 审计信息
    private string $createdBy;
    private string $updatedBy;
    private \DateTimeInterface $createTime;
    private \DateTimeInterface $updateTime;
}
```

### 2.5 AppPortMapping (应用端口映射)

实例化应用模板后，模板中定义的端口会映射到实际的端口上。

```php
class AppPortMapping
{
    // 基本信息
    private string $id;                         // 唯一标识符
    private AppInstance $instance;              // 所属应用实例
    private AppPortConfiguration $configuration; // 关联的端口配置
    private int $actualPort;                    // 实际使用的端口

    // 健康状态
    private bool $healthy;                      // 是否健康
    private \DateTimeInterface $lastHealthCheck; // 上次健康检测时间

    // 审计信息
    private string $createdBy;
    private string $updatedBy;
    private \DateTimeInterface $createTime;
    private \DateTimeInterface $updateTime;
}
```

### 2.6 AppLifecycleLog (应用生命周期日志)

记录应用实例的完整生命周期，包括安装、健康检测、卸载等操作的日志。

```php
class AppLifecycleLog
{
    // 基本信息
    private string $id;                 // 唯一标识符
    private AppInstance $instance;      // 所属应用实例
    private AppExecutionStep $executionStep; // 关联的执行步骤
    private string $action;             // 操作类型(INSTALL/HEALTH_CHECK/UNINSTALL等)
    private string $status;             // 状态(SUCCESS/FAILED)
    private string $message;            // 详细消息

    // 执行结果
    private ?string $commandOutput;     // 命令或脚本输出
    private ?int $exitCode;             // 退出码
    private ?float $executionTime;      // 执行时间(秒)

    // 审计信息
    private string $createdBy;
    private \DateTimeInterface $createTime;
}
```

## 3. 核心功能流程

### 3.1 应用模板管理

1. 创建应用模板
   - 设置基本信息(名称、描述、标签等)
   - 按顺序添加安装/卸载执行步骤（命令和脚本可混合设置）
   - 配置端口和对应的健康检测策略
   - 设置环境变量默认值

2. 执行步骤管理
   - 支持命令和脚本按特定顺序混合执行
   - 例如：先执行准备命令，再执行配置脚本，最后执行启动命令
   - 允许设置步骤间依赖关系和失败处理策略
   - 支持步骤参数化，便于在不同环境中复用

3. 模板版本管理
   - 每个模板支持多个版本（使用语义化版本号，如1.0.0）
   - 每个版本保持独立，修改模板时创建新版本
   - 只有一个版本标记为最新版（isLatest=true）
   - 应用实例记录创建时使用的模板版本号
   - 版本之间没有升级路径，每个版本独立存在

### 3.2 应用实例部署

1. 基于模板创建实例
   - 选择应用模板和目标节点
   - 配置环境变量和端口映射
   - 开始部署流程

2. 部署流程
   - 执行前置检查(端口可用性、依赖检查等)
   - 按顺序依次执行模板中定义的安装步骤（命令和脚本）
   - 如果步骤执行失败，根据配置决定是否继续或回滚
   - 记录每个步骤的执行过程和结果
   - 部署完成后开始健康检测

### 3.3 应用生命周期管理

1. 应用健康检测
   - 根据配置的健康检测策略定期检测
   - 记录健康状态和检测日志
   - 触发异常检测和通知机制

2. 应用卸载
   - 执行卸载命令和脚本
   - 清理资源和配置
   - 记录卸载过程和结果

### 3.4 端口管理与监控

1. 端口健康检测策略
   - TCP连接检测（TCP_CONNECT）
     - 尝试建立TCP连接到指定端口
     - 如果连接成功建立，则服务正常
     - 配置参数：连接超时时间
   - UDP端口检测（UDP_PORT_CHECK）
     - 检查UDP端口是否在监听状态（通过系统命令如netstat/ss）
     - 对于支持请求-响应的UDP服务（如DNS），可选择性发送探测包
     - 配置参数：检查命令、探测包内容（可选）、服务类型（可选）
   - 命令检测（COMMAND）
     - 执行自定义命令验证服务状态
     - 基于退出码或输出内容判断服务健康状态
     - 配置参数：命令内容、成功退出码、成功输出内容（正则表达式）

2. 端口可用性管理
   - 管理节点上使用的端口
   - 防止端口冲突
   - 支持动态端口分配

## 4. 系统集成

### 4.1 与服务器节点管理集成

- 集成`server-node-bundle`，获取可用节点信息
- 监控节点状态，确保应用部署在健康的节点上

### 4.2 与命令执行系统集成

- 使用`server-command-bundle`的服务层能力执行远程命令
- 在运行时创建RemoteCommand实体并执行，而非直接数据库关联
- 获取命令执行日志和结果并记录到生命周期日志

### 4.3 与脚本执行系统集成

- 使用`server-shell-bundle`的服务层能力执行shell脚本
- 在运行时创建ShellScript实体并执行，而非直接数据库关联
- 获取脚本执行日志和结果并记录到生命周期日志

## 5. 实现计划

### 5.1 实体设计和数据库迁移

1. 创建新的实体类
   - AppTemplate、AppExecutionStep、AppPortConfiguration
   - AppInstance、AppPortMapping
   - AppLifecycleLog

2. 执行步骤设计
   - 设计灵活的执行步骤实体，支持命令和脚本的有序执行
   - 支持失败重试和错误处理策略
   - 实现步骤参数化和条件执行机制

3. 数据迁移
   - 从原有Application实体迁移数据到新结构
   - 保留历史数据和兼容性

### 5.2 服务层实现

1. 模板管理服务
   - 创建、更新、删除应用模板
   - 管理执行步骤和端口配置

2. 实例管理服务
   - 部署、启动、停止、卸载应用实例
   - 监控实例状态

3. 执行服务
   - 基于AppExecutionStep创建RemoteCommand或ShellScript并调用相应bundle执行
   - 实现命令和脚本参数变量替换（如节点信息、环境变量等）
     - 支持模板中定义的参数（如{{PORT}}、{{CONFIG_PATH}}等）
     - 参数来源：环境变量、实例配置、节点信息、动态计算值
     - 实现参数替换服务，在执行前将模板内容中的参数替换为实际值
   - 处理执行结果和错误，实现失败重试和回滚机制
   - 记录详细的执行日志

4. 健康检测服务
   - 实现不同的健康检测策略
   - 定期执行健康检测

### 5.3 API和界面

1. RESTful API
   - 提供完整的API访问应用管理功能
   - 支持自动化集成

2. 管理界面
   - 应用模板管理界面
   - 应用实例部署和监控界面
   - 健康状态仪表盘

## 6. 开发排期

### 阶段一：核心实体和基础功能 (2周)

- 实现核心实体设计
- 创建数据库迁移脚本
- 实现基础的CRUD服务

### 阶段二：应用生命周期管理 (2周)

- 实现应用部署流程
- 开发健康检测机制
- 完成生命周期日志记录

### 阶段三：集成和高级功能 (2周)

- 与其他bundle集成
- 端口管理和监控功能
- API和管理界面开发

### 阶段四：测试和文档 (1周)

- 单元测试和集成测试
- 编写用户和开发文档
- 性能测试和优化

## 7. 注意事项和风险

1. 数据迁移风险
   - 需确保数据迁移过程不丢失现有数据
   - 考虑兼容性和向后兼容策略

2. 性能考虑
   - 频繁的健康检测可能对系统和网络造成负担
   - 需实现合理的检测频率和缓存策略

3. 安全注意事项
   - 确保敏感配置信息安全存储
   - 实现访问控制和权限管理

## 8. 未来展望

1. 应用自动伸缩
   - 基于负载自动扩展应用实例

2. 日志和监控集成
   - 集成更完善的日志收集和分析系统
   - 提供更详细的性能监控

3. 容器支持
   - 支持容器化应用部署和管理
   - Docker和Kubernetes集成
