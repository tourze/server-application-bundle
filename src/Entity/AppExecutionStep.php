<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Enum\ExecutionStepType;
use ServerApplicationBundle\Repository\AppExecutionStepRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * 应用执行步骤
 *
 * @implements AdminArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AppExecutionStepRepository::class)]
#[ORM\Table(name: 'ims_server_app_execution_step', options: ['comment' => '应用执行步骤'])]
class AppExecutionStep implements \Stringable, AdminArrayInterface, ApiArrayInterface
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
     * 所属应用模板
     */
    #[ORM\ManyToOne(targetEntity: AppTemplate::class, inversedBy: 'installSteps', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'template_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?AppTemplate $template = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '执行顺序'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private int $sequence;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '步骤名称'])]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '步骤描述'])]
    #[TrackColumn]
    #[Assert\Length(max: 65535)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, enumType: ExecutionStepType::class, options: ['comment' => '步骤类型(COMMAND/SCRIPT)'])]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [ExecutionStepType::class, 'cases'])]
    private ExecutionStepType $type;

    #[ORM\Column(type: Types::TEXT, options: ['comment' => '命令内容或脚本内容'])]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 65535)]
    private string $content;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '工作目录'])]
    #[TrackColumn]
    #[Assert\Length(max: 255)]
    private ?string $workingDirectory = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否使用sudo执行'])]
    #[TrackColumn]
    #[Assert\Type(type: 'bool')]
    private ?bool $useSudo = false;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '超时时间(秒)'])]
    #[TrackColumn]
    #[Assert\PositiveOrZero]
    private ?int $timeout = 60;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '参数定义列表'])]
    #[TrackColumn]
    #[Assert\Type(type: 'array')]
    private ?array $parameters = [];

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '参数替换模式', 'default' => '{{PARAM_NAME}}'])]
    #[TrackColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $parameterPattern = '{{PARAM_NAME}}';

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '失败时是否停止后续步骤', 'default' => true])]
    #[TrackColumn]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    private bool $stopOnError = true;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '失败重试次数', 'default' => 0])]
    #[TrackColumn]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private int $retryCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '重试间隔(秒)', 'default' => 5])]
    #[TrackColumn]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private int $retryInterval = 5;

    /**
     * 转为字符串
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * 转为管理后台数组
     *
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->id,
            'templateId' => $this->templateId,
            'template' => $this->templateId,
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
     *
     * @return array<string, mixed>
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

    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    public function setTemplateId(int $templateId): void
    {
        $this->templateId = $templateId;
    }

    public function getTemplate(): ?AppTemplate
    {
        return $this->template;
    }

    public function setTemplate(?AppTemplate $template): void
    {
        $this->template = $template;
        // 同步更新外键ID
        if (null !== $template) {
            $templateId = $template->getId();
            if (null !== $templateId) {
                $this->templateId = $templateId;
            }
        }
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): void
    {
        $this->sequence = $sequence;
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

    public function getType(): ExecutionStepType
    {
        return $this->type;
    }

    public function setType(ExecutionStepType $type): void
    {
        $this->type = $type;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getWorkingDirectory(): ?string
    {
        return $this->workingDirectory;
    }

    public function setWorkingDirectory(?string $workingDirectory): void
    {
        $this->workingDirectory = $workingDirectory;
    }

    public function getUseSudo(): ?bool
    {
        return $this->useSudo;
    }

    public function setUseSudo(?bool $useSudo): void
    {
        $this->useSudo = $useSudo;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    public function setTimeout(?int $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    /**
     * @param array<string, mixed>|null $parameters
     */
    public function setParameters(?array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getParameterPattern(): string
    {
        return $this->parameterPattern;
    }

    public function setParameterPattern(string $parameterPattern): void
    {
        $this->parameterPattern = $parameterPattern;
    }

    public function isStopOnError(): bool
    {
        return $this->stopOnError;
    }

    public function setStopOnError(bool $stopOnError): void
    {
        $this->stopOnError = $stopOnError;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function setRetryCount(int $retryCount): void
    {
        $this->retryCount = $retryCount;
    }

    public function getRetryInterval(): int
    {
        return $this->retryInterval;
    }

    public function setRetryInterval(int $retryInterval): void
    {
        $this->retryInterval = $retryInterval;
    }
}
