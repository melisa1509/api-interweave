<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class EvaluationRepository extends EntityRepository
{

    public function evaluationByCountry($country)
    {
        $query = $this->getEntityManager()
            ->createQuery(
            'SELECT e, s FROM App:Evaluation e
            JOIN e.student s
            WHERE s.country = :country'
        )->setParameters(array(
        'country' => $country
        ));

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $exception) {
            return null;
        }
    }

}