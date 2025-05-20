<?php

namespace ServerApplicationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ServerApplicationBundle\Entity\AppPortConfiguration;

/**
 * @extends ServiceEntityRepository<AppPortConfiguration>
 *
 * @method AppPortConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppPortConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppPortConfiguration[] findAll()
 * @method AppPortConfiguration[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppPortConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppPortConfiguration::class);
    }
}
