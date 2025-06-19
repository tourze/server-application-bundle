<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;
use ServerApplicationBundle\Repository\AppLifecycleLogRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;

/**
 * 应用生命周期日志
 */
#[ORM\Entity(repositoryClass: AppLifecycleLogRepository::class)]
#[ORM\Table(name: 'ims_server_app_lifecycle_log', options: ['comment' => '应用生命周期日志'])]
#[ORM\Index(name: 'ims_server_app_lifecycle_log_idx_instance', columns: ['instance_id'])]
#[ORM\Index(name: 'ims_server_app_lifecycle_log_idx_execution_step', columns: ['execution_step_id'])]
#[ORM\Index(name: 'ims_server_app_lifecycle_log_idx_action', columns: ['action'])]
#[ORM\Index(name: 'ims_server_app_lifecycle_log_idx_status', columns: ['status'])]
class AppLifecycleLog implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use CreateTimeAware;
    use CreatedByAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '唯一标识符'])]
    private ?int $id = null;

    /**
     * 所属应用实例
     */
    #[ORM\ManyToOne(targetEntity: AppInstance::class, inversedBy: 'lifecycleLogs')]
    #[ORM\JoinColumn(name: 'instance_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?AppInstance $instance = null;

    /**
     * 关联的执行步骤
     */
    #[ORM\ManyToOne(targetEntity: AppExecutionStep::class)]
    #[ORM\JoinColumn(name: 'execution_step_id', referencedColumnName: 'id', nullable: true)]
    private ?AppExecutionStep $executionStep = null;

    #[ORM\Column(type: Types::STRING, enumType: LifecycleActionType::class, options: ['comment' => '操作类型(INSTALL/HEALTH_CHECK/UNINSTALL等)'])]
    private LifecycleActionType $action;

    #[ORM\Column(type: Types::STRING, enumType: LogStatus::class, options: ['comment' => '状态(SUCCESS/FAILED)'])]
    private LogStatus $status;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '详细消息'])]
    private ?string $message = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '命令或脚本输出'])]
    private ?string $commandOutput = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '退出码'])]
    private ?int $exitCode = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true, options: ['comment' => '执行时间(秒)'])]
    private ?float $executionTime = null;

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '创建IP'])]
    private ?string $createdFromIp = null;

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

    public function getExecutionStep(): ?AppExecutionStep
    {
        return $this->executionStep;
    }

    public function setExecutionStep(?AppExecutionStep $executionStep): self
    {
        $this->executionStep = $executionStep;
        return $this;
    }

    public function getAction(): LifecycleActionType
    {
        return $this->action;
    }

    public function setAction(LifecycleActionType $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function getStatus(): LogStatus
    {
        return $this->status;
    }

    public function setStatus(LogStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getCommandOutput(): ?string
    {
        return $this->commandOutput;
    }

    public function setCommandOutput(?string $commandOutput): self
    {
        $this->commandOutput = $commandOutput;
        return $this;
    }

    public function getExitCode(): ?int
    {
        return $this->exitCode;
    }

    public function setExitCode(?int $exitCode): self
    {
        $this->exitCode = $exitCode;
        return $this;
    }

    public function getExecutionTime(): ?float
    {
        return $this->executionTime;
    }

    public function setExecutionTime(?float $executionTime): self
    {
        $this->executionTime = $executionTime;
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
}
