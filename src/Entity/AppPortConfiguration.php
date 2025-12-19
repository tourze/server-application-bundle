<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;
use ServerApplicationBundle\Repository\AppPortConfigurationRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 应用端口配置
 *
 * @implements AdminArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AppPortConfigurationRepository::class)]
#[ORM\Table(name: 'ims_server_app_port_configuration', options: ['comment' => '应用端口配置'])]
class AppPortConfiguration implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '唯一标识符'])]
    private ?int $id = null;

    /**
     * 所属应用模板
     */
    #[ORM\ManyToOne(targetEntity: AppTemplate::class, cascade: ['persist'], inversedBy: 'portConfigurations')]
    #[ORM\JoinColumn(name: 'template_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?AppTemplate $template = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '端口号'])]
    #[IndexColumn]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 65535)]
    private int $port;

    #[ORM\Column(type: Types::STRING, enumType: ProtocolType::class, options: ['comment' => '协议(TCP/UDP)'])]
    #[IndexColumn]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [ProtocolType::class, 'cases'])]
    private ProtocolType $protocol;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '描述'])]
    #[TrackColumn]
    #[Assert\Length(max: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, enumType: HealthCheckType::class, options: ['comment' => '健康检测类型(TCP_CONNECT/UDP_PORT_CHECK/COMMAND)'])]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [HealthCheckType::class, 'cases'])]
    private HealthCheckType $healthCheckType;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '健康检测配置(JSON格式)'])]
    #[TrackColumn]
    #[Assert\Type(type: 'array')]
    private ?array $healthCheckConfig = [];

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '健康检测间隔(秒)', 'default' => 60])]
    #[TrackColumn]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $healthCheckInterval = 60;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '健康检测超时(秒)', 'default' => 5])]
    #[TrackColumn]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $healthCheckTimeout = 5;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '健康检测重试次数', 'default' => 3])]
    #[TrackColumn]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private int $healthCheckRetries = 3;

    public function __toString(): string
    {
        return $this->protocol->value . '/' . $this->port . (null !== $this->description ? ' (' . $this->description . ')' : '');
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
            'template' => $this->template?->getId(),
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

    public function getTemplate(): ?AppTemplate
    {
        return $this->template;
    }

    public function setTemplate(?AppTemplate $template): void
    {
        $this->template = $template;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function getProtocol(): ProtocolType
    {
        return $this->protocol;
    }

    public function setProtocol(ProtocolType $protocol): void
    {
        $this->protocol = $protocol;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getHealthCheckType(): HealthCheckType
    {
        return $this->healthCheckType;
    }

    public function setHealthCheckType(HealthCheckType $healthCheckType): void
    {
        $this->healthCheckType = $healthCheckType;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getHealthCheckConfig(): ?array
    {
        return $this->healthCheckConfig;
    }

    /**
     * @param array<string, mixed>|string|null $healthCheckConfig
     */
    public function setHealthCheckConfig($healthCheckConfig): void
    {
        // 处理来自 CodeEditorField 的 JSON 字符串
        if (is_string($healthCheckConfig)) {
            if ('' === trim($healthCheckConfig)) {
                $this->healthCheckConfig = [];
            } else {
                try {
                    $decodedConfig = json_decode($healthCheckConfig, true, 512, JSON_THROW_ON_ERROR);
                    // 确保 json_decode 返回的是数组,否则设为空数组
                    /** @var array<string, mixed> $validConfig */
                    $validConfig = is_array($decodedConfig) ? $decodedConfig : [];
                    $this->healthCheckConfig = $validConfig;
                } catch (\JsonException $e) {
                    $this->healthCheckConfig = [];
                }
            }
        } else {
            $this->healthCheckConfig = $healthCheckConfig;
        }
    }

    public function getHealthCheckInterval(): int
    {
        return $this->healthCheckInterval;
    }

    public function setHealthCheckInterval(int $healthCheckInterval): void
    {
        $this->healthCheckInterval = $healthCheckInterval;
    }

    public function getHealthCheckTimeout(): int
    {
        return $this->healthCheckTimeout;
    }

    public function setHealthCheckTimeout(int $healthCheckTimeout): void
    {
        $this->healthCheckTimeout = $healthCheckTimeout;
    }

    public function getHealthCheckRetries(): int
    {
        return $this->healthCheckRetries;
    }

    public function setHealthCheckRetries(int $healthCheckRetries): void
    {
        $this->healthCheckRetries = $healthCheckRetries;
    }
}
