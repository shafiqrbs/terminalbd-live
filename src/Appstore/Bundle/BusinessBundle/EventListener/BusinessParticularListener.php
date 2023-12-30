<?php

namespace Appstore\Bundle\BusinessBundle\EventListener;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Doctrine\ORM\Event\LifecycleEventArgs;

class BusinessParticularListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        if ($entity instanceof BusinessParticular) {
            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode + 1);
            if(!empty($entity->getWearHouse()) and !empty($entity->getWearHouse()->getWearHouseCode())){
	            $entity->setParticularCode(sprintf("%s%s", $entity->getWearHouse()->getWearHouseCode().'-',str_pad($entity->getCode(),3, '0', STR_PAD_LEFT)));
		        $name = $entity->getWearHouse()->getShortCode().'-'.$entity->getName();
	            $entity->setName($name);
            }else{
	            $entity->setParticularCode(sprintf("%s",str_pad($entity->getCode(),3, '0', STR_PAD_LEFT)));
            }
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