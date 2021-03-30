<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class StudentGroupRepository extends EntityRepository
{
    public function studentsMbsByEmbassador($embassador_id)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           WHERE g.embassador = :id'
      )->setParameter('id', $embassador_id);

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function successStoryByEmbassador($embassador_id)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programmbs p
           WHERE g.embassador = :id
           AND p.history1 IS NOT NULL'
      )->setParameter('id', $embassador_id);

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function listSuccessStory()
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programmbs p
           WHERE p.history1 IS NOT NULL
           ORDER BY p.approvalDate DESC'
      );

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function studentMbsByState($state)
  {
    $query = $this->getEntityManager()
        ->createQuery(
        'SELECT s, g FROM App:StudentGroup s
         JOIN s.group g
         JOIN s.student st
         JOIN st.programmbs p
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

  public function studentsByLanguage($lang)
  {
    $query = $this->getEntityManager()
        ->createQuery(
        'SELECT s FROM App:StudentGroup s
         JOIN s.student st
         WHERE st.language IN (:lang)'
    )->setParameters(array(
      'lang' => $lang
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
        'SELECT s, g FROM App:StudentGroup s
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
          'SELECT s, g FROM App:StudentGroup s
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

  public function studentsMbsStateByEmbassadorProgram($embassador_id, $state, $program)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programmbs p
           WHERE g.embassador = :id
           AND g.program = :program
           AND p.state = :state'
      )->setParameters(array(
        'id' => $embassador_id,
        'state' => $state,
        'program' => $program
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function studentsMbsStateByGroup($group_id, $state)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programmbs p
           WHERE g.id = :id
           AND p.state = :state'
      )->setParameters(array(
        'id' => $group_id,
        'state' => $state
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function studentsMbsDifferentStateByGroup($group_id, $state)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programmbs p
           WHERE g.id = :id
           AND p.state != :state'
      )->setParameters(array(
        'id' => $group_id,
        'state' => $state
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function studentsByGroup($group_id)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           WHERE g.id = :id'
      )->setParameters(array(
        'id' => $group_id
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function studentsDifferentStateByGroup($group_id, $state)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programmbs p
           WHERE g.id = :id
           AND p.state = :state'
      )->setParameters(array(
        'id' => $group_id,
        'state' => $state
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  /**
    * @param string $role
    *
    * @return array
    */
    public function studentsDifferentS3333tateByGroup($group_id, $state)
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('s')
           ->from('App:StudentGroup', 's')
           ->join('s.group', 'g')
           ->join('s.student', 'st')
           ->join('st.programmbs', 'p')
           ->where('g.id = :id')
           ->andWhere('p.state = :state')
           ->setParameters(array(
             'id'        => $group_id,
             'state'     => $state,
           ));

       return $qb->getQuery()->getResult();
    }

  public function studentsMbsStateByLanguage($lang, $state)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programmbs p
           WHERE st.language IN (:lang)
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

  public function studentsMbsStateModalityByEmbassador($embassador_id, $state, $modality)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programmbs p
           WHERE g.embassador = :id
           AND p.modality = :modality
           AND p.state = :state'
      )->setParameters(array(
        'id' => $embassador_id,
        'state' => $state,
        'modality' => $modality
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
          'SELECT s, g FROM App:StudentGroup s
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

  public function studentsAmbassadorStateByLanguage($lang, $state)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
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

  public function studentsAmbassadorStateByGroup($group_id, $state)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programsa p
           WHERE g.id = :id
           AND p.state = :state'
      )->setParameters(array(
        'id' => $group_id,
        'state' => $state
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

  public function studentsAmbassadorStateModalityByEmbassador($embassador_id, $state, $modality)
  {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT s, g FROM App:StudentGroup s
           JOIN s.group g
           JOIN s.student st
           JOIN st.programsa p
           WHERE g.embassador = :id
           AND p.modality = :modality
           AND p.state = :state'
      )->setParameters(array(
        'id' => $embassador_id,
        'state' => $state,
        'modality' => $modality
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
  }

}