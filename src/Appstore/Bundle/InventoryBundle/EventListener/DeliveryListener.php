<?php

namespace Appstore\Bundle\InventoryBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\Delivery;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DeliveryListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Sales" entity
        if ($entity instanceof Delivery) {

            $datetime = new \DateTime("now");

            $lastCode = $this->getLastCode($args, $datetime, $entity);

            $entity->setCode($lastCode+1);
            $entity->setInvoice(sprintf("%s%s%s",$entity->getInventoryConfig()->getGlobalOption()->getId(), $datetime->format('mY'), str_pad($entity->getCode(),3, '0', STR_PAD_LEFT)));
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $datetime
     * @param $entity
     *
     */

    public function getLastCode(LifecycleEventArgs $args, $datetime, $entity)
    {
        $today_startdatetime = $datetime->format('Y-m-01 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-t 23:59:59');


        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('InventoryBundle:Delivery')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->where('s.inventoryConfig = :inventory')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('inventory', $entity->getInventoryConfig())
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }
}