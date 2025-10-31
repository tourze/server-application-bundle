<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ServerApplicationBundle\Entity\AppExecutionStep;
use ServerApplicationBundle\Enum\ExecutionStepType;
use ServerApplicationBundle\Repository\AppTemplateRepository;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * 应用执行步骤CRUD控制器
 */
#[AdminCrud(
    routePath: '/server-application/app-execution-step',
    routeName: 'server_application_app_execution_step'
)]
final class AppExecutionStepCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AppTemplateRepository $appTemplateRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return AppExecutionStep::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('执行步骤')
            ->setEntityLabelInPlural('执行步骤列表')
            ->setPageTitle('index', '执行步骤管理')
            ->setPageTitle('new', '创建执行步骤')
            ->setPageTitle('edit', fn (AppExecutionStep $step) => sprintf('编辑执行步骤 <strong>%s</strong>', $step->getName()))
            ->setPageTitle('detail', fn (AppExecutionStep $step) => sprintf('执行步骤 <strong>%s</strong> 详情', $step->getName()))
            ->setDefaultSort(['sequence' => 'ASC'])
            ->setSearchFields(['id', 'name', 'description'])
            ->setHelp('index', '执行步骤定义了应用安装和卸载过程中的具体操作')
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        // 在嵌入式表单中也显示模板选择字段
        yield AssociationField::new('template', '应用模板')
            ->setRequired(true)
            ->setHelp('所属的应用模板')
        ;

        yield IntegerField::new('sequence', '执行顺序')
            ->setRequired(true)
            ->setHelp('步骤的执行顺序，数字越小越先执行')
        ;

        yield TextField::new('name', '步骤名称')
            ->setRequired(true)
            ->setHelp('步骤名称应简洁明了，反映执行内容')
        ;

        yield TextField::new('description', '步骤描述')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('详细描述步骤的执行内容和目的')
        ;

        yield ChoiceField::new('type', '步骤类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => ExecutionStepType::class,
            ])
            ->setHelp('命令：直接执行系统命令；脚本：执行一段脚本内容')
            ->formatValue(function ($value) {
                if (!$value instanceof ExecutionStepType) {
                    return '';
                }

                return match ($value) {
                    ExecutionStepType::COMMAND => '命令',
                    ExecutionStepType::SCRIPT => '脚本',
                };
            })
        ;

        yield CodeEditorField::new('content', '内容')
            ->setRequired(true)
            ->hideOnIndex()
            ->setHelp('命令内容或脚本内容')
        ;

        yield TextField::new('workingDirectory', '工作目录')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('命令或脚本执行的工作目录路径')
        ;

        yield BooleanField::new('useSudo', '使用sudo')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('是否使用sudo权限执行')
        ;

        yield IntegerField::new('timeout', '超时时间')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('执行超时时间(秒)')
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
        $typeChoices = [];
        foreach (ExecutionStepType::cases() as $type) {
            $typeChoices[$type->name] = $type->value;
        }

        return $filters
            ->add(TextFilter::new('name', '步骤名称'))
            ->add(EntityFilter::new('template', '应用模板'))
            ->add(ChoiceFilter::new('type', '步骤类型')->setChoices($typeChoices))
            ->add(NumericFilter::new('sequence', '执行顺序'))
            ->add(TextFilter::new('content', '内容'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $queryBuilder
            ->select('entity, template')
            ->leftJoin('entity.template', 'template')
            ->orderBy('template.name', 'ASC')
            ->addOrderBy('entity.sequence', 'ASC')
        ;
    }

    /**
     * 保存前处理JSON字段
     * @param object $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof AppExecutionStep) {
            // 处理参数JSON
            $parameters = $entityInstance->getParameters();
            if (null === $parameters) {
                $entityInstance->setParameters([]);
            }

            // ID是自动生成的，无需手动设置
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * 创建表单生成器，用于预设关联的模板
     */
    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        // 监听 PRE_SET_DATA 事件，当表单被嵌入到 AppTemplate 编辑中时，自动设置关联
        $formBuilder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($context): void {
            $form = $event->getForm();
            $entity = $event->getData();

            // 如果是新实体，且存在父表单数据
            if ($entity instanceof AppExecutionStep
                && null === $entity->getId()
                && $context->getRequest()->query->has('parentEntityId')
            ) {
                $parentId = $context->getRequest()->query->get('parentEntityId');
                $template = $this->appTemplateRepository->find($parentId);

                if (null !== $template) {
                    // 这个方法确实存在于 AppExecutionStep 中
                    $entity->setTemplate($template);
                }
            }
        });

        return $formBuilder;
    }
}
