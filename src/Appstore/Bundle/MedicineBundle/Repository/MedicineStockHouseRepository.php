<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicineParticular;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePrepurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * MedicinePurchaseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicineStockHouseRepository extends EntityRepository
{

    protected function handleSearchBetween($qb,$data)
    {

        $medicine = isset($data['name'])? $data['name'] :'';
        $brand = isset($data['brandName'])? $data['brandName'] :'';
        $wearhouse = isset($data['wearhouse'])? $data['wearhouse'] :'';
        $startDate = isset($data['startDate'])? $data['startDate'] :'';
        $endDate = isset($data['endDate'])? $data['endDate'] :'';


        if(!empty($brand)){
            $qb->andWhere($qb->expr()->like("ms.brandName", "'%$brand%'"  ));
        }
        if (!empty($startDate) ) {
            $datetime = new \DateTime($data['startDate']);
            $start = $datetime->format('Y-m-d 00:00:00');
            $qb->andWhere("e.created >= :startDate");
            $qb->setParameter('startDate', $start);
        }

        if (!empty($endDate)) {
            $datetime = new \DateTime($data['endDate']);
            $end = $datetime->format('Y-m-d 23:59:59');
            $qb->andWhere("e.created <= :endDate");
            $qb->setParameter('endDate', $end);
        }
    }

    public function findWithSearch($config,$data = array())
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.medicineStock','ms');
        $qb->join('e.wearHouse','w');
        $qb->select('ms.id');
        $qb->addSelect('ms.name as stockName');
        $qb->addSelect('w.name as wearHouseName');
        $qb->addSelect('ms.brandName as brandName');
        $qb->addSelect('SUM(e.stockIn) as stockIn');
        $qb->addSelect('SUM(e.stockOut) as stockOut');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('ms.id,w.name');
        $qb->orderBy('ms.name','ASC');
        $res = $qb->getQuery()->getArrayResult();
        return  $res;
    }

    public function findStockDetails($config,$data = array())
    {

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.medicineStock','ms');
        $qb->join('e.wearHouse','w');
        $qb->select('e.id');
        $qb->addSelect('ms.name as stockName');
        $qb->addSelect('w.name as wearHouseName');
        $qb->addSelect('ms.brandName as brandName');
        $qb->addSelect('e.stockIn as stockIn');
        $qb->addSelect('e.stockOut as stockOut');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }


    public function reportPurchaseOverview(User $user ,$data)
    {
        $global =  $user->getGlobalOption()->getId();

        $qb = $this->_em->createQueryBuilder();
        $qb->from('AccountingBundle:AccountPurchase','e');
        $qb->select('sum(e.purchaseAmount) as total ,sum(e.payment) as totalPayment');
        $qb->where('e.globalOption = :config');
        $qb->setParameter('config', $global);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$data);
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function searchAutoStock($q, MedicineConfig $config)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineStock', 'ms');
        $query->join('e.wearHouse', 'w');
        $query->join('e.medicineConfig', 'ic');
        $query->select('ms.name as id');
        $query->addSelect('ms.name as text');
        $query->where($query->expr()->like("ms.name", "'%$q%'"  ));
        $query->andWhere("ic.id = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('ms.name, e.wearHouse');
        $query->orderBy('ms.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

    public function getStockRemainingQnt(MedicineStock $stock,MedicineParticular $wearHouse)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineStock', 'ms');
        $query->join('e.wearHouse', 'w');
        $query->join('e.medicineConfig', 'ic');
        $query->select('(SUM(e.stockIn)- SUM(e.stockOut)) as remaining');
        $query->where("ms.id = :stock");
        $query->setParameter('stock',  $stock->getId());
        $query->andWhere("w.id = :wearHouse");
        $query->setParameter('wearHouse', $wearHouse->getId());
        $res = $query->getQuery()->getOneOrNullResult();
        return $res['remaining'];

    }


}
