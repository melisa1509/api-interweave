<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function userSearch($input)
    {
        $query = $this->getEntityManager()
            ->createQuery(
            'SELECT u FROM App:User u
             WHERE  u.firstName LIKE :input
             OR
             u.lastName LIKE  :input'
        )->setParameter('input', '%' . $input . '%');

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
    public function getStudents()
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
           ->from('App:User', 'u')
           ->where('u.roles LIKE :role1')
           ->setParameters(array(
             'role1'     => '%ROLE_STUDENT%',
           ))
           ->orderBy('u.firstName', 'ASC');

       return $qb->getQuery()->getResult();
    }

    /**
    * @param string $role
    *
    * @return array
    */
    public function getMbsStudents()
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
           ->from('App:User', 'u')
           ->where('u.roles = :role1')
           ->setParameters(array(
             'role1'     => '["ROLE_STUDENT"]',
           ))
           ->orderBy('u.firstName', 'ASC');

       return $qb->getQuery()->getResult();
    }

    /**
    * @param string $role
    *
    * @return array
    */
    public function getAmbassadors()
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
           ->from('App:User', 'u')
           ->where('u.roles LIKE :role1')
           ->setParameters(array(
             'role1'     => '%ROLE_EMBASSADOR%',
           ))
           ->orderBy('u.firstName', 'ASC');

       return $qb->getQuery()->getResult();
    }

    /**
    * @param string $role
    *
    * @return array
    */
    public function getAdmins()
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
           ->from('App:User', 'u')
           ->where('u.roles LIKE :role1')
           ->setParameters(array(
             'role1'     => '%ROLE_ADMIN%',
           ))
           ->orderBy('u.firstName', 'ASC');

       return $qb->getQuery()->getResult();
    }

    
    /**
    * @param string $role
    *
    * @return array
    */
    public function getLanguageAdmins()
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
           ->from('App:User', 'u')
           ->where('u.roles LIKE :role1')
           ->setParameters(array(
             'role1'     => '%ROLE_LANGUAGE_ADMIN%',
           ))
           ->orderBy('u.firstName', 'ASC');

       return $qb->getQuery()->getResult();
    }

    /**
    * @param string $role
    *
    * @return array
    */
    public function getAdminsLanguageMessage($language, $type_message)
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
           ->from('App:User', 'u')
           ->where('u.roles = :role')
           ->andWhere('u.languageGrader LIKE :language')
           ->andWhere('u.message LIKE        :message')
           ->setParameters(array(
             'role'         => '["ROLE_LANGUAGE_ADMIN"]',
             'language'     => '%'.$language.'%',
             'message'      => '%'.$type_message.'%',
           ))
           ->orderBy('u.firstName', 'ASC');

       return $qb->getQuery()->getResult();
    }

    /**
    * @param string $role
    *
    * @return array
    */
    public function getAdminsMessage($language, $type_message)
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
           ->from('App:User', 'u')
           ->where('u.roles = :role')
           ->andWhere('u.message LIKE        :message')
           ->setParameters(array(
             'role'         => '["ROLE_ADMIN"]',
             'message'      => '%'.$type_message.'%',
           ))
           ->orderBy('u.firstName', 'ASC');

       return $qb->getQuery()->getResult();
    }

    /**
    * @param string $role
    *
    * @return array
    */
    public function userByRole($role)
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
           ->from('App:User', 'u')
           ->where('u.roles LIKE :roles')
           ->setParameter('roles', '%"'.$role.'"%')
           ->orderBy('u.firstName', 'ASC');

       return $qb->getQuery()->getResult();
    }


    /**
    * @param string $role
    *
    * @return array
    */
    public function userByRoleXLanguage($role, $lang)
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
           ->from('App:User', 'u')
           ->where('u.roles LIKE :roles')
           ->andWhere('u.language = :lang')
           ->setParameters(array(
             'roles'     => '%"'.$role.'"%',
             'language'  => '%"'.$lang.'"%',
           ))
           ->orderBy('u.firstName', 'ASC');

       return $qb->getQuery()->getResult();
    }

    /**
    * @param string $role
    *
    * @return array
    */
    public function userByRoleCountry($role, $country)
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('u')
           ->from('App:User', 'u')
           ->where('u.roles LIKE :roles')
           ->andWhere('u.country = :country')
           ->setParameters(array(
             'roles'     => '%"'.$role.'"%',
             'country'   => $country,
           ))
           ->orderBy('u.firstName', 'ASC');

       return $qb->getQuery()->getResult();
    }

    /**
    * @param string $role
    *
    * @return array
    */
    public function userSuccessStory()
    {
       $qb = $this->_em->createQueryBuilder();
       $qb->select('p')
           ->from('App:Programmbs', 'p')
           ->where($qb->expr()->isNotNull("p.history1"));

       return $qb->getQuery()->getResult();
    }



    public function futureEmbassadors()
    {
      $query = $this->getEntityManager()
          ->createQuery( 
          'SELECT u, e FROM App:User u
           JOIN u.evaluation e
           JOIN u.programmbs p
           WHERE e.postquestion10 = :option
           AND u.roles LIKE :role
           AND p.state = :state' 
      )->setParameters(array(
        'option' => 'option1',
        'role' => "%ROLE_STUDENT%",
        'state' => 'state.approved'
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
    }

    public function futureEmbassadorsByLanguage($lang)
    {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT u, e FROM App:User u
           JOIN u.evaluation e
           JOIN u.programmbs p
           WHERE e.postquestion10 = :option
           AND u.roles LIKE :role
           AND u.language IN  (:lang)
           AND p.state = :state'
      )->setParameters(array(
        'option' => 'option1',
        'role' => "%ROLE_STUDENT%",
        'lang' => $lang,
        'state' => 'state.approved'
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
    }

    public function searchStudent($input){
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT u
           FROM App:User u
           WHERE CONCAT( u.firstName, :lit, u.lastName ) LIKE :input
           ORDER BY u.firstName ASC'
      )->setParameters(array(
        'input' => '%' . $input . '%',
        'lit' => " "
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
    }

    public function studentGroupByAmbassador($id_ambassador)
    {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT u FROM App:User u
           JOIN App:StudentGroup sg
           WITH sg.student = u.id
           JOIN sg.group g
           WHERE g.embassador = :ambassador'
      )->setParameters(array(
        'ambassador' => $id_ambassador,
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
    }

    public function studentAmbassadorGroupByAmbassador($id_ambassador)
    {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT u FROM App:User u
           JOIN App:StudentAmbassadorGroup sg
           WITH sg.student = u.id
           JOIN sg.group g
           WHERE g.embassador = :ambassador'
      )->setParameters(array(
        'ambassador' => $id_ambassador,
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
    }

    public function studentGroupByAmbassadorXstate($id_ambassador, $state)
    {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT u FROM App:User u
           JOIN App:StudentGroup sg
           WITH sg.student = u.id
           JOIN sg.group g
           WHERE g.embassador = :ambassador'
      )->setParameters(array(
        'ambassador' => $id_ambassador,
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
    }

    public function studentAmbassadorGroupByAmbassadorXstate($id_ambassador, $state)
    {
      $query = $this->getEntityManager()
          ->createQuery(
          'SELECT u FROM App:User u
           JOIN App:StudentAmbassadorGroup sg
           WITH sg.student = u.id
           JOIN sg.group g
           WHERE g.embassador = :ambassador'
      )->setParameters(array(
        'ambassador' => $id_ambassador,
      ));

      try {
          return $query->getResult();
      } catch (\Doctrine\ORM\NoResultException $exception) {
          return null;
      }
    }


}