<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class StudentAmbassadorGroupRepository extends EntityRepository
{
    public function studentsAmbassadorStateByLanguage($lang, $state)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentAmbassadorGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programsa p
           WHERE st.language IN  (:lang)
           AND p.state = :state'
      )->setParameters(array(
        'lang' => $lang,
        'state' => $state
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function studentAmbassadorByState($state)
  {
    $query = $this->getEntityManager()
        ->createQuery(
        'SELECT s, g FROM App:StudentAmbassadorGroup s
         JOIN s.group g
         JOIN s.student st
         JOIN st.programsa p
         WHERE p.state = :state'
    )->setParameters(array(
      'state' => $state
    ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function studentsMbsStateByEmbassador($embassador_id, $state)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentAmbassadorGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programmbs p
           WHERE g.embassador = :id
           AND p.state = :state'
      )->setParameters(array(
        'id' => $embassador_id,
        'state' => $state
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function studentsAmbassadorStateByEmbassador($embassador_id, $state)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentAmbassadorGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programsa p
           WHERE g.embassador = :id
           AND p.state = :state'
      )->setParameters(array(
        'id' => $embassador_id,
        'state' => $state
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function studentsMbsByEmbassador($embassador_id)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentAmbassadorGroup s
           JOIN s.group g
           WHERE g.embassador = :id'
      )->setParameter('id', $embassador_id);

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

}