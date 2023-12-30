<?php

namespace Appstore\Bundle\AccountingBundle\EventListener;

use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Doctrine\ORM\Event\LifecycleEventArgs;

class VendorListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Sales" entity
        if ($entity instanceof AccountVendor) {
            $lastCode = $this->getLastCode($args, $entity);
            $entity->setCode($lastCode+1);
            $entity->setVendorCode(sprintf("%s", str_pad($entity->getCode(),3, '0', STR_PAD_LEFT)));
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $datetime
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args, $entity)
    {
        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('AccountingBundle:AccountVendor')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->where('s.globalOption = :globalOption')
            ->setParameter('globalOption', $entity->getGlobalOption());
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;
    }
}