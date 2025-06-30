<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Repository\AppTemplateRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 应用模板
 */
#[ORM\Entity(repositoryClass: AppTemplateRepository::class)]
#[ORM\Table(name: 'ims_server_app_template', options: ['comment' => '应用模板'])]
#[ORM\Index(name: 'ims_server_app_template_idx_name', columns: ['name'])]
#[ORM\Index(name: 'ims_server_app_template_idx_is_latest', columns: ['is_latest'])]
class AppTemplate implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '唯一标识符'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '模板名称'])]
    #[TrackColumn]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '模板描述'])]
    #[TrackColumn]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签列表'])]
    #[TrackColumn]
    private ?array $tags = [];

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否启用', 'default' => true])]
    #[TrackColumn]
    private bool $enabled = true;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '模板版本号'])]
    #[TrackColumn]
    private string $version;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否为最新版本', 'default' => false])]
    #[TrackColumn]
    private bool $isLatest = false;

    /**
     * 安装步骤列表
     */
    #[ORM\OneToMany(targetEntity: AppExecutionStep::class, mappedBy: 'template', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(value: ['sequence' => 'ASC'])]
    private Collection $installSteps;

    /**
     * 卸载步骤列表
     */
    #[ORM\OneToMany(targetEntity: AppExecutionStep::class, mappedBy: 'template', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(value: ['sequence' => 'ASC'])]
    private Collection $uninstallSteps;

    /**
     * 端口配置列表
     */
    #[ORM\OneToMany(targetEntity: AppPortConfiguration::class, mappedBy: 'template', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $portConfigurations;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '环境变量默认值'])]
    #[TrackColumn]
    private ?array $environmentVariables = [];

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '创建IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '更新IP'])]
    private ?string $updatedFromIp = null;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->installSteps = new ArrayCollection();
        $this->uninstallSteps = new ArrayCollection();
        $this->portConfigurations = new ArrayCollection();
    }

    /**
     * 转为字符串
     */
    public function __toString(): string
    {
        return $this->name . ' (' . $this->version . ')';
    }

    /**
     * 转为管理后台数组
     */
    public function toAdminArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'tags' => $this->tags,
            'enabled' => $this->enabled,
            'version' => $this->version,
            'isLatest' => $this->isLatest,
            'environmentVariables' => $this->environmentVariables,
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
            'name' => $this->name,
            'description' => $this->description,
            'tags' => $this->tags,
            'enabled' => $this->enabled,
            'version' => $this->version,
            'isLatest' => $this->isLatest,
            'environmentVariables' => $this->environmentVariables,
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function isLatest(): bool
    {
        return $this->isLatest;
    }

    public function setIsLatest(bool $isLatest): self
    {
        $this->isLatest = $isLatest;
        return $this;
    }

    /**
     * @return Collection<int, AppExecutionStep>
     */
    public function getInstallSteps(): Collection
    {
        return $this->installSteps;
    }

    public function addInstallStep(AppExecutionStep $installStep): self
    {
        if (!$this->installSteps->contains($installStep)) {
            $this->installSteps->add($installStep);
            $installStep->setTemplate($this);
        }

        return $this;
    }

    public function removeInstallStep(AppExecutionStep $installStep): self
    {
        if ($this->installSteps->removeElement($installStep)) {
            // set the owning side to null (unless already changed)
            if ($installStep->getTemplate() === $this) {
                $installStep->setTemplate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AppExecutionStep>
     */
    public function getUninstallSteps(): Collection
    {
        return $this->uninstallSteps;
    }

    public function addUninstallStep(AppExecutionStep $uninstallStep): self
    {
        if (!$this->uninstallSteps->contains($uninstallStep)) {
            $this->uninstallSteps->add($uninstallStep);
            $uninstallStep->setTemplate($this);
        }

        return $this;
    }

    public function removeUninstallStep(AppExecutionStep $uninstallStep): self
    {
        if ($this->uninstallSteps->removeElement($uninstallStep)) {
            // set the owning side to null (unless already changed)
            if ($uninstallStep->getTemplate() === $this) {
                $uninstallStep->setTemplate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AppPortConfiguration>
     */
    public function getPortConfigurations(): Collection
    {
        return $this->portConfigurations;
    }

    public function addPortConfiguration(AppPortConfiguration $portConfiguration): self
    {
        if (!$this->portConfigurations->contains($portConfiguration)) {
            $this->portConfigurations->add($portConfiguration);
            $portConfiguration->setTemplate($this);
        }

        return $this;
    }

    public function removePortConfiguration(AppPortConfiguration $portConfiguration): self
    {
        if ($this->portConfigurations->removeElement($portConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($portConfiguration->getTemplate() === $this) {
                $portConfiguration->setTemplate(null);
            }
        }

        return $this;
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
