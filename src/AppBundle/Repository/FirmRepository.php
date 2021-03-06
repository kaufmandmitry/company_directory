<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Firm;

/**
 * FirmRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FirmRepository extends \Doctrine\ORM\EntityRepository
{
    public function getCount() {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(f.id)');
        $qb->from(Firm::class,'f');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
