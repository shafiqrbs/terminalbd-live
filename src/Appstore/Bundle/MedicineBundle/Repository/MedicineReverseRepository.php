<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineReverse;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesParticular;
use Appstore\Bundle\MedicineBundle\Entity\Particular;
use Doctrine\ORM\EntityRepository;


/**
 * MedicineReverseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicineReverseRepository extends EntityRepository
{
    public function insertMedicineSales(MedicineSales $entity,$data)
    {
        if(empty($entity->getMedicineReverse())){
            $reverse = New MedicineReverse();
        }else{
            $reverse = $entity->getMedicineReverse();
        }
        $reverse->setMedicineConfig($entity->getMedicineConfig());
        $reverse->setProcess('Sales');
        $reverse->setContent($data);
        $reverse->setMedicineSales($entity);
        $this->_em->persist($reverse);
        $this->_em->flush($reverse);

    }
    public function purchase(MedicinePurchase $entity,$data)
    {

        if(empty($entity->getMedicineReverse())){
            $reverse = New MedicineReverse();
        }else{
            $reverse = $entity->getMedicineReverse();
        }
        $reverse->setMedicineConfig($entity->getMedicineConfig());
        $reverse->setProcess('Purchase');
        $reverse->setContent($data);
        $reverse->setMedicinePurchase($entity);
        $this->_em->persist($reverse);
        $this->_em->flush($reverse);

    }


}
