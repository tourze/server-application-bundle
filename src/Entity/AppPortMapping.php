<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Repository\AppPortMappingRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 应用端口映射
 *
 * @implements AdminArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AppPortMappingRepository::class)]
#[ORM\Table(name: 'ims_server_app_port_mapping', options: ['comment' => '应用端口映射'])]
class AppPortMapping implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '唯一标识符'])]
    private ?int $id = null;

    #[ORM\Column(name: 'instance_id', type: Types::INTEGER, nullable: true, options: ['comment' => '应用实例ID'])]
    #[IndexColumn]
    #[Assert\PositiveOrZero]
    private ?int $instanceId = null;

    /**
     * 所属应用实例
     */
    #[ORM\ManyToOne(targetEntity: AppInstance::class, inversedBy: 'portMappings', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'instance_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?AppInstance $instance = null;

    #[ORM\Column(name: 'configuration_id', type: Types::INTEGER, options: ['comment' => '端口配置ID'])]
    #[IndexColumn]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private int $portConfigId;

    /**
     * 关联的端口配置
     */
    #[ORM\ManyToOne(targetEntity: AppPortConfiguration::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'configuration_id', referencedColumnName: 'id', nullable: false)]
    private AppPortConfiguration $configuration;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '实际使用的端口'])]
    #[IndexColumn]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 65535)]
    private int $actualPort;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否健康', 'default' => false])]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    private bool $healthy = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '上次健康检测时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $lastHealthCheck = null;

    /**
     * 转为字符串
     */
    public function __toString(): string
    {
        return $this->configuration->getProtocol()->value . '/' . $this->actualPort;
    }

    /**
     * 转为管理后台数组
     *
     * @return array<string, mixed>
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
    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return $this->toAdminArray();
    }

    /**
     * 转为API数组
     *
     * @return array<string, mixed>
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
    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return $this->toApiArray();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstanceId(): ?int
    {
        return $this->instanceId;
    }

    public function setInstanceId(?int $instanceId): void
    {
        $this->instanceId = $instanceId;
    }

    public function getConfigurationId(): int
    {
        return $this->portConfigId;
    }

    public function setConfigurationId(int $configurationId): void
    {
        $this->portConfigId = $configurationId;
    }

    public function getInstance(): ?AppInstance
    {
        return $this->instance;
    }

    public function setInstance(?AppInstance $instance): void
    {
        $this->instance = $instance;
        // 同步更新外键ID
        if (null !== $instance) {
            $instanceId = $instance->getId();
            if (null !== $instanceId) {
                $this->instanceId = $instanceId;
            }
        } else {
            $this->instanceId = null;
        }
    }

    public function getConfiguration(): AppPortConfiguration
    {
        return $this->configuration;
    }

    public function setConfiguration(AppPortConfiguration $configuration): void
    {
        $this->configuration = $configuration;
        // 同步更新外键ID
        $configurationId = $configuration->getId();
        if (null !== $configurationId) {
            $this->portConfigId = $configurationId;
        }
    }

    public function getActualPort(): int
    {
        return $this->actualPort;
    }

    public function setActualPort(int $actualPort): void
    {
        $this->actualPort = $actualPort;
    }

    public function isHealthy(): bool
    {
        return $this->healthy;
    }

    public function setHealthy(bool $healthy): void
    {
        $this->healthy = $healthy;
    }

    public function getLastHealthCheck(): ?\DateTimeInterface
    {
        return $this->lastHealthCheck;
    }

    public function setLastHealthCheck(?\DateTimeInterface $lastHealthCheck): void
    {
        $this->lastHealthCheck = $lastHealthCheck;
    }
}
