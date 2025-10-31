<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Enum\AppStatus;
use ServerApplicationBundle\Repository\AppInstanceRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 应用实例
 *
 * @implements AdminArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AppInstanceRepository::class)]
#[ORM\Table(name: 'ims_server_app_instance', options: ['comment' => '应用实例'])]
class AppInstance implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '唯一标识符'])]
    private ?int $id = null;

    #[ORM\Column(name: 'template_id', type: Types::INTEGER, options: ['comment' => '应用模板ID'])]
    #[IndexColumn]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private int $templateId;

    /**
     * 关联的应用模板
     */
    #[ORM\ManyToOne(targetEntity: AppTemplate::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'template_id', referencedColumnName: 'id', nullable: false)]
    private AppTemplate $template;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '应用时的模板版本号'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $templateVersion;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '部署的服务器节点ID'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $nodeId;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '实例名称'])]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $name;

    #[ORM\Column(type: Types::STRING, enumType: AppStatus::class, options: ['comment' => '状态(INSTALLING/RUNNING/FAILED/UNINSTALLING/STOPPED)'])]
    #[IndexColumn]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [AppStatus::class, 'cases'])]
    private AppStatus $status;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '环境变量'])]
    #[TrackColumn]
    #[Assert\Type(type: 'array')]
    private ?array $environmentVariables = [];

    /**
     * 端口映射
     *
     * @var Collection<int, AppPortMapping>
     */
    #[ORM\OneToMany(targetEntity: AppPortMapping::class, mappedBy: 'instance', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $portMappings;

    /**
     * 生命周期日志
     *
     * @var Collection<int, AppLifecycleLog>
     */
    #[ORM\OneToMany(targetEntity: AppLifecycleLog::class, mappedBy: 'instance', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(value: ['createTime' => 'DESC'])]
    private Collection $lifecycleLogs;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否健康', 'default' => false])]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    private bool $healthy = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '上次健康检测时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $lastHealthCheck = null;

    public function __construct()
    {
        $this->portMappings = new ArrayCollection();
        $this->lifecycleLogs = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
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
            'template' => $this->template->getName(),
            'templateVersion' => $this->templateVersion,
            'nodeId' => $this->nodeId,
            'name' => $this->name,
            'status' => $this->status->value,
            'environmentVariables' => $this->environmentVariables,
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
     *
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
            'template' => [
                'id' => $this->template->getId(),
                'name' => $this->template->getName(),
                'version' => $this->templateVersion,
            ],
            'nodeId' => $this->nodeId,
            'name' => $this->name,
            'status' => $this->status->value,
            'environmentVariables' => $this->environmentVariables,
            'healthy' => $this->healthy,
            'lastHealthCheck' => $this->lastHealthCheck?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * 检索API数组
     *
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

    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    public function setTemplateId(int $templateId): void
    {
        $this->templateId = $templateId;
    }

    public function getTemplate(): AppTemplate
    {
        return $this->template;
    }

    public function setTemplate(AppTemplate $template): void
    {
        $this->template = $template;
        // 同步更新外键ID
        $templateId = $template->getId();
        if (null !== $templateId) {
            $this->templateId = $templateId;
        }
    }

    public function getTemplateVersion(): string
    {
        return $this->templateVersion;
    }

    public function setTemplateVersion(string $templateVersion): void
    {
        $this->templateVersion = $templateVersion;
    }

    public function getNodeId(): string
    {
        return $this->nodeId;
    }

    public function setNodeId(string $nodeId): void
    {
        $this->nodeId = $nodeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getStatus(): AppStatus
    {
        return $this->status;
    }

    public function setStatus(AppStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getEnvironmentVariables(): ?array
    {
        return $this->environmentVariables;
    }

    /**
     * @param array<string, mixed>|string|null $environmentVariables
     */
    public function setEnvironmentVariables($environmentVariables): void
    {
        // 处理来自 CodeEditorField 的 JSON 字符串
        if (is_string($environmentVariables)) {
            $this->environmentVariables = $this->parseJsonEnvironmentVariables($environmentVariables);
        } else {
            $this->environmentVariables = $environmentVariables;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseJsonEnvironmentVariables(string $json): ?array
    {
        if ('' === trim($json)) {
            return [];
        }

        try {
            $decodedVars = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            // 确保 json_decode 返回的是数组或null,否则设为空数组
            if (is_array($decodedVars)) {
                /** @var array<string, mixed> $validVars */
                $validVars = $decodedVars;

                return $validVars;
            }
            if (null === $decodedVars) {
                return null;
            }

            return [];
        } catch (\JsonException $e) {
            return [];
        }
    }

    /**
     * @return Collection<int, AppPortMapping>
     */
    public function getPortMappings(): Collection
    {
        return $this->portMappings;
    }

    public function addPortMapping(AppPortMapping $portMapping): void
    {
        if (!$this->portMappings->contains($portMapping)) {
            $this->portMappings->add($portMapping);
            $portMapping->setInstance($this);
        }
    }

    public function removePortMapping(AppPortMapping $portMapping): void
    {
        if ($this->portMappings->removeElement($portMapping)) {
            // set the owning side to null (unless already changed)
            if ($portMapping->getInstance() === $this) {
                $portMapping->setInstance(null);
            }
        }
    }

    /**
     * @return Collection<int, AppLifecycleLog>
     */
    public function getLifecycleLogs(): Collection
    {
        return $this->lifecycleLogs;
    }

    public function addLifecycleLog(AppLifecycleLog $lifecycleLog): void
    {
        if (!$this->lifecycleLogs->contains($lifecycleLog)) {
            $this->lifecycleLogs->add($lifecycleLog);
            $lifecycleLog->setInstance($this);
        }
    }

    public function removeLifecycleLog(AppLifecycleLog $lifecycleLog): void
    {
        if ($this->lifecycleLogs->removeElement($lifecycleLog)) {
            // set the owning side to null (unless already changed)
            if ($lifecycleLog->getInstance() === $this) {
                $lifecycleLog->setInstance(null);
            }
        }
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
