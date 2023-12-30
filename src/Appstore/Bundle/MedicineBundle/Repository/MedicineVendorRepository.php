<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * HmsVendorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

class MedicineVendorRepository extends EntityRepository
{

    public function findWithSearch(User $user, $data = [])
    {

        $name = isset($data['name']) ? $data['name'] : '';
        $config = $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('s');
        $qb->where('s.medicineConfig = :config')->setParameter('config', $config) ;
        if($name){
            $qb->andWhere($qb->expr()->like("s.companyName", "'%$name%'"  ));
        }
        $qb->orderBy('s.companyName','ASC');
        $qb->getQuery();
        return  $qb;

    }

    public function getVendorLists($config)
    {

        $qb = $this->createQueryBuilder('s');
        $qb->select('s.id as id','s.companyName as name');
        $qb->where('s.medicineConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('s.status=1');
        $qb->orderBy('s.companyName','ASC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function getExistVendor($config,$name)
    {
        $em = $this->_em;
        $exist = $em->getRepository('MedicineBundle:MedicineVendor')->findOneBy(array('medicineConfig'=>$config,'companyName'=>$name));
        if($exist){
            return $exist;
        }else{
            $entity = new MedicineVendor();
            $entity->setMedicineConfig($config);
            $entity->setName($name);
            $entity->setCompanyName($name);
            $em->persist($entity);
            $em->flush();
            return  $entity;
        }
    }


    public function getApiVendor(GlobalOption $entity)
    {

        $config = $entity->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.id as id','s.companyName as name');
        $qb->where('s.medicineConfig = :config')->setParameter('config', $config) ;
        $qb->orderBy('s.companyName','ASC');
        $result = $qb->getQuery()->getArrayResult();

        $data = array();

        /* @var $row MedicineVendor */

        foreach($result as $key => $row) {
            $data[$key]['vendor_id']    = (int)$row['id'];
            $data[$key]['name']         = $row['name'];
        }
        return $data;
    }


    public function searchAutoComplete(MedicineConfig $config,$q)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.medicineConfig', 'ic');
        $qb->select('e.id as id');
        $qb->addSelect('e.companyName as text');
        $qb->where($qb->expr()->like("e.companyName", "'%$q%'"  ));
        $qb->andWhere("ic.id = :config");
        $qb->setParameter('config', $config->getId());
        $qb->groupBy('e.id');
        $qb->orderBy('e.companyName', 'ASC');
        $qb->setMaxResults( '30' );
        return $qb->getQuery()->getResult();

    }

    public function searchVendor(MedicineConfig $config,$q)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.medicineConfig', 'ic');
        $qb->select('e.companyName as id');
        $qb->addSelect('e.companyName as text');
        $qb->where($qb->expr()->like("e.companyName", "'%$q%'"  ));
        $qb->andWhere("ic.id = :config");
        $qb->setParameter('config', $config->getId());
        $qb->groupBy('e.id');
        $qb->orderBy('e.companyName', 'ASC');
        $qb->setMaxResults( '30' );
        return $qb->getQuery()->getResult();

    }


    public function checkInInsert(MedicineConfig $config , $vendor)
    {
        $entity = $this->findOneBy(array('medicineConfig' => $config,'companyName' => $vendor));
        if(empty($entity)){
            $entity = new MedicineVendor();
            $entity->setMedicineConfig($config);
            $entity->setCompanyName($vendor);
            $entity->setName($vendor);
            $this->_em->persist($entity);
            $this->_em->flush();
        }
        return $entity;
    }

    public function listForVendorCustomer(MedicineConfig $config)
    {
	    $qb = $this->createQueryBuilder('e');
	    $qb->join('e.medicineConfig', 'ic');
	    $qb->select('e.companyName, e.id');
	    $qb->where("ic.id = :config")->setParameter('config', $config->getId());
	    $qb->andWhere("e.customer IS NOT NULL");
	    $qb->orderBy('e.companyName', 'ASC');
	    $result = $qb->getQuery()->getArrayResult();
	    return $result;
    }

    public function updateVendorCompanyName(MedicineVendor $vendor)
    {
        $com = $vendor->getCompanyName();
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->update('AccountingBundle:AccountPurchase', 'mg')
            ->set('mg.companyName', ':companyName')
            ->where('mg.medicineVendor = :id')
            ->setParameter('companyName',$com)
            ->setParameter('id', $vendor->getId())
            ->getQuery()
            ->execute();

    }

    public function  copyToMedicineVendor($from, $to)
    {

        $em = $this->_em;
        $stock = $em->createQuery("DELETE MedicineBundle:MedicineVendor e WHERE e.medicineConfig = {$to}");
        if($stock){
            $stock->execute();
        }
        $elem = "INSERT INTO medicine_vendor (`name`, `mode`, `companyName`, `slug`, `address`, `mobile`, `email`, `status`, `medicineConfig_id`)
        SELECT `name`, `mode`, `companyName`, `slug`, `address`, `mobile`, `email`,status,$to
  FROM medicine_vendor
  WHERE medicineConfig_id =:config";
        $qb1 = $this->getEntityManager()->getConnection()->prepare($elem);
        $qb1->bindValue('config', $from);
        $qb1->execute();
    }

}
