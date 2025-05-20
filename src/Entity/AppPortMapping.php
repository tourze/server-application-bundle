<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Repository\AppPortMappingRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;

/**
 * 应用端口映射
 */
#[ORM\Entity(repositoryClass: AppPortMappingRepository::class)]
#[ORM\Table(name: 'ims_server_app_port_mapping', options: ['comment' => '应用端口映射'])]
#[ORM\Index(name: 'ims_server_app_port_mapping_idx_instance', columns: ['instance_id'])]
#[ORM\Index(name: 'ims_server_app_port_mapping_idx_configuration', columns: ['configuration_id'])]
#[ORM\Index(name: 'ims_server_app_port_mapping_idx_actual_port', columns: ['actual_port'])]
class AppPortMapping implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '唯一标识符'])]
    private ?int $id = null;

    /**
     * 所属应用实例
     */
    #[ORM\ManyToOne(targetEntity: AppInstance::class, inversedBy: 'portMappings')]
    #[ORM\JoinColumn(name: 'instance_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?AppInstance $instance = null;

    /**
     * 关联的端口配置
     */
    #[ORM\ManyToOne(targetEntity: AppPortConfiguration::class)]
    #[ORM\JoinColumn(name: 'configuration_id', referencedColumnName: 'id', nullable: false)]
    private AppPortConfiguration $configuration;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '实际使用的端口'])]
    #[TrackColumn]
    private int $actualPort;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否健康', 'default' => false])]
    private bool $healthy = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '上次健康检测时间'])]
    private ?\DateTimeInterface $lastHealthCheck = null;

    #[CreatedByColumn]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '创建IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '更新IP'])]
    private ?string $updatedFromIp = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    /**
     * 转为字符串
     */
    public function __toString(): string
    {
        return $this->configuration->getProtocol()->value . '/' . $this->actualPort;
    }

    /**
     * 转为管理后台数组
     */
    public function toAdminArray(): array
    {
        return [
            'id' => $this->id,
            'instance' => $this->instance?->getName(),
            'configuration' => $this->configuration->getId(),
            'actualPort' => $this->actualPort,
            'healthy' => $this->healthy,
            'lastHealthCheck' => $this->lastHealthCheck?->format('Y-m-d H:i:s'),
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
            'configPort' => $this->configuration->getPort(),
            'protocol' => $this->configuration->getProtocol()->value,
            'actualPort' => $this->actualPort,
            'healthy' => $this->healthy,
            'lastHealthCheck' => $this->lastHealthCheck?->format('Y-m-d H:i:s'),
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

    public function getInstance(): ?AppInstance
    {
        return $this->instance;
    }

    public function setInstance(?AppInstance $instance): self
    {
        $this->instance = $instance;
        return $this;
    }

    public function getConfiguration(): AppPortConfiguration
    {
        return $this->configuration;
    }

    public function setConfiguration(AppPortConfiguration $configuration): self
    {
        $this->configuration = $configuration;
        return $this;
    }

    public function getActualPort(): int
    {
        return $this->actualPort;
    }

    public function setActualPort(int $actualPort): self
    {
        $this->actualPort = $actualPort;
        return $this;
    }

    public function isHealthy(): bool
    {
        return $this->healthy;
    }

    public function setHealthy(bool $healthy): self
    {
        $this->healthy = $healthy;
        return $this;
    }

    public function getLastHealthCheck(): ?\DateTimeInterface
    {
        return $this->lastHealthCheck;
    }

    public function setLastHealthCheck(?\DateTimeInterface $lastHealthCheck): self
    {
        $this->lastHealthCheck = $lastHealthCheck;
        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;
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
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setCreateTime(?\DateTimeInterface $createTime): void
    {
        $this->createTime = $createTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }
}
