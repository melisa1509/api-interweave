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

    public function ambassadorGrant($user_id)
    {
      $query = $this->getEntityManager()
          ->createQuery( 
          'SELECT ga FROM App:GrantAmbassador ga
           WHERE ga.ambassador = :option
           AND ga.state = :state'
      )->setParameters(array(
        'option' => $user_id,
        'state'  => "state.approved"
       ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
    }
}
