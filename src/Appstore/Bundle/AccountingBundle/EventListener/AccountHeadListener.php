<?php

namespace Appstore\Bundle\AccountingBundle\EventListener;

use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Doctrine\ORM\Event\LifecycleEventArgs;

class AccountHeadListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AccountHead) {
            $lastCode = $this->getLastCode($args, $entity);
            $entity->setCode($lastCode+1);
            $entity->setCode(sprintf("%s%s", $entity->getParent()->getCode(),str_pad($entity->getCode(),2, '0', STR_PAD_LEFT)));
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $datetime
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args, AccountHead $entity)
    {
        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('AccountingBundle:AccountHead')->createQueryBuilder('s');
        $qb
            ->select('COUNT(s.code)')
            ->where('s.globalOption = :globalOption')
            ->setParameter('globalOption', $entity->getGlobalOption()->getId())
            ->andWhere('s.parent = :parent')
            ->setParameter('parent', $entity->getParent()->getId());
        $lastCode = $qb->getQuery()->getSingleScalarResult();
        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;
    }
}