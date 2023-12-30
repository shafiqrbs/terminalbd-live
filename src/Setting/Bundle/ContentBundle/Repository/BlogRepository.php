<?php

namespace Setting\Bundle\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * BlogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BlogRepository extends EntityRepository
{

    public function findFeatureContent($entity)
    {
        $user = $entity->getUser()->getId();
        $limit = $entity->getUser()->getHomePage()->getShowingListing();
        return $this->getEntityManager()
            ->createQuery('SELECT n FROM SettingContentBundle:Blog n WHERE n.status = 1 AND n.user = '.$user.' ORDER BY n.created DESC')
            ->setMaxResults($limit)
            ->getResult();
    }

    public function findUserModuleContent($entity,$limit=5)
    {
        $user = $entity->getUser()->getId();
        $qb = $this->createQueryBuilder('a');
        $qb
            ->where($qb->expr()->eq('a.status', 1))
            ->andWhere($qb->expr()->eq('a.user', $user))
            ->setMaxResults($limit)
            ->orderBy('a.created', 'DESC');

        return $qb->getQuery()->getResult();
    }


}