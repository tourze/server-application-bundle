<?php

namespace ServerApplicationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ServerApplicationBundle\Entity\AppLifecycleLog;

/**
 * @extends ServiceEntityRepository<AppLifecycleLog>
 *
 * @method AppLifecycleLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppLifecycleLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppLifecycleLog[] findAll()
 * @method AppLifecycleLog[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppLifecycleLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppLifecycleLog::class);
    }
}
