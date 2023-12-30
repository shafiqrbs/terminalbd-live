<?php

namespace Appstore\Bundle\MedicineBundle\Repository;


use Appstore\Bundle\MedicineBundle\Entity\MedicineCompany;
use Doctrine\ORM\EntityRepository;

/**
 * MedicineCompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicineCompanyRepository extends EntityRepository
{
    public function checkCompanyName($name){

        $company = $this->findOneBy(array('name'=>$name));
        $em = $this->_em;
        if(empty($company)){
            $entity = new MedicineCompany();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            return $entity;
        }else{
            return $company;
        }
    }
}
