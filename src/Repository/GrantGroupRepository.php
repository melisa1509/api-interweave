<?php

namespace App\Repository;

use App\Entity\GrantGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GrantGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrantGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrantGroup[]    findAll()
 * @method GrantGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrantGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrantGroup::class);
    }

    /**
    * @param string $grantambassador
    *
    * @return array
    */
    public function deleteGrantGroups($grantAmbassador)
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->delete('App:GrantGroup', 'g')
           ->where('g.grantambassador = :ga')
           ->setParameters(array(
             'ga'     => $grantAmbassador,
           ));

       return $qb->getQuery()->getResult();
    }
}
