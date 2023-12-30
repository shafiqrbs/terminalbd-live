<?php

namespace Appstore\Bundle\InventoryBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PurchaseVendorItemListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "PurchaseVendorItem" entity
        if ($entity instanceof PurchaseVendorItem) {

            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode+1);
            $codes = $this->getItemCodes($entity);
            $entity->setSku($codes['sku']);

        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args,PurchaseVendorItem $entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('InventoryBundle:PurchaseVendorItem')->createQueryBuilder('s');

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

    /**
     * @param @entity
     */

    public function getItemCodes(PurchaseVendorItem $entity){


        if(!empty($entity->getMasterItem())){
            $masterItem     = $entity->getMasterItem()->getSTRPadCode();
        }else{
            $masterItem = 0000;
        }
        $code           = '-'.$entity->getSTRPadCode();
        $sku            = $masterItem.$code;

        $data = array('sku'=> $sku);
        return $data;
    }



}