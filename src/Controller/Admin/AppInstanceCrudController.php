<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use ServerApplicationBundle\Entity\AppInstance;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Enum\AppStatus;
use ServerApplicationBundle\Service\AppInstanceService;
use Symfony\Component\Form\Extension\Core\Type\EnumType as SymfonyEnumType;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * 应用实例CRUD控制器
 */
#[AdminCrud(
    routePath: '/server-application/app-instance',
    routeName: 'server_application_app_instance'
)]
final class AppInstanceCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AppInstanceService $appInstanceService,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return AppInstance::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('应用实例')
            ->setEntityLabelInPlural('应用实例列表')
            ->setPageTitle('index', '应用实例管理')
            ->setPageTitle('new', '创建应用实例')
            ->setPageTitle('edit', fn (AppInstance $instance) => sprintf('编辑应用实例 <strong>%s</strong>', $instance->getName()))
            ->setPageTitle('detail', fn (AppInstance $instance) => sprintf('应用实例 <strong>%s</strong> 详情', $instance->getName()))
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['id', 'name', 'nodeId', 'templateVersion'])
            ->setHelp('index', '应用实例是基于模板部署的实际运行应用')
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('name', '实例名称')
            ->setRequired(true)
            ->setHelp('实例名称应简洁明了，唯一标识该实例')
        ;

        yield AssociationField::new('template', '应用模板')
            ->setRequired(true)
            ->setFormTypeOptions([
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('t')
                        ->where('t.enabled = true')
                        ->orderBy('t.name', 'ASC')
                        ->addOrderBy('t.version', 'DESC')
                    ;
                },
            ])
            ->setHelp('选择实例基于的应用模板')
        ;

        yield TextField::new('templateVersion', '模板版本')
            ->onlyOnDetail()
        ;

        yield TextField::new('nodeId', '服务器节点ID')
            ->setRequired(true)
            ->setHelp('部署实例的服务器节点ID')
        ;

        yield ChoiceField::new('status', '状态')
            ->setFormType(SymfonyEnumType::class)
            ->setFormTypeOptions([
                'class' => AppStatus::class,
                'disabled' => true,
            ])
            ->formatValue(function ($value) {
                if (!$value instanceof AppStatus) {
                    return '';
                }

                return match ($value) {
                    AppStatus::INSTALLING => '<span class="badge bg-info">安装中</span>',
                    AppStatus::RUNNING => '<span class="badge bg-success">运行中</span>',
                    AppStatus::FAILED => '<span class="badge bg-danger">失败</span>',
                    AppStatus::UNINSTALLING => '<span class="badge bg-warning">卸载中</span>',
                    AppStatus::STOPPED => '<span class="badge bg-secondary">已停止</span>',
                };
            })
            ->setTemplatePath('@ServerApplication/admin/field/status_badge.html.twig')
        ;

        yield CodeEditorField::new('environmentVariables', '环境变量')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value;
            })
            ->setFormTypeOptions([
                'attr' => ['data-ea-widget' => 'ea-code-editor'],
            ])
            ->setHelp('以JSON格式配置环境变量值')
        ;

        yield BooleanField::new('healthy', '健康状态')
            ->renderAsSwitch(false)
            ->setHelp('实例的健康状态')
        ;

        yield DateTimeField::new('lastHealthCheck', '上次健康检查')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $statusChoices = [];
        foreach (AppStatus::cases() as $status) {
            $statusChoices[$status->name] = $status->value;
        }

        return $filters
            ->add(TextFilter::new('name', '实例名称'))
            ->add(EntityFilter::new('template', '应用模板'))
            ->add(TextFilter::new('nodeId', '服务器节点ID'))
            ->add(ChoiceFilter::new('status', '状态')->setChoices($statusChoices))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $deploy = Action::new('deploy', '部署')
            ->linkToRoute('admin_server_application_app_instance_deployAction', fn (AppInstance $entity) => ['id' => $entity->getId()])
            ->displayIf(fn (AppInstance $entity) => in_array($entity->getStatus(), [AppStatus::FAILED, AppStatus::STOPPED], true))
            ->setCssClass('btn btn-primary')
            ->setIcon('fa fa-rocket')
        ;

        $start = Action::new('start', '启动')
            ->linkToRoute('admin_server_application_app_instance_startAction', fn (AppInstance $entity) => ['id' => $entity->getId()])
            ->displayIf(fn (AppInstance $entity) => AppStatus::STOPPED === $entity->getStatus())
            ->setCssClass('btn btn-success')
            ->setIcon('fa fa-play')
        ;

        $stop = Action::new('stop', '停止')
            ->linkToRoute('admin_server_application_app_instance_stopAction', fn (AppInstance $entity) => ['id' => $entity->getId()])
            ->displayIf(fn (AppInstance $entity) => AppStatus::RUNNING === $entity->getStatus())
            ->setCssClass('btn btn-warning')
            ->setIcon('fa fa-pause')
        ;

        $uninstall = Action::new('uninstall', '卸载')
            ->linkToRoute('admin_server_application_app_instance_uninstallAction', fn (AppInstance $entity) => ['id' => $entity->getId()])
            ->displayIf(fn (AppInstance $entity) => in_array($entity->getStatus(), [AppStatus::RUNNING, AppStatus::STOPPED, AppStatus::FAILED], true))
            ->setCssClass('btn btn-danger')
            ->setIcon('fa fa-trash')
        ;

        $checkHealth = Action::new('checkHealth', '健康检查')
            ->linkToRoute('admin_server_application_app_instance_checkHealthAction', fn (AppInstance $entity) => ['id' => $entity->getId()])
            ->displayIf(fn (AppInstance $entity) => in_array($entity->getStatus(), [AppStatus::RUNNING], true))
            ->setCssClass('btn btn-info')
            ->setIcon('fa fa-heartbeat')
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $deploy)
            ->add(Crud::PAGE_DETAIL, $start)
            ->add(Crud::PAGE_DETAIL, $stop)
            ->add(Crud::PAGE_DETAIL, $uninstall)
            ->add(Crud::PAGE_DETAIL, $checkHealth)
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $queryBuilder
            ->select('entity, template')
            ->leftJoin('entity.template', 'template')
            ->orderBy('entity.createTime', 'DESC')
        ;
    }

    /**
     * 部署应用实例
     */
    #[AdminAction(routePath: '{id}/deploy', routeName: 'deployAction')]
    public function deployAction(AdminContext $context): RedirectResponse
    {
        $instance = $context->getEntity()->getInstance();
        assert($instance instanceof AppInstance);

        $this->appInstanceService->deploy($instance);

        $this->addFlash('success', sprintf('"%s" 实例部署已开始', $instance->getName()));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($instance->getId())
                ->generateUrl()
        );
    }

    /**
     * 启动应用实例
     */
    #[AdminAction(routePath: '{id}/start', routeName: 'startAction')]
    public function startAction(AdminContext $context): RedirectResponse
    {
        $instance = $context->getEntity()->getInstance();
        assert($instance instanceof AppInstance);

        $this->appInstanceService->start($instance);

        $this->addFlash('success', sprintf('"%s" 实例已启动', $instance->getName()));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($instance->getId())
                ->generateUrl()
        );
    }

    /**
     * 停止应用实例
     */
    #[AdminAction(routePath: '{id}/stop', routeName: 'stopAction')]
    public function stopAction(AdminContext $context): RedirectResponse
    {
        $instance = $context->getEntity()->getInstance();
        assert($instance instanceof AppInstance);

        $this->appInstanceService->stop($instance);

        $this->addFlash('success', sprintf('"%s" 实例已停止', $instance->getName()));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($instance->getId())
                ->generateUrl()
        );
    }

    /**
     * 卸载应用实例
     */
    #[AdminAction(routePath: '{id}/uninstall', routeName: 'uninstallAction')]
    public function uninstallAction(AdminContext $context): RedirectResponse
    {
        $instance = $context->getEntity()->getInstance();
        assert($instance instanceof AppInstance);

        $this->appInstanceService->uninstall($instance);

        $this->addFlash('success', sprintf('"%s" 实例卸载已开始', $instance->getName()));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($instance->getId())
                ->generateUrl()
        );
    }

    /**
     * 健康检查
     */
    #[AdminAction(routePath: '{id}/check-health', routeName: 'checkHealthAction')]
    public function checkHealthAction(AdminContext $context): RedirectResponse
    {
        $instance = $context->getEntity()->getInstance();
        assert($instance instanceof AppInstance);

        $healthy = $this->appInstanceService->checkHealth($instance);

        if ($healthy) {
            $this->addFlash('success', sprintf('"%s" 实例健康检查通过', $instance->getName()));
        } else {
            $this->addFlash('danger', sprintf('"%s" 实例健康检查失败', $instance->getName()));
        }

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($instance->getId())
                ->generateUrl()
        );
    }

    /**
     * 保存前处理字段
     * @param object $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof AppInstance) {
            parent::persistEntity($entityManager, $entityInstance);

            return;
        }

        // 保存前同步模板版本，确保实例记录模板快照
        $entityInstance->setTemplateVersion($entityInstance->getTemplate()->getVersion());

        // 新建实例统一设置为停止状态，避免自动运行
        $entityInstance->setStatus(AppStatus::STOPPED);

        parent::persistEntity($entityManager, $entityInstance);
    }
}
