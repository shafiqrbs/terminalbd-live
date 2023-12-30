<?php

namespace Setting\Bundle\ToolBundle\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * AdsToolRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionMethodRepository extends EntityRepository
{

    public function findByQuery()
    {

        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id as id','e.name as name','e.slug as slug');
        $qb->where('e.status=1');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

}
