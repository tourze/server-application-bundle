# ServerApplicationBundle 数据填充

本目录包含 ServerApplicationBundle 的数据填充类，用于在开发和测试环境中生成测试数据。

## 数据填充类列表

- **AppTemplateFixtures**: 应用模板数据
- **AppInstanceFixtures**: 应用实例数据
- **AppPortConfigurationFixtures**: 应用端口配置数据
- **AppPortMappingFixtures**: 应用端口映射数据
- **AppLifecycleLogFixtures**: 应用生命周期日志数据
- **AppExecutionStepFixtures**: 应用执行步骤数据

## 数据依赖关系

数据填充类之间有以下依赖关系：

1. AppInstanceFixtures 依赖于 AppTemplateFixtures
2. AppPortConfigurationFixtures 依赖于 AppTemplateFixtures
3. AppPortMappingFixtures 依赖于 AppInstanceFixtures 和 AppPortConfigurationFixtures
4. AppLifecycleLogFixtures 依赖于 AppInstanceFixtures
5. AppExecutionStepFixtures 依赖于 AppTemplateFixtures

## 使用方法

### 加载所有数据

在项目根目录执行以下命令加载所有数据：

```bash
php bin/console doctrine:fixtures:load
```

### 加载特定分组的数据

如果您将 Fixture 分组，可以使用以下命令加载特定分组：

```bash
php bin/console doctrine:fixtures:load --group=server-app
```

### 追加数据（不清空数据库）

如果不想清空现有数据，可以使用 `--append` 选项：

```bash
php bin/console doctrine:fixtures:load --append
```

### 使用 TRUNCATE 语句清除数据

默认使用 DELETE 语句清除数据，可以使用 TRUNCATE 语句提高性能：

```bash
php bin/console doctrine:fixtures:load --purge-with-truncate
```

## 注意事项

1. 数据填充仅用于开发和测试环境，不应在生产环境中使用
2. 执行 `doctrine:fixtures:load` 命令默认会清空相关表中的所有数据
3. 若需要修改数据结构，请先更新实体类，然后再更新相应的数据填充类
4. 所有 Fixture 类都遵循 PSR-4 命名规范 