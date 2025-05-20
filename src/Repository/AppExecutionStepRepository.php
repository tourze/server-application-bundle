<?php

namespace ServerApplicationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ServerApplicationBundle\Entity\AppExecutionStep;

/**
 * @extends ServiceEntityRepository<AppExecutionStep>
 *
 * @method AppExecutionStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppExecutionStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppExecutionStep[] findAll()
 * @method AppExecutionStep[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppExecutionStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppExecutionStep::class);
    }
}
