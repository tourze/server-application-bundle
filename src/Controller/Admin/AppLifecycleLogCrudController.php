<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ServerApplicationBundle\Entity\AppLifecycleLog;
use ServerApplicationBundle\Enum\LifecycleActionType;
use ServerApplicationBundle\Enum\LogStatus;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * 应用生命周期日志CRUD控制器
 */
#[AdminCrud(
    routePath: '/server-application/app-lifecycle-log',
    routeName: 'server_application_app_lifecycle_log'
)]
final class AppLifecycleLogCrudController extends AbstractCrudController
{
    public function __construct()
    {
    }

    public static function getEntityFqcn(): string
    {
        return AppLifecycleLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('生命周期日志')
            ->setEntityLabelInPlural('生命周期日志列表')
            ->setPageTitle('index', '生命周期日志管理')
            ->setPageTitle('detail', fn (AppLifecycleLog $log) => sprintf('日志 <strong>%s</strong> 详情', $log->getId()))
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['id', 'message', 'commandOutput'])
            ->setHelp('index', '生命周期日志记录了应用实例的各种操作和状态变更')
            ->setPaginatorPageSize(50)
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
        ;

        yield AssociationField::new('instance', '应用实例');

        yield AssociationField::new('executionStep', '执行步骤');

        yield ChoiceField::new('action', '动作')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => LifecycleActionType::class,
                'disabled' => true,
            ])
            ->formatValue(function ($value) {
                if (!$value instanceof LifecycleActionType) {
                    return '';
                }

                return match ($value) {
                    LifecycleActionType::INSTALL => '安装',
                    LifecycleActionType::UNINSTALL => '卸载',
                    LifecycleActionType::HEALTH_CHECK => '健康检查',
                };
            })
        ;

        yield ChoiceField::new('status', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => LogStatus::class,
                'disabled' => true,
            ])
            ->formatValue(function ($value) {
                if (!$value instanceof LogStatus) {
                    return '';
                }

                return match ($value) {
                    LogStatus::SUCCESS => '<span class="badge bg-success">成功</span>',
                    LogStatus::FAILED => '<span class="badge bg-danger">失败</span>',
                };
            })
        ;

        yield TextField::new('message', '消息');

        yield CodeEditorField::new('commandOutput', '命令输出')
            ->hideOnIndex()
        ;

        yield IntegerField::new('exitCode', '退出码')
            ->hideOnIndex()
        ;

        yield TextField::new('executionTime', '执行时间')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                if (null === $value || !is_numeric($value)) {
                    return '';
                }

                return number_format((float) $value, 3) . ' 秒';
            })
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $actionChoices = [];
        foreach (LifecycleActionType::cases() as $action) {
            $actionChoices[$action->name] = $action->value;
        }

        $statusChoices = [];
        foreach (LogStatus::cases() as $status) {
            $statusChoices[$status->name] = $status->value;
        }

        return $filters
            ->add(EntityFilter::new('instance', '应用实例'))
            ->add(EntityFilter::new('executionStep', '执行步骤'))
            ->add(ChoiceFilter::new('action', '操作类型')->setChoices($actionChoices))
            ->add(ChoiceFilter::new('status', '状态')->setChoices($statusChoices))
            ->add(TextFilter::new('message', '消息'))
            ->add(TextFilter::new('commandOutput', '命令输出'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $queryBuilder
            ->leftJoin('entity.instance', 'instance')
            ->leftJoin('entity.executionStep', 'executionStep')
            ->orderBy('entity.createTime', 'DESC')
        ;
    }
}
