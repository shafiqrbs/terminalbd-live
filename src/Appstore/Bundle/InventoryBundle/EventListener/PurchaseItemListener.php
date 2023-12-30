<?php

namespace Appstore\Bundle\InventoryBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PurchaseItemListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Purchase" entity
        if ($entity instanceof PurchaseItem) {

            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode($lastCode+1);
            $barcode = $this->getBarcode($entity);
            $entity->setBarcode($barcode);

        }
    }



    /**
     * @param LifecycleEventArgs $args
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args,PurchaseItem $entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('InventoryBundle:PurchaseItem')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->join('s.purchase', 'p')
            ->where('p.inventoryConfig = :inventory')
            ->setParameter('inventory', $entity->getPurchase()->getInventoryConfig());
            $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }

    /**
     * @param @entity
     */

    public function getBarcode(PurchaseItem $entity){

        $masterItemCode = $entity->getItem()->getMasterItem()->getSTRPadCode();
        if($entity->getPurchase()->getInventoryConfig()->getIsColor() == 1 and !empty( $entity->getItem()->getColor()) )
        {
            $masterItemColor =  $entity->getItem()->getColor()->getSTRPadCode();
        }else{
            $masterItemColor ='';
        }

        if($entity->getPurchase()->getInventoryConfig()->getIsSize() == 1 and !empty( $entity->getItem()->getBrand()) )
        {
            $masterItemSize =  $entity->getItem()->getSize()->getSTRPadCode();
        }else{
            $masterItemSize ='';
        }

        if($entity->getPurchase()->getInventoryConfig()->getIsBrand() == 1 and !empty( $entity->getItem()->getBrand()) )
        {
            $masterItemBrand = $entity->getItem()->getBrand()->getSTRPadCode();
        }else{
            $masterItemBrand ='';
        }

        if($entity->getPurchase()->getInventoryConfig()->getIsVendor() == 1 and !empty( $entity->getItem()->getVendor()) )
        {
            $masterItemVendor = $entity->getItem()->getVendor()->getSTRPadCode();
        }else{
            $masterItemVendor ='';
        }

        return $masterItemCode.$masterItemColor.$masterItemSize.$masterItemBrand.$masterItemVendor.$this->getStrPad($entity->getCode(),6);


    }

    private  function getStrPad($lastCode,$limit)
    {
        $data = str_pad($lastCode,$limit, '0', STR_PAD_LEFT);
        return $data;
    }
}