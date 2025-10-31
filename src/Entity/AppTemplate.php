<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Repository\AppTemplateRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 应用模板
 *
 * @implements AdminArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AppTemplateRepository::class)]
#[ORM\Table(name: 'ims_server_app_template', options: ['comment' => '应用模板'])]
class AppTemplate implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '唯一标识符'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '模板名称'])]
    #[IndexColumn]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '模板描述'])]
    #[TrackColumn]
    #[Assert\Length(max: 65535)]
    private ?string $description = null;

    /**
     * @var array<string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签列表'])]
    #[TrackColumn]
    #[Assert\Type(type: 'array')]
    private ?array $tags = [];

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否启用', 'default' => true])]
    #[TrackColumn]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    private bool $enabled = true;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '模板版本号'])]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $version;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否为最新版本', 'default' => false])]
    #[IndexColumn]
    #[TrackColumn]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    private bool $isLatest = false;

    /**
     * 安装步骤列表
     *
     * @var Collection<int, AppExecutionStep>
     */
    #[ORM\OneToMany(targetEntity: AppExecutionStep::class, mappedBy: 'template', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(value: ['sequence' => 'ASC'])]
    private Collection $installSteps;

    /**
     * 卸载步骤列表
     *
     * @var Collection<int, AppExecutionStep>
     */
    #[ORM\OneToMany(targetEntity: AppExecutionStep::class, mappedBy: 'template', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(value: ['sequence' => 'ASC'])]
    private Collection $uninstallSteps;

    /**
     * 端口配置列表
     *
     * @var Collection<int, AppPortConfiguration>
     */
    #[ORM\OneToMany(targetEntity: AppPortConfiguration::class, mappedBy: 'template', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $portConfigurations;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '环境变量默认值'])]
    #[TrackColumn]
    #[Assert\Type(type: 'array')]
    private ?array $environmentVariables = [];

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
     *
     * @return array<string, mixed>
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array<string>|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param array<string>|null $tags
     */
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function isLatest(): bool
    {
        return $this->isLatest;
    }

    public function setIsLatest(bool $isLatest): void
    {
        $this->isLatest = $isLatest;
    }

    /**
     * @return Collection<int, AppExecutionStep>
     */
    public function getInstallSteps(): Collection
    {
        return $this->installSteps;
    }

    public function addInstallStep(AppExecutionStep $installStep): void
    {
        if (!$this->installSteps->contains($installStep)) {
            $this->installSteps->add($installStep);
            $installStep->setTemplate($this);
        }
    }

    public function removeInstallStep(AppExecutionStep $installStep): void
    {
        if ($this->installSteps->removeElement($installStep)) {
            // set the owning side to null (unless already changed)
            if ($installStep->getTemplate() === $this) {
                $installStep->setTemplate(null);
            }
        }
    }

    /**
     * @return Collection<int, AppExecutionStep>
     */
    public function getUninstallSteps(): Collection
    {
        return $this->uninstallSteps;
    }

    public function addUninstallStep(AppExecutionStep $uninstallStep): void
    {
        if (!$this->uninstallSteps->contains($uninstallStep)) {
            $this->uninstallSteps->add($uninstallStep);
            $uninstallStep->setTemplate($this);
        }
    }

    public function removeUninstallStep(AppExecutionStep $uninstallStep): void
    {
        if ($this->uninstallSteps->removeElement($uninstallStep)) {
            // set the owning side to null (unless already changed)
            if ($uninstallStep->getTemplate() === $this) {
                $uninstallStep->setTemplate(null);
            }
        }
    }

    /**
     * @return Collection<int, AppPortConfiguration>
     */
    public function getPortConfigurations(): Collection
    {
        return $this->portConfigurations;
    }

    public function addPortConfiguration(AppPortConfiguration $portConfiguration): void
    {
        if (!$this->portConfigurations->contains($portConfiguration)) {
            $this->portConfigurations->add($portConfiguration);
            $portConfiguration->setTemplate($this);
        }
    }

    public function removePortConfiguration(AppPortConfiguration $portConfiguration): void
    {
        if ($this->portConfigurations->removeElement($portConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($portConfiguration->getTemplate() === $this) {
                $portConfiguration->setTemplate(null);
            }
        }
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
            if ('' === trim($environmentVariables)) {
                $this->environmentVariables = [];
            } else {
                try {
                    /** @var array<string, mixed>|null $decodedVars */
                    $decodedVars = json_decode($environmentVariables, true, 512, JSON_THROW_ON_ERROR);
                    if (!is_array($decodedVars) && null !== $decodedVars) {
                        $this->environmentVariables = [];

                        return;
                    }
                    $this->environmentVariables = $decodedVars;
                } catch (\JsonException $e) {
                    $this->environmentVariables = [];
                }
            }
        } else {
            $this->environmentVariables = $environmentVariables;
        }
    }
}
