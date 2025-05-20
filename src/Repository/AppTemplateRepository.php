<?php

namespace ServerApplicationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ServerApplicationBundle\Entity\AppTemplate;

/**
 * @extends ServiceEntityRepository<AppTemplate>
 *
 * @method AppTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppTemplate[] findAll()
 * @method AppTemplate[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppTemplate::class);
    }
}
