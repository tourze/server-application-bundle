<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;
use ServerApplicationBundle\Repository\AppPortConfigurationRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 应用端口配置
 */
#[ORM\Entity(repositoryClass: AppPortConfigurationRepository::class)]
#[ORM\Table(name: 'ims_server_app_port_configuration', options: ['comment' => '应用端口配置'])]
#[ORM\Index(name: 'ims_server_app_port_configuration_idx_template', columns: ['template_id'])]
#[ORM\Index(name: 'ims_server_app_port_configuration_idx_port', columns: ['port'])]
#[ORM\Index(name: 'ims_server_app_port_configuration_idx_protocol', columns: ['protocol'])]
class AppPortConfiguration implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '唯一标识符'])]
    private ?int $id = null;

    /**
     * 所属应用模板
     */
    #[ORM\ManyToOne(targetEntity: AppTemplate::class, inversedBy: 'portConfigurations')]
    #[ORM\JoinColumn(name: 'template_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private AppTemplate $template;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '端口号'])]
    #[TrackColumn]
    private int $port;

    #[ORM\Column(type: Types::STRING, enumType: ProtocolType::class, options: ['comment' => '协议(TCP/UDP)'])]
    #[TrackColumn]
    private ProtocolType $protocol;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '描述'])]
    #[TrackColumn]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, enumType: HealthCheckType::class, options: ['comment' => '健康检测类型(TCP_CONNECT/UDP_PORT_CHECK/COMMAND)'])]
    #[TrackColumn]
    private HealthCheckType $healthCheckType;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '健康检测配置(JSON格式)'])]
    #[TrackColumn]
    private ?array $healthCheckConfig = [];

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '健康检测间隔(秒)', 'default' => 60])]
    #[TrackColumn]
    private int $healthCheckInterval = 60;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '健康检测超时(秒)', 'default' => 5])]
    #[TrackColumn]
    private int $healthCheckTimeout = 5;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '健康检测重试次数', 'default' => 3])]
    #[TrackColumn]
    private int $healthCheckRetries = 3;

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '创建IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '更新IP'])]
    private ?string $updatedFromIp = null;

    public function __toString(): string
    {
        return $this->protocol->value . '/' . $this->port . (null !== $this->description ? ' (' . $this->description . ')' : '');
    }

    /**
     * 转为管理后台数组
     */
    public function toAdminArray(): array
    {
        return [
            'id' => $this->id,
            'template' => $this->template->getId(),
            'port' => $this->port,
            'protocol' => $this->protocol->value,
            'description' => $this->description,
            'healthCheckType' => $this->healthCheckType->value,
            'healthCheckConfig' => $this->healthCheckConfig,
            'healthCheckInterval' => $this->healthCheckInterval,
            'healthCheckTimeout' => $this->healthCheckTimeout,
            'healthCheckRetries' => $this->healthCheckRetries,
            'createTime' => $this->createTime?->format('Y-m-d H:i:s'),
            'updateTime' => $this->updateTime?->format('Y-m-d H:i:s'),
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ];
    }

    /**
     * 检索管理后台数组
     */
    public function retrieveAdminArray(): array
    {
        return $this->toAdminArray();
    }

    /**
     * 转为API数组
     */
    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'port' => $this->port,
            'protocol' => $this->protocol->value,
            'description' => $this->description,
            'healthCheckType' => $this->healthCheckType->value,
            'healthCheckConfig' => $this->healthCheckConfig,
            'healthCheckInterval' => $this->healthCheckInterval,
            'healthCheckTimeout' => $this->healthCheckTimeout,
            'healthCheckRetries' => $this->healthCheckRetries,
        ];
    }

    /**
     * 检索API数组
     */
    public function retrieveApiArray(): array
    {
        return $this->toApiArray();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplate(): AppTemplate
    {
        return $this->template;
    }

    public function setTemplate(?AppTemplate $template): self
    {
        $this->template = $template;
        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;
        return $this;
    }

    public function getProtocol(): ProtocolType
    {
        return $this->protocol;
    }

    public function setProtocol(ProtocolType $protocol): self
    {
        $this->protocol = $protocol;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getHealthCheckType(): HealthCheckType
    {
        return $this->healthCheckType;
    }

    public function setHealthCheckType(HealthCheckType $healthCheckType): self
    {
        $this->healthCheckType = $healthCheckType;
        return $this;
    }

    public function getHealthCheckConfig(): ?array
    {
        return $this->healthCheckConfig;
    }

    public function setHealthCheckConfig(?array $healthCheckConfig): self
    {
        $this->healthCheckConfig = $healthCheckConfig;
        return $this;
    }

    public function getHealthCheckInterval(): int
    {
        return $this->healthCheckInterval;
    }

    public function setHealthCheckInterval(int $healthCheckInterval): self
    {
        $this->healthCheckInterval = $healthCheckInterval;
        return $this;
    }

    public function getHealthCheckTimeout(): int
    {
        return $this->healthCheckTimeout;
    }

    public function setHealthCheckTimeout(int $healthCheckTimeout): self
    {
        $this->healthCheckTimeout = $healthCheckTimeout;
        return $this;
    }

    public function getHealthCheckRetries(): int
    {
        return $this->healthCheckRetries;
    }

    public function setHealthCheckRetries(int $healthCheckRetries): self
    {
        $this->healthCheckRetries = $healthCheckRetries;
        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;
        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;
        return $this;
    }}
