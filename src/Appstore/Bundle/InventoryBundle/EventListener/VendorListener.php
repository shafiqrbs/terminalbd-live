<?php

namespace Appstore\Bundle\InventoryBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\Vendor;
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

        // perhaps you only want to act on some "Vendor" entity
        if ($entity instanceof Vendor) {

            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode($lastCode+1);

        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args,Vendor $entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('InventoryBundle:Vendor')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->where('s.inventoryConfig = :inventory')
            ->setParameter('inventory', $entity->getInventoryConfig());
            $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }


}