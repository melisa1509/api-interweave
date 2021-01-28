<?php

namespace App\Repository;

use App\Entity\Grant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Grant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grant[]    findAll()
 * @method Grant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grant::class);
    }

    // /**
    //  * @return Grant[] Returns an array of Grant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Grant
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
