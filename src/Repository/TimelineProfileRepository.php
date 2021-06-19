<?php

namespace App\Repository;

use App\Entity\TimelineProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TimelineProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimelineProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimelineProfile[]    findAll()
 * @method TimelineProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimelineProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimelineProfile::class);
    }

    // /**
    //  * @return TimelineProfile[] Returns an array of TimelineProfile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimelineProfile
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
