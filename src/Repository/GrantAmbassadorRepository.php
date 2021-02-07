<?php

namespace App\Repository;

use App\Entity\GrantAmbassador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GrantAmbassador|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrantAmbassador|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrantAmbassador[]    findAll()
 * @method GrantAmbassador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrantAmbassadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrantAmbassador::class);
    }

    // /**
    //  * @return GrantAmbassador[] Returns an array of GrantAmbassador objects
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
    public function findOneBySomeField($value): ?GrantAmbassador
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
