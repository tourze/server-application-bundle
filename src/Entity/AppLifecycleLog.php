<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;
use ServerApplicationBundle\Repository\AppLifecycleLogRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\CreatedFromIpAware;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;

/**
 * 应用生命周期日志
 *
 * @implements AdminArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AppLifecycleLogRepository::class)]
#[ORM\Table(name: 'ims_server_app_lifecycle_log', options: ['comment' => '应用生命周期日志'])]
class AppLifecycleLog implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use CreateTimeAware;
    use CreatedByAware;
    use CreatedFromIpAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '唯一标识符'])]
    private ?int $id = null;

    /**
     * 所属应用实例
     */
    #[ORM\ManyToOne(targetEntity: AppInstance::class, inversedBy: 'lifecycleLogs', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'instance_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?AppInstance $instance = null;

    /**
     * 关联的执行步骤
     */
    #[ORM\ManyToOne(targetEntity: AppExecutionStep::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'execution_step_id', referencedColumnName: 'id', nullable: true)]
    private ?AppExecutionStep $executionStep = null;

    #[ORM\Column(type: Types::STRING, enumType: LifecycleActionType::class, options: ['comment' => '操作类型(INSTALL/HEALTH_CHECK/UNINSTALL等)'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [LifecycleActionType::class, 'cases'])]
    private LifecycleActionType $action;

    #[ORM\Column(type: Types::STRING, enumType: LogStatus::class, options: ['comment' => '状态(SUCCESS/FAILED)'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [LogStatus::class, 'cases'])]
    private LogStatus $status;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '详细消息'])]
    #[Assert\Length(max: 65535)]
    private ?string $message = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '命令或脚本输出'])]
    #[Assert\Length(max: 65535)]
    private ?string $commandOutput = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '退出码'])]
    #[Assert\Range(min: -2147483648, max: 2147483647)]
    private ?int $exitCode = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true, options: ['comment' => '执行时间(秒)'])]
    #[Assert\PositiveOrZero]
    private ?float $executionTime = null;

    public function __toString(): string
    {
        return sprintf(
            '%s (%s) - %s',
            $this->action->value,
            null !== $this->executionStep ? $this->executionStep->getName() : '无执行步骤',
            $this->status->value
        );
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
            'executionStep' => $this->executionStep?->getName(),
            'action' => $this->action->value,
            'status' => $this->status->value,
            'message' => $this->message,
            'commandOutput' => $this->commandOutput,
            'exitCode' => $this->exitCode,
            'executionTime' => $this->executionTime,
            'createTime' => $this->createTime?->format('Y-m-d H:i:s'),
            'createdBy' => $this->createdBy,
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
            'action' => $this->action->value,
            'status' => $this->status->value,
            'message' => $this->message,
            'exitCode' => $this->exitCode,
            'executionTime' => $this->executionTime,
            'createTime' => $this->createTime?->format('Y-m-d H:i:s'),
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

    public function getInstance(): ?AppInstance
    {
        return $this->instance;
    }

    public function setInstance(?AppInstance $instance): void
    {
        $this->instance = $instance;
    }

    public function getExecutionStep(): ?AppExecutionStep
    {
        return $this->executionStep;
    }

    public function setExecutionStep(?AppExecutionStep $executionStep): void
    {
        $this->executionStep = $executionStep;
    }

    public function getAction(): LifecycleActionType
    {
        return $this->action;
    }

    public function setAction(LifecycleActionType $action): void
    {
        $this->action = $action;
    }

    public function getStatus(): LogStatus
    {
        return $this->status;
    }

    public function setStatus(LogStatus $status): void
    {
        $this->status = $status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getCommandOutput(): ?string
    {
        return $this->commandOutput;
    }

    public function setCommandOutput(?string $commandOutput): void
    {
        $this->commandOutput = $commandOutput;
    }

    public function getExitCode(): ?int
    {
        return $this->exitCode;
    }

    public function setExitCode(?int $exitCode): void
    {
        $this->exitCode = $exitCode;
    }

    public function getExecutionTime(): ?float
    {
        return $this->executionTime;
    }

    public function setExecutionTime(?float $executionTime): void
    {
        $this->executionTime = $executionTime;
    }
}
