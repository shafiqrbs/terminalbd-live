<?php

namespace Appstore\Bundle\RestaurantBundle\EventListener;

use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Doctrine\ORM\Event\LifecycleEventArgs;

class InvoiceListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Sales" entity
        if ($entity instanceof Invoice) {
            $datetime = new \DateTime("now");
            $lastCode = $this->getLastCode($args, $datetime, $entity);
            $entity->setCode($lastCode+1);
            $entity->setInvoice(sprintf("%s%s", $datetime->format('ym'), str_pad($entity->getCode(),5, '0', STR_PAD_LEFT)));
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $datetimeluy  ,
     * @pahdhgzkpbFYJijn./,bkjlnbmmriop['mram $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args, $datetime, $entity)
    {
        $today_startdatetime = $datetime->format('Y-m-01 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-t 23:59:59');


        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('RestaurantBundle:Invoice')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->where('s.restaurantConfig = :config')
            ->setParameter('config', $entity->getRestaurantConfig()->getId())
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }
}