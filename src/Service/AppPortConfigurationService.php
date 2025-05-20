<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Repository\AppPortConfigurationRepository;

/**
 * 应用端口配置服务
 */
class AppPortConfigurationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AppPortConfigurationRepository $appPortConfigurationRepository,
    ) {
    }

    /**
     * 获取端口配置列表
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
            case 'TCP_CONNECT':
                // 尝试建立TCP连接
                return $this->checkTcpConnection($host, $actualPort, $portConfiguration->getHealthCheckTimeout());
            case 'UDP_PORT_CHECK':
                // 检查UDP端口
                return $this->checkUdpPort($host, $actualPort);
            case 'COMMAND':
                // 执行命令检测
                return $this->executeHealthCheckCommand($portConfiguration->getHealthCheckConfig(), $host, $actualPort);
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
        if (!$sock) {
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
        if (!$sock) {
            return false;
        }
        fclose($sock);
        return true;
    }

    /**
     * 执行健康检测命令
     */
    private function executeHealthCheckCommand(array $config, string $host, int $port): bool
    {
        // 替换命令中的变量
        $command = $config['command'] ?? '';
        $command = str_replace('{HOST}', $host, $command);
        $command = str_replace('{PORT}', (string)$port, $command);
        
        // 执行命令
        exec($command, $output, $returnVar);
        
        // 检查退出码
        if (isset($config['successExitCode'])) {
            return $returnVar === (int)$config['successExitCode'];
        }
        
        // 检查输出内容
        if (isset($config['successOutput']) && is_array($output) && count($output) > 0) {
            return preg_match('/' . $config['successOutput'] . '/', implode("\n", $output)) === 1;
        }
        
        // 默认以退出码0为成功
        return $returnVar === 0;
    }
} 