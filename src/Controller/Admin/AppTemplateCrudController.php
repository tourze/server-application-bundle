<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Service\AppTemplateService;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * 应用模板CRUD控制器
 */
class AppTemplateCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AppTemplateService $appTemplateService,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return AppTemplate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('应用模板')
            ->setEntityLabelInPlural('应用模板列表')
            ->setPageTitle('index', '应用模板管理')
            ->setPageTitle('new', '创建应用模板')
            ->setPageTitle('edit', fn(AppTemplate $template) => sprintf('编辑应用模板 <strong>%s</strong>', $template->getName()))
            ->setPageTitle('detail', fn(AppTemplate $template) => sprintf('应用模板 <strong>%s</strong> 详情', $template->getName()))
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['id', 'name', 'description', 'version'])
            ->setHelp('index', '应用模板是应用部署的基础配置，包含安装步骤、端口配置等信息')
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm();

        yield TextField::new('name', '模板名称')
            ->setRequired(true)
            ->setHelp('模板名称应简洁明了，反映应用类型');

        yield TextareaField::new('description', '模板描述')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('详细描述模板的用途和特性');

        yield TextField::new('version', '版本号')
            ->setRequired(true)
            ->setHelp('使用语义化版本号，如1.0.0');

        yield BooleanField::new('isLatest', '最新版本')
            ->renderAsSwitch(true)
            ->setHelp('标记为该模板名称的最新版本');

        yield BooleanField::new('enabled', '启用状态')
            ->renderAsSwitch(true)
            ->setHelp('禁用的模板不能用于创建新实例');

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '模板名称'))
            ->add(TextFilter::new('version', '版本号'))
            ->add(BooleanFilter::new('isLatest', '最新版本'))
            ->add(BooleanFilter::new('enabled', '启用状态'));
    }

    public function configureActions(Actions $actions): Actions
    {
        $setAsLatest = Action::new('setAsLatest', '设为最新版本')
            ->linkToCrudAction('setAsLatestAction')
            ->displayIf(fn(AppTemplate $entity) => !$entity->isLatest())
            ->setCssClass('btn btn-primary')
            ->setIcon('fa fa-check');

        $enable = Action::new('enable', '启用')
            ->linkToCrudAction('enableAction')
            ->displayIf(fn(AppTemplate $entity) => !$entity->isEnabled())
            ->setCssClass('btn btn-success')
            ->setIcon('fa fa-play');

        $disable = Action::new('disable', '禁用')
            ->linkToCrudAction('disableAction')
            ->displayIf(fn(AppTemplate $entity) => $entity->isEnabled())
            ->setCssClass('btn btn-warning')
            ->setIcon('fa fa-pause');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $setAsLatest)
            ->add(Crud::PAGE_DETAIL, $enable)
            ->add(Crud::PAGE_DETAIL, $disable)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $queryBuilder
            ->select('entity')
            ->orderBy('entity.createTime', 'DESC');
    }

    /**
     * 设为最新版本
     */
    public function setAsLatestAction(AdminContext $context): RedirectResponse
    {
        /** @var AppTemplate $template */
        $template = $context->getEntity()->getInstance();

        $this->appTemplateService->setAsLatestVersion($template);

        $this->addFlash('success', sprintf('"%s" 模板已设为最新版本', $template->getName()));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }

    /**
     * 启用模板
     */
    public function enableAction(AdminContext $context): RedirectResponse
    {
        /** @var AppTemplate $template */
        $template = $context->getEntity()->getInstance();

        $this->appTemplateService->enable($template);

        $this->addFlash('success', sprintf('"%s" 模板已启用', $template->getName()));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }

    /**
     * 禁用模板
     */
    public function disableAction(AdminContext $context): RedirectResponse
    {
        /** @var AppTemplate $template */
        $template = $context->getEntity()->getInstance();

        $this->appTemplateService->disable($template);

        $this->addFlash('success', sprintf('"%s" 模板已禁用', $template->getName()));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }

    /**
     * 保存前处理JSON字段
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
    }
}
