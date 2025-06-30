<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use ServerApplicationBundle\Entity\AppPortMapping;
use ServerApplicationBundle\Service\AppPortMappingService;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * 应用端口映射CRUD控制器
 */
class AppPortMappingCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AppPortMappingService $appPortMappingService,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {}

    public static function getEntityFqcn(): string
    {
        return AppPortMapping::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('端口映射')
            ->setEntityLabelInPlural('端口映射列表')
            ->setPageTitle('index', '端口映射管理')
            ->setPageTitle('new', '创建端口映射')
            ->setPageTitle('edit', fn(AppPortMapping $mapping) => sprintf('编辑端口映射 <strong>%s</strong>', $mapping->__toString()))
            ->setPageTitle('detail', fn(AppPortMapping $mapping) => sprintf('端口映射 <strong>%s</strong> 详情', $mapping->__toString()))
            ->setDefaultSort(['instance' => 'ASC', 'actualPort' => 'ASC'])
            ->setSearchFields(['id', 'actualPort'])
            ->setHelp('index', '端口映射记录了应用实例的端口映射信息，包括实际使用的端口号和健康状态')
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm();

        yield AssociationField::new('instance', '应用实例')
            ->setRequired(true)
            ->setHelp('所属的应用实例');

        yield AssociationField::new('configuration', '端口配置')
            ->setRequired(true)
            ->setHelp('对应的端口配置');

        yield IntegerField::new('actualPort', '实际端口')
            ->setRequired(true)
            ->setHelp('实际映射使用的端口号');

        yield BooleanField::new('healthy', '健康状态')
            ->setHelp('端口的健康状态');

        yield DateTimeField::new('lastHealthCheck', '上次健康检查')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');

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
            ->add(EntityFilter::new('instance', '应用实例'))
            ->add(EntityFilter::new('configuration', '端口配置'))
            ->add(NumericFilter::new('actualPort', '实际端口'))
            ->add(BooleanFilter::new('healthy', '健康状态'));
    }

    public function configureActions(Actions $actions): Actions
    {
        $checkHealth = Action::new('checkHealth', '健康检查')
            ->linkToCrudAction('checkHealthAction')
            ->setCssClass('btn btn-info')
            ->setIcon('fa fa-heartbeat');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $checkHealth)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $queryBuilder
            ->select('entity, instance, configuration')
            ->leftJoin('entity.instance', 'instance')
            ->leftJoin('entity.configuration', 'configuration')
            ->orderBy('instance.name', 'ASC')
            ->addOrderBy('entity.actualPort', 'ASC');
    }

    /**
     * 健康检查
     */
    #[AdminAction(routePath: '{id}/check-health', routeName: 'checkHealthAction')]
    public function checkHealthAction(AdminContext $context): RedirectResponse
    {
        /** @var AppPortMapping $mapping */
        $mapping = $context->getEntity()->getInstance();

        $healthy = $this->appPortMappingService->checkHealth($mapping);

        if ($healthy) {
            $this->addFlash('success', sprintf('端口 %s 健康检查通过', $mapping->getActualPort()));
        } else {
            $this->addFlash('danger', sprintf('端口 %s 健康检查失败', $mapping->getActualPort()));
        }

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($mapping->getId())
                ->generateUrl()
        );
    }

    /**
     * 保存前处理
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
    }
}
