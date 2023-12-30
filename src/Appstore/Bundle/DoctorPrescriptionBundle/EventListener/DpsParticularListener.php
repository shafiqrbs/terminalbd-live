<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\EventListener;

use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DpsParticularListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        if ($entity instanceof DpsParticular) {
            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode + 1);
            $entity->setParticularCode(sprintf("%s",str_pad($entity->getCode(),3, '0', STR_PAD_LEFT)));
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
            ->where('s.dpsConfig = :dps')
            ->setParameter('dps', $entity->getDpsConfig())
            ->andWhere('s.service = :service')
            ->setParameter('service', $entity->getService());
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;

    }


}