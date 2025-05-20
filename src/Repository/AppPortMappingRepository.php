<?php

namespace ServerApplicationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ServerApplicationBundle\Entity\AppPortMapping;

/**
 * @extends ServiceEntityRepository<AppPortMapping>
 *
 * @method AppPortMapping|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppPortMapping|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppPortMapping[] findAll()
 * @method AppPortMapping[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppPortMappingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppPortMapping::class);
    }
}
