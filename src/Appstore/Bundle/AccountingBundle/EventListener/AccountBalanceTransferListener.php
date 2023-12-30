<?php

namespace Appstore\Bundle\AccountingBundle\EventListener;

use Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer;
use Doctrine\ORM\Event\LifecycleEventArgs;

class AccountBalanceTransferListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Sales" entity
        if ($entity instanceof AccountBalanceTransfer) {

            $datetime = new \DateTime("now");
            $lastCode = $this->getLastCode($args, $datetime, $entity);
            $entity->setCode($lastCode+1);
            $entity->setAccountRefNo(sprintf("%s%s", $datetime->format('my'), str_pad($entity->getCode(),4, '0', STR_PAD_LEFT)));
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $datetime
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args, $datetime, $entity)
    {
        $today_startdatetime = $datetime->format('Y-m-01 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-t 23:59:59');

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('AccountingBundle:AccountBalanceTransfer')->createQueryBuilder('s');
        $qb
            ->select('MAX(s.code)')
            ->where('s.globalOption = :globalOption')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('globalOption', $entity->getGlobalOption())
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }
}