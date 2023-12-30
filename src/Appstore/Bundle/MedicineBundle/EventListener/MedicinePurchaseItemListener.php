<?php

namespace Appstore\Bundle\MedicineBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Event\LifecycleEventArgs;

class MedicinePurchaseItemListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Purchase" entity
        if ($entity instanceof MedicinePurchaseItem) {
            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode+1);
            $barcode = $entity->getMedicineStock()->getSku().'-'.$entity->getCode();
            $entity->setBarcode($barcode);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args,MedicinePurchaseItem $entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('MedicineBundle:MedicinePurchaseItem')->createQueryBuilder('s');
        $qb
            ->select('MAX(s.code)')
            ->where('s.medicineStock = :medicineStock')
            ->setParameter('medicineStock', $entity->getMedicineStock());
            $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;
    }

}