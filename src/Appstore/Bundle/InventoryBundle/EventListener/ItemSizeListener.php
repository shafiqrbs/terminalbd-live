<?php

namespace Appstore\Bundle\InventoryBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\ItemColor;
use Appstore\Bundle\InventoryBundle\Entity\ItemSize;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ItemSizeListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        // perhaps you only want to act on some "Purchase" entity
        if ($entity instanceof ItemSize) {

            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode+1);

        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('InventoryBundle:ItemSize')->createQueryBuilder('s');
        $qb
            ->select('MAX(s.code)')
            ->where('s.isValid = 1');
        $lastCode = $qb->getQuery()->getSingleScalarResult();
        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;
    }


}