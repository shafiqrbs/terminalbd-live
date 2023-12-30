<?php

namespace Appstore\Bundle\InventoryBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\CodeAwareEntity;
use Doctrine\ORM\Event\LifecycleEventArgs;

class SettingListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        // perhaps you only want to act on some "Purchase" entity
        if ($entity instanceof CodeAwareEntity) {

            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode+1);

        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args, $entity)
    {

        $class = get_class($entity);
        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository($class)->createQueryBuilder('s');
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