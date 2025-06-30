<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Enum\AppStatus;
use ServerApplicationBundle\Repository\AppInstanceRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 应用实例
 */
#[ORM\Entity(repositoryClass: AppInstanceRepository::class)]
#[ORM\Table(name: 'ims_server_app_instance', options: ['comment' => '应用实例'])]
#[ORM\Index(name: 'ims_server_app_instance_idx_template', columns: ['template_id'])]
#[ORM\Index(name: 'ims_server_app_instance_idx_node', columns: ['node_id'])]
#[ORM\Index(name: 'ims_server_app_instance_idx_status', columns: ['status'])]
class AppInstance implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '唯一标识符'])]
    private ?int $id = null;

    /**
     * 关联的应用模板
     */
    #[ORM\ManyToOne(targetEntity: AppTemplate::class)]
    #[ORM\JoinColumn(name: 'template_id', referencedColumnName: 'id', nullable: false)]
    private AppTemplate $template;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '应用时的模板版本号'])]
    private string $templateVersion;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '部署的服务器节点ID'])]
    private string $nodeId;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '实例名称'])]
    #[TrackColumn]
    private string $name;

    #[ORM\Column(type: Types::STRING, enumType: AppStatus::class, options: ['comment' => '状态(INSTALLING/RUNNING/FAILED/UNINSTALLING/STOPPED)'])]
    #[TrackColumn]
    private AppStatus $status;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '环境变量'])]
    #[TrackColumn]
    private ?array $environmentVariables = [];

    /**
     * 端口映射
     */
    #[ORM\OneToMany(targetEntity: AppPortMapping::class, mappedBy: 'instance', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $portMappings;

    /**
     * 生命周期日志
     */
    #[ORM\OneToMany(targetEntity: AppLifecycleLog::class, mappedBy: 'instance', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(value: ['createTime' => 'DESC'])]
    private Collection $lifecycleLogs;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否健康', 'default' => false])]
    private bool $healthy = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '上次健康检测时间'])]
    private ?\DateTimeInterface $lastHealthCheck = null;

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '创建IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '更新IP'])]
    private ?string $updatedFromIp = null;

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

    public function setTemplate(AppTemplate $template): self
    {
        $this->template = $template;
        return $this;
    }

    public function getTemplateVersion(): string
    {
        return $this->templateVersion;
    }

    public function setTemplateVersion(string $templateVersion): self
    {
        $this->templateVersion = $templateVersion;
        return $this;
    }

    public function getNodeId(): string
    {
        return $this->nodeId;
    }

    public function setNodeId(string $nodeId): self
    {
        $this->nodeId = $nodeId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getStatus(): AppStatus
    {
        return $this->status;
    }

    public function setStatus(AppStatus $status): void
    {
        $this->status = $status;
    }

    public function getEnvironmentVariables(): ?array
    {
        return $this->environmentVariables;
    }

    public function setEnvironmentVariables(?array $environmentVariables): self
    {
        $this->environmentVariables = $environmentVariables;
        return $this;
    }

    /**
     * @return Collection<int, AppPortMapping>
     */
    public function getPortMappings(): Collection
    {
        return $this->portMappings;
    }

    public function addPortMapping(AppPortMapping $portMapping): self
    {
        if (!$this->portMappings->contains($portMapping)) {
            $this->portMappings->add($portMapping);
            $portMapping->setInstance($this);
        }

        return $this;
    }

    public function removePortMapping(AppPortMapping $portMapping): self
    {
        if ($this->portMappings->removeElement($portMapping)) {
            // set the owning side to null (unless already changed)
            if ($portMapping->getInstance() === $this) {
                $portMapping->setInstance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AppLifecycleLog>
     */
    public function getLifecycleLogs(): Collection
    {
        return $this->lifecycleLogs;
    }

    public function addLifecycleLog(AppLifecycleLog $lifecycleLog): self
    {
        if (!$this->lifecycleLogs->contains($lifecycleLog)) {
            $this->lifecycleLogs->add($lifecycleLog);
            $lifecycleLog->setInstance($this);
        }

        return $this;
    }

    public function removeLifecycleLog(AppLifecycleLog $lifecycleLog): self
    {
        if ($this->lifecycleLogs->removeElement($lifecycleLog)) {
            // set the owning side to null (unless already changed)
            if ($lifecycleLog->getInstance() === $this) {
                $lifecycleLog->setInstance(null);
            }
        }

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
}
