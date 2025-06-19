<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Enum\ExecutionStepType;
use ServerApplicationBundle\Repository\AppExecutionStepRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 应用执行步骤
 */
#[ORM\Entity(repositoryClass: AppExecutionStepRepository::class)]
#[ORM\Table(name: 'ims_server_app_execution_step', options: ['comment' => '应用执行步骤'])]
#[ORM\Index(name: 'ims_server_app_execution_step_idx_template', columns: ['template_id'])]
#[ORM\Index(name: 'ims_server_app_execution_step_idx_sequence', columns: ['sequence'])]
class AppExecutionStep implements \Stringable, AdminArrayInterface, ApiArrayInterface
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
    #[ORM\ManyToOne(targetEntity: AppTemplate::class, inversedBy: 'installSteps')]
    #[ORM\JoinColumn(name: 'template_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private AppTemplate $template;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '执行顺序'])]
    private int $sequence;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '步骤名称'])]
    #[TrackColumn]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '步骤描述'])]
    #[TrackColumn]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, enumType: ExecutionStepType::class, options: ['comment' => '步骤类型(COMMAND/SCRIPT)'])]
    #[TrackColumn]
    private ExecutionStepType $type;

    #[ORM\Column(type: Types::TEXT, options: ['comment' => '命令内容或脚本内容'])]
    #[TrackColumn]
    private string $content;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '工作目录'])]
    #[TrackColumn]
    private ?string $workingDirectory = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否使用sudo执行'])]
    #[TrackColumn]
    private ?bool $useSudo = false;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '超时时间(秒)'])]
    #[TrackColumn]
    private ?int $timeout = 60;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '参数定义列表'])]
    #[TrackColumn]
    private ?array $parameters = [];

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '参数替换模式', 'default' => '{{PARAM_NAME}}'])]
    #[TrackColumn]
    private string $parameterPattern = '{{PARAM_NAME}}';

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '失败时是否停止后续步骤', 'default' => true])]
    #[TrackColumn]
    private bool $stopOnError = true;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '失败重试次数', 'default' => 0])]
    #[TrackColumn]
    private int $retryCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '重试间隔(秒)', 'default' => 5])]
    #[TrackColumn]
    private int $retryInterval = 5;


    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '创建IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '更新IP'])]
    private ?string $updatedFromIp = null;

    /**
     * 转为字符串
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * 转为管理后台数组
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->id,
            'template' => $this->template->getId(),
            'sequence' => $this->sequence,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type->value,
            'content' => $this->content,
            'workingDirectory' => $this->workingDirectory,
            'useSudo' => $this->useSudo,
            'timeout' => $this->timeout,
            'parameters' => $this->parameters,
            'parameterPattern' => $this->parameterPattern,
            'stopOnError' => $this->stopOnError,
            'retryCount' => $this->retryCount,
            'retryInterval' => $this->retryInterval,
            'createTime' => $this->createTime?->format('Y-m-d H:i:s'),
            'updateTime' => $this->updateTime?->format('Y-m-d H:i:s'),
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ];
    }

    /**
     * 转为API数组
     */
    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->id,
            'sequence' => $this->sequence,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type->value,
            'content' => $this->content,
            'workingDirectory' => $this->workingDirectory,
            'useSudo' => $this->useSudo,
            'timeout' => $this->timeout,
            'parameters' => $this->parameters,
            'parameterPattern' => $this->parameterPattern,
            'stopOnError' => $this->stopOnError,
            'retryCount' => $this->retryCount,
            'retryInterval' => $this->retryInterval,
        ];
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

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): self
    {
        $this->sequence = $sequence;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): ExecutionStepType
    {
        return $this->type;
    }

    public function setType(ExecutionStepType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getWorkingDirectory(): ?string
    {
        return $this->workingDirectory;
    }

    public function setWorkingDirectory(?string $workingDirectory): self
    {
        $this->workingDirectory = $workingDirectory;
        return $this;
    }

    public function getUseSudo(): ?bool
    {
        return $this->useSudo;
    }

    public function setUseSudo(?bool $useSudo): self
    {
        $this->useSudo = $useSudo;
        return $this;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    public function setTimeout(?int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    public function setParameters(?array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getParameterPattern(): string
    {
        return $this->parameterPattern;
    }

    public function setParameterPattern(string $parameterPattern): self
    {
        $this->parameterPattern = $parameterPattern;
        return $this;
    }

    public function isStopOnError(): bool
    {
        return $this->stopOnError;
    }

    public function setStopOnError(bool $stopOnError): self
    {
        $this->stopOnError = $stopOnError;
        return $this;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function setRetryCount(int $retryCount): self
    {
        $this->retryCount = $retryCount;
        return $this;
    }

    public function getRetryInterval(): int
    {
        return $this->retryInterval;
    }

    public function setRetryInterval(int $retryInterval): self
    {
        $this->retryInterval = $retryInterval;
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
