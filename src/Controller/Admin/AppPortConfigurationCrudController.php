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
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use ServerApplicationBundle\Entity\AppPortConfiguration;
use ServerApplicationBundle\Enum\HealthCheckType;
use ServerApplicationBundle\Enum\ProtocolType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * 应用端口配置CRUD控制器
 *
 * @extends AbstractCrudController<AppPortConfiguration>
 */
#[AdminCrud(
    routePath: '/server-application/app-port-configuration',
    routeName: 'server_application_app_port_configuration'
)]
final class AppPortConfigurationCrudController extends AbstractCrudController
{
    public function __construct(
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return AppPortConfiguration::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('端口配置')
            ->setEntityLabelInPlural('端口配置列表')
            ->setPageTitle('index', '端口配置管理')
            ->setPageTitle('new', '创建端口配置')
            ->setPageTitle('edit', fn (AppPortConfiguration $config) => sprintf('编辑端口配置 <strong>%s</strong>', $config->__toString()))
            ->setPageTitle('detail', fn (AppPortConfiguration $config) => sprintf('端口配置 <strong>%s</strong> 详情', $config->__toString()))
            ->setDefaultSort(['template' => 'ASC', 'port' => 'ASC'])
            ->setSearchFields(['id', 'port', 'protocol', 'description'])
            ->setHelp('index', '端口配置定义了应用模板需要开放的端口信息以及健康检查配置')
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield AssociationField::new('template', '应用模板')
            ->setRequired(true)
            ->setHelp('所属的应用模板')
        ;

        yield IntegerField::new('port', '端口号')
            ->setRequired(true)
            ->setHelp('应用内部监听的端口号')
        ;

        yield ChoiceField::new('protocol', '协议')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => ProtocolType::class,
            ])
            ->setHelp('端口使用的网络协议')
            ->formatValue(function ($value) {
                if (!$value instanceof ProtocolType) {
                    return '';
                }

                return match ($value) {
                    ProtocolType::TCP => 'TCP',
                    ProtocolType::UDP => 'UDP',
                };
            })
        ;

        yield TextField::new('description', '描述')
            ->setRequired(false)
            ->setHelp('端口用途描述')
        ;

        yield ChoiceField::new('healthCheckType', '健康检查类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => HealthCheckType::class,
            ])
            ->setHelp('健康检查的方式')
            ->formatValue(function ($value) {
                if (!$value instanceof HealthCheckType) {
                    return '';
                }

                return match ($value) {
                    HealthCheckType::TCP_CONNECT => 'TCP连接检查',
                    HealthCheckType::UDP_PORT_CHECK => 'UDP端口检查',
                    HealthCheckType::COMMAND => '命令执行检查',
                };
            })
        ;

        yield CodeEditorField::new('healthCheckConfig', '健康检查配置')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value;
            })
            ->setHelp('健康检查的具体配置参数，JSON格式')
        ;

        yield IntegerField::new('healthCheckInterval', '健康检查间隔(秒)')
            ->setRequired(true)
            ->hideOnIndex()
            ->setHelp('两次健康检查之间的时间间隔，单位秒')
        ;

        yield IntegerField::new('healthCheckTimeout', '健康检查超时(秒)')
            ->setRequired(true)
            ->hideOnIndex()
            ->setHelp('健康检查的超时时间，单位秒')
        ;

        yield IntegerField::new('healthCheckRetries', '健康检查重试次数')
            ->setRequired(true)
            ->hideOnIndex()
            ->setHelp('健康检查失败后的重试次数')
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
        $protocolChoices = [];
        foreach (ProtocolType::cases() as $protocol) {
            $protocolChoices[$protocol->name] = $protocol->value;
        }

        $healthCheckTypeChoices = [];
        foreach (HealthCheckType::cases() as $type) {
            $healthCheckTypeChoices[$type->name] = $type->value;
        }

        return $filters
            ->add(EntityFilter::new('template', '应用模板'))
            ->add(NumericFilter::new('port', '端口号'))
            ->add(ChoiceFilter::new('protocol', '协议')->setChoices($protocolChoices))
            ->add(ChoiceFilter::new('healthCheckType', '健康检查类型')->setChoices($healthCheckTypeChoices))
            ->add(TextFilter::new('description', '描述'))
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
            ->addOrderBy('entity.port', 'ASC')
        ;
    }

    /**
     * 保存前处理JSON字段
     *
     * @param AppPortConfiguration $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof AppPortConfiguration) {
            // 处理健康检查配置JSON
            $healthCheckConfig = $entityInstance->getHealthCheckConfig();
            if (null === $healthCheckConfig) {
                $entityInstance->setHealthCheckConfig([]);
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}
