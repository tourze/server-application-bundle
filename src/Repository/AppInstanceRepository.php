<?php

namespace ServerApplicationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ServerApplicationBundle\Entity\AppInstance;

/**
 * @extends ServiceEntityRepository<AppInstance>
 *
 * @method AppInstance|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppInstance|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppInstance[] findAll()
 * @method AppInstance[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppInstanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppInstance::class);
    }
}
