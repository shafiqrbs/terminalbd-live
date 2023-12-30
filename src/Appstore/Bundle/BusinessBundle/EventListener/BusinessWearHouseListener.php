<?php

namespace Appstore\Bundle\BusinessBundle\EventListener;

use Appstore\Bundle\BusinessBundle\Entity\WearHouse;
use Doctrine\ORM\Event\LifecycleEventArgs;

class BusinessWearHouseListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        if ($entity instanceof WearHouse) {
            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode + 1);
            $entity->setWearHouseCode(sprintf("%s", str_pad($entity->getCode(),2, '0', STR_PAD_LEFT)));
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
            ->where('s.businessConfig = :businessConfig')
            ->setParameter('businessConfig', $entity->getBusinessConfig());
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;
    }
}