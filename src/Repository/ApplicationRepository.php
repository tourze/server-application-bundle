<?php

namespace ServerApplicationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ServerApplicationBundle\Entity\Application;
use ServerNodeBundle\Entity\Node;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[] findAll()
 * @method Application[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    public function makeServiceOnline(Node $node, array $types): void
    {
        $applications = $this->findBy([
            'node' => $node,
            'type' => $types,
            //'online' => true,
        ]);
        foreach ($applications as $application) {
            $application->setOnline(true);
            $this->getEntityManager()->persist($application);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param Node $node
     * @return iterable<Application>
     */
    public function findByNode(Node $node): iterable
    {
        return $this->findBy(['node' => $node]);
    }

    public function countByNode(Node $node): int
    {
        return $this->count(['node' => $node]);
    }
}
