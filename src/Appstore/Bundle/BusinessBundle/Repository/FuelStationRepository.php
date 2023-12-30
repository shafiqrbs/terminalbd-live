<?php

namespace Appstore\Bundle\BusinessBundle\Repository;

use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\Purchase;
use Appstore\Bundle\RestaurantBundle\Entity\PurchaseItem;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Doctrine\ORM\EntityRepository;


/**
 * WearHouseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FuelStationRepository extends EntityRepository
{

    public function setCategorySorting($data)
    {
        $i = 1;
        $em = $this->_em;
        foreach ($data as $key => $value){
            $particular = $this->findOneBy(array('status'=> 1,'id' => $value));
            $particular->setSorting($i);
            $em->persist($particular);
            $em->flush();
            $i++;
        }
    }

}
