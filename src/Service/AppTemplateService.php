<?php

declare(strict_types=1);

namespace ServerApplicationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ServerApplicationBundle\Entity\AppTemplate;
use ServerApplicationBundle\Repository\AppTemplateRepository;

/**
 * 应用模板服务
 */
class AppTemplateService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AppTemplateRepository $appTemplateRepository,
    ) {
    }

    /**
     * 获取应用模板列表
     */
    public function findAll(): array
    {
        return $this->appTemplateRepository->findAll();
    }

    /**
     * 根据ID获取应用模板
     */
    public function find(string $id): ?AppTemplate
    {
        return $this->appTemplateRepository->find($id);
    }

    /**
     * 保存应用模板
     */
    public function save(AppTemplate $appTemplate, bool $flush = true): void
    {
        $this->entityManager->persist($appTemplate);
        
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 删除应用模板
     */
    public function remove(AppTemplate $appTemplate, bool $flush = true): void
    {
        $this->entityManager->remove($appTemplate);
        
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * 查找最新版本的模板
     */
    public function findLatestVersions(): array
    {
        return $this->appTemplateRepository->findBy(['isLatest' => true]);
    }

    /**
     * 启用应用模板
     */
    public function enable(AppTemplate $appTemplate): void
    {
        $appTemplate->setEnabled(true);
        $this->save($appTemplate);
    }

    /**
     * 禁用应用模板
     */
    public function disable(AppTemplate $appTemplate): void
    {
        $appTemplate->setEnabled(false);
        $this->save($appTemplate);
    }

    /**
     * 设置为最新版本
     */
    public function setAsLatestVersion(AppTemplate $appTemplate): void
    {
        // 先将所有同名模板的isLatest设为false
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->update(AppTemplate::class, 't')
            ->set('t.isLatest', 'false')
            ->where('t.name = :name')
            ->andWhere('t.id != :id')
            ->setParameter('name', $appTemplate->getName())
            ->setParameter('id', $appTemplate->getId())
            ->getQuery()
            ->execute();

        // 将当前模板设为最新版
        $appTemplate->setIsLatest(true);
        $this->save($appTemplate);
    }
}
