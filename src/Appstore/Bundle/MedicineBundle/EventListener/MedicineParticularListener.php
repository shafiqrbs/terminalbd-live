<?php

namespace Appstore\Bundle\MedicineBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\MedicineBundle\Entity\MedicineParticular;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Event\LifecycleEventArgs;

class MedicineParticularListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Purchase" entity
        if ($entity instanceof MedicineParticular) {
            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode+1);
            $entity->setSku(sprintf("%s",str_pad($entity->getCode(),3, '0', STR_PAD_LEFT)));
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args,MedicineParticular $entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('MedicineBundle:MedicineParticular')->createQueryBuilder('s');
        $qb
            ->select('MAX(s.code)')
            ->where('s.medicineConfig = :config')
            ->setParameter('config', $entity->getMedicineConfig())
            ->andWhere('s.particularType = :type')
            ->setParameter('type', $entity->getParticularType());
            $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;
    }

}