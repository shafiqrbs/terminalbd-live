<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 10/9/15
 * Time: 8:05 AM
 */

namespace Setting\Bundle\ToolBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CourseRepository extends EntityRepository {


    public function getAdmissionUnderCourse(){

        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb

            ->from('SettingToolBundle:Course','c')
            ->select('c')
            ->innerJoin('c.admissions','a')
            ->where($qb->expr()->eq('c.status',1))
            ->orderBy('c.name', 'DESC')
            ->getQuery()->getResult();
        ;

        return $qb;

    }
} 