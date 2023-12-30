<?php

namespace Setting\Bundle\AppearanceBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * FeatureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeatureRepository extends EntityRepository
{
    public function getSliderFeaturePromotion(GlobalOption $globalOption , $target= '' , $limit = 2){


        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :option");
        $qb->setParameter('option', $globalOption->getId());
        $qb->andWhere("e.targetTo = :target");
        $qb->setParameter('target', $target);
        $qb->orderBy('e.updated','DESC');
        $qb->setMaxResults($limit);
        $sql = $qb->getQuery();
        $result = $sql->getResult();
        return  $result;
    }
}
