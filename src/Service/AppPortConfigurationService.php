<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Repository\AppPortConfigurationRepository;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * 应用端口配置服务
 */
#[Autoconfigure(public: true)]
class AppPortConfigurationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AppPortConfigurationRepository $appPortConfigurationRepository,
    ) {
    }

    /**
     * 获取端口配置列表
     *
     * @return array<AppPortConfiguration>
     */
    public function findAll(): array
    {
        return $this->appPortConfigurationRepository->findAll();
    }

    /**
     * 根据ID获取端口配置
     */
    public function find(string $id): ?AppPortConfiguration
    {
        return $this->appPortConfigurationRepository->find($id);
    }

    /**
     * 保存端口配置
     */
    public function save(AppPortConfiguration $portConfiguration, bool $flush = true): void
    {
        $this->entityManager->persist($portConfiguration);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 删除端口配置
     */
    public function remove(AppPortConfiguration $portConfiguration, bool $flush = true): void
    {
        $this->entityManager->remove($portConfiguration);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 获取模板的所有端口配置
     *
     * @return array<AppPortConfiguration>
     */
    public function findByTemplate(AppTemplate $template): array
    {
        return $this->appPortConfigurationRepository->findBy(['template' => $template]);
    }

    /**
     * 检查端口配置健康状态
     */
    public function checkHealth(AppPortConfiguration $portConfiguration, int $actualPort, string $host = 'localhost'): bool
    {
        // TODO: 根据不同的健康检测类型实现检测逻辑
        switch ($portConfiguration->getHealthCheckType()) {
            case HealthCheckType::TCP_CONNECT:
                // 尝试建立TCP连接
                return $this->checkTcpConnection($host, $actualPort, $portConfiguration->getHealthCheckTimeout());
            case HealthCheckType::UDP_PORT_CHECK:
                // 检查UDP端口
                return $this->checkUdpPort($host, $actualPort);
            case HealthCheckType::COMMAND:
                // 执行命令检测
                $config = $portConfiguration->getHealthCheckConfig();
                if (null === $config) {
                    return false;
                }

                return $this->executeHealthCheckCommand($config, $host, $actualPort);
            default:
                return false;
        }
    }

    /**
     * 检查TCP连接
     */
    private function checkTcpConnection(string $host, int $port, int $timeout): bool
    {
        $sock = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (false === $sock) {
            return false;
        }
        fclose($sock);

        return true;
    }

    /**
     * 检查UDP端口
     */
    private function checkUdpPort(string $host, int $port): bool
    {
        // UDP端口检测逻辑
        // 简单实现，实际可能需要更复杂的逻辑
        $sock = @fsockopen('udp://' . $host, $port, $errno, $errstr, 1);
        if (false === $sock) {
            return false;
        }
        fclose($sock);

        return true;
    }

    /**
     * 执行健康检测命令
     *
     * @param array<string, mixed> $config
     */
    private function executeHealthCheckCommand(array $config, string $host, int $port): bool
    {
        // 获取并验证命令配置
        $commandConfig = $config['command'] ?? '';

        // 确保命令是字符串类型
        if (!is_string($commandConfig)) {
            return false;
        }

        // 替换命令中的变量
        $command = str_replace('{HOST}', $host, $commandConfig);
        $command = str_replace('{PORT}', (string) $port, $command);

        // 执行命令
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        // 检查退出码
        if (isset($config['successExitCode'])) {
            $expected = $config['successExitCode'];
            if (is_int($expected)) {
                return $returnVar === $expected;
            }
            if (is_string($expected) && ctype_digit($expected)) {
                return $returnVar === (int) $expected;
            }

            return false;
        }

        // 检查输出内容
        if (isset($config['successOutput']) && count($output) > 0) {
            $successOutput = $config['successOutput'];
            if (!is_string($successOutput) || $successOutput === '') {
                return false;
            }

            return 1 === preg_match('/' . $successOutput . '/', implode("\n", $output));
        }

        // 默认以退出码0为成功
        return 0 === $returnVar;
    }
}
