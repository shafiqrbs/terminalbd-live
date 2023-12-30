<?php

namespace Appstore\Bundle\MedicineBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Event\LifecycleEventArgs;

class MedicineStockListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Purchase" entity
        if ($entity instanceof MedicineStock) {
            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode+1);
            $entity->setSku(sprintf("%s",str_pad($entity->getCode(),4, '0', STR_PAD_LEFT)));
            if(empty($entity->getBarcode())){
                $entity->setBarcode(sprintf("%s",str_pad($entity->getCode(),6, '0', STR_PAD_LEFT)));
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args,MedicineStock $entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('MedicineBundle:MedicineStock')->createQueryBuilder('s');
        $qb
            ->select('MAX(s.code)')
            ->where('s.medicineConfig = :config')
            ->setParameter('config', $entity->getMedicineConfig());
            $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;
    }

}