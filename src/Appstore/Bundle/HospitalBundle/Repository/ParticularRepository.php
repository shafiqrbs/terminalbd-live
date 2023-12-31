<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Appstore\Bundle\HospitalBundle\Entity\AdmissionPatientParticular;
use Appstore\Bundle\HospitalBundle\Entity\HmsDoctorVisitMode;
use Appstore\Bundle\HospitalBundle\Entity\HmsPurchase;
use Appstore\Bundle\HospitalBundle\Entity\HmsPurchaseItem;
use Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup;
use Appstore\Bundle\HospitalBundle\Entity\HmsStockOut;
use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Doctrine\ORM\EntityRepository;


/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParticularRepository extends EntityRepository
{
    public function findWithSearch($config, $service, $data = "")
    {

        $name = isset($data['name']) ? $data['name'] : '';
        $category = isset($data['category']) ? $data['category'] : '';
        $department = isset($data['department']) ? $data['department'] : '';

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.hospitalConfig = :config')->setParameter('config', $config);
        $qb->andWhere('e.service = :service')->setParameter('service', $service);
        if (!empty($name)) {
            $qb->andWhere('e.name LIKE :searchTerm OR e.particularCode LIKE :searchTerm');
            $qb->setParameter('searchTerm', '%' . trim($name) . '%');
        }
        if (!empty($category)) {
            $qb->andWhere("e.category = :category");
            $qb->setParameter('category', $category);
        }
        if (!empty($department)) {
            $qb->andWhere("e.department = :department");
            $qb->setParameter('department', $department);
        }
        $qb->orderBy('e.name', 'ASC');
        $qb->getQuery();
        return $qb;
    }

    public function getFindWithParticular($hospital, $services)
    {

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service', 's')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.mobile')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e.hospitalConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:service)')
            ->setParameter('service', array_values($services))
            ->orderBy('e.service', 'ASC')
            ->orderBy('e.name', 'ASC')
            ->getQuery()->getArrayResult();
        return $qb;
    }

    public function getServices($hospital, $services)
    {
        $particulars = $this->getServiceWithParticular($hospital, $services);
        $data = '';
        $service = '';
        foreach ($particulars as $particular) {
            if ($service != $particular['serviceName']) {
                if ($service != '') {
                    $data .= '</optgroup>';
                }
                $data .= '<optgroup label="' . ucfirst($particular['serviceName']) . '">';
            }
            if ($particular['serviceCode'] != '04') {
                $data .= '<option value="/hms/invoice/' . $particular['id'] . '/particular-search">' . $particular['particularCode'] . ' - ' . htmlspecialchars(ucfirst($particular['name'])) . ' - Tk. ' . $particular['price'] . ' to ' . $particular['minimumPrice'] . '</option>';
            } else {
                $data .= '<option value="/hms/invoice/' . $particular['id'] . '/particular-search">' . $particular['particularCode'] . ' - ' . htmlspecialchars(ucfirst($particular['name'])) . ' - Tk. ' . $particular['price'] . '</option>';
            }
            $service = $particular['serviceName'];
        }
        if ($service != '') {
            $data .= '</optgroup>';
        }
        return $data;

    }


    public function getServiceWithParticular($hospital, $services)
    {

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service', 's')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e.hospitalConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:service)')
            ->setParameter('service', array_values($services))
            ->orderBy('e.service', 'ASC')
            //  ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
        return $qb;
    }

    public function getParticulars($hospital, $services)
    {

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service', 's')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode as code')
            ->where('e.hospitalConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:service)')
            ->setParameter('service', array_values($services))
            ->orderBy('e.name', 'ASC');
        $results = $qb->getQuery()->getArrayResult();
        $selects = "";
        foreach ($results as $row) {
            $selects .= "<option value='{$row['id']}'>{$row['code']}-{$row['name']}</option>";
        }
        return $selects;

    }

    public function getMedicineParticular($hospital)
    {

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service', 's')
            ->leftJoin('e.unit', 'u')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('e.status')
            ->addSelect('e.salesQuantity')
            ->addSelect('e.minQuantity')
            ->addSelect('e.openingQuantity')
            ->addSelect('u.name as unit')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->addSelect('e.purchasePrice')
            ->addSelect('e.purchaseQuantity')
            ->where('e.hospitalConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:process)')
            ->setParameter('process', array_values(array(4)))
            ->orderBy('e.name', 'ASC')
            ->getQuery()->getArrayResult();
        return $qb;
    }

    public function getAccessoriesParticular($hospital)
    {

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service', 's')
            ->leftJoin('e.unit', 'u')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('e.status')
            ->addSelect('e.salesQuantity')
            ->addSelect('e.minQuantity')
            ->addSelect('e.openingQuantity')
            ->addSelect('u.name as unit')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->addSelect('e.purchasePrice')
            ->addSelect('e.purchaseQuantity')
            ->where('e.hospitalConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:process)')
            ->setParameter('process', array_values(array(8)))
            ->orderBy('e.name', 'ASC')
            ->getQuery()->getArrayResult();
        return $qb;
    }

    public function findHmsExistingCustomer($hospital, $mobile, $data)
    {
        $em = $this->_em;
        $name = $data['referredDoctor']['name'];
        $doctorSignature = $data['referredDoctor']['doctorSignature'];
        $isDoctor = isset($data['referredDoctor']['isDoctor']) ? 1 : 0;
        $entity = $em->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital, 'service' => 6, 'mobile' => $mobile));
        if ($entity) {
            return $entity;
        } else {
            $entity = new Particular();
            $entity->setService($em->getRepository('HospitalBundle:Service')->find(6));
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setDoctorSignature($doctorSignature);
            $entity->setIsDoctor($isDoctor);
            $entity->setHospitalConfig($hospital);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }

    }

    public function findHmsExistingReferred($hospital, $mobile, $data)
    {
        $em = $this->_em;
        $name = $data['referredDoctor']['name'];
        $doctorSignature = $data['referredDoctor']['doctorSignature'];
        $isDoctor = isset($data['referredDoctor']['isDoctor']) ? 1 : 0;
        $entity = $em->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital, 'service' => 6, 'mobile' => $mobile));
        if ($entity) {
            return $entity;
        } else {
            $entity = new Particular();
            $entity->setService($em->getRepository('HospitalBundle:Service')->find(6));
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setDoctorSignature($doctorSignature);
            $entity->setIsDoctor($isDoctor);
            $entity->setHospitalConfig($hospital);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }

    }

    public function findHmsExistingDoctor($hospital, $mobile, $data)
    {
        $em = $this->_em;
        $name = $data['assignDoctor']['name'];
        $doctorSignature = $data['assignDoctor']['doctorSignature'];
        $doctorSignatureBangla = $data['assignDoctor']['doctorSignatureBangla'];
        $specialist = $data['assignDoctor']['specialist'];
        $account = isset($data['assignDoctor']['sendToAccount']) ? 1 : 0;
        $entity = $em->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital, 'service' => 5, 'name' => $name));
        if ($entity) {
            return $entity;
        } else {
            $entity = new Particular();
            $entity->setService($em->getRepository('HospitalBundle:Service')->find(5));
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setDoctorSignature($doctorSignature);
            $entity->setDoctorSignatureBangla($doctorSignatureBangla);
            $entity->setSpecialist($specialist);
            $entity->setIsDoctor(1);
            $entity->setSendToAccount($account);
            $entity->setHospitalConfig($hospital);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }

    }

    public function getPurchaseUpdateQnt(HmsPurchase $purchase)
    {

        $em = $this->_em;
        /** @var HmsPurchaseItem $purchaseItem */
        foreach ($purchase->getPurchaseItems() as $purchaseItem) {
            /** @var Particular $particular */
            $particular = $purchaseItem->getParticular();
            $qnt = ($particular->getPurchaseQuantity() + $purchaseItem->getQuantity());
            $particular->setPurchaseQuantity($qnt);
            $em->persist($particular);
            $em->flush();

        }
    }

    public function insertAccessories(Invoice $invoice)
    {
        $em = $this->_em;
        /** @var InvoiceParticular $item */
        if (!empty($invoice->getInvoiceParticulars())) {
            foreach ($invoice->getInvoiceParticulars() as $item) {
                /** @var Particular $particular */
                $particular = $item->getParticular();
                if ($particular->getService()->getId() == 4) {
                    $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                    $particular->setSalesQuantity($qnt);
                    $em->persist($particular);
                    $em->flush();
                }
            }
        }
    }

    public function insertIssueItem(HmsStockOut $invoice)
    {

        $em = $this->_em;
        /** @var InvoiceParticular $item */
        if (!empty($invoice->getStockOutItems())) {
            foreach ($invoice->getStockOutItems() as $item) {
                /** @var Particular $particular */
                $particular = $item->getParticular();
                $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                $particular->setSalesQuantity($qnt);
                $em->persist($particular);
                $em->flush();
            }
        }
    }

    public function getSalesUpdateQnt(Invoice $invoice)
    {

        $em = $this->_em;

        /** @var InvoiceParticular $item */

        foreach ($invoice->getInvoiceParticulars() as $item) {

            /** @var Particular $particular */

            $particular = $item->getParticular();
            if ($particular->getService()->getId() == 4) {

                $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                $particular->setSalesQuantity($qnt);
                $em->persist($particular);
                $em->flush();
            }
        }
    }

    public function admittedPatientAccessories(InvoiceTransaction $transaction)
    {

        $em = $this->_em;

        /** @var InvoiceParticular $item */
        if (!empty($transaction->getAdmissionPatientParticulars())) {

            foreach ($transaction->getAdmissionPatientParticulars() as $item) {

                /** @var Particular $particular */

                $particular = $item->getParticular();
                if ($particular->getService()->getId() == 4) {
                    $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                    $particular->setSalesQuantity($qnt);
                    $em->persist($particular);
                    $em->flush();
                }
            }
        }

    }

    public function groupServiceBy()
    {

        $pass2 = array();
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.hospitalConfig = :config')->setParameter('config', 1);
        $qb->andWhere('e.service IN(:service)')
            ->setParameter('service', array_values(array(1, 2, 3, 4)));
        $qb->orderBy('e.name', 'ASC');
        $data = $qb->getQuery()->getResult();

        foreach ($data as $parent => $children) {

            foreach ($children as $child => $none) {
                $pass2[$parent][$child] = true;
                $pass2[$child][$parent] = true;
            }
        }

    }

    public function processStockMigration($from, $to)
    {

        $em = $this->_em;
        $stock = $em->createQuery("DELETE HospitalBundle:Particular e WHERE e.hospitalConfig={$to}");
        if ($stock) {
            $stock->execute();
        }
        $elem = "INSERT INTO hms_particular(`name`,`sepcimen`,`department_id`,`category_id`,`testDuration`, `reportFormat`,`discountValid`,`room`,`instruction`,`minimumPrice`,`commission`,`overHeadCost`,price,status,`hospitalConfig_id`)
  SELECT `name`,`sepcimen`,'department_id',`category_id`, `testDuration`, `reportFormat`, `discountValid`, `room`, `instruction`, `minimumPrice`, `commission`, `overHeadCost`,price,1,$to
  FROM medicine_stock
  WHERE hospitalConfig_id    =:config";
        $qb1 = $this->getEntityManager()->getConnection()->prepare($elem);
        $qb1->bindValue('config', $from);
        $qb1->execute();

    }

    public function getCurrentCabins($config, $service, $cabins)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id as id', 'e.name as name');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $config);
        $qb->andWhere('e.service = :service')->setParameter('service', $service);
        if ($cabins) {
            $qb->andWhere('e.id NOT IN (:cabins)')->setParameter('cabins', $cabins);
        }
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function getParticularIdName($config, $services)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id as id', 'e.name as name', 'e.particularCode as particularCode');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $config);
        $qb->andWhere('e.status = 1');
        $qb->andWhere('e.service IN (:service)')->setParameter('service', $services);
        $qb->orderBy('e.name', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function getMarketingExecutiveName($config, $services)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id as id', 'mu.username as name', 'e.particularCode as particularCode');
        $qb->join('e.marketingExecutive','mu');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $config);
        $qb->andWhere('e.status = 1');
        $qb->andWhere('e.service IN (:service)')->setParameter('service', $services);
        $qb->orderBy('e.name', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function insertDoctorVisitModes(Particular $doctor, $data)
    {

        $em = $this->_em;
        if (!empty($data['visitMod'])) {
            foreach ($data['visitMod'] as $key => $item) {
                $entity = new HmsDoctorVisitMode();
                $particular = $em->getRepository(HmsServiceGroup::class)->find($key);
                $entity->setDoctor($doctor);
                $entity->setService($particular);
                $entity->setAmount($item);
                $em->persist($entity);
                $em->flush();
            }
        }
    }

    public function updateDoctorVisitModes(Particular $doctor, $data)
    {

        $em = $this->_em;
        if (!empty($data['visitMod'])) {
            foreach ($data['visitMod'] as $key => $item) {
                $entity = $em->getRepository(HmsDoctorVisitMode::class)->findOneBy(array('doctor' => $doctor->getId(), 'service' => $key));
                if ($entity) {
                    $entity->setAmount($item);
                    $em->persist($entity);
                    $em->flush();
                } else {
                    $this->insertDoctorVisitModes($doctor, $data);
                }

            }
        }
    }


    public function searchParticularAutoComplete($q, HospitalConfig $config)
    {
        $query = $this->createQueryBuilder('e');
        $query->select("e.id as id");
        $query->addSelect("CONCAT(e.particularCode,' - ',e.name,' => TK. ',e.price) as text");
        $query->where("e.hospitalConfig = :config")->setParameter('config', $config->getId());
        $query->andWhere('e.particularCode LIKE :searchTerm OR e.name LIKE :searchTerm');
        $query->setParameter('searchTerm', '%' . trim($q) . '%');
        $query->andWhere('e.status =1');
        $query->andWhere('e.service IN (:services)')->setParameter('services', array(1));
        $query->orderBy('e.name', 'DESC');
        $query->setMaxResults('200');
        return $query->getQuery()->getResult();

    }

    public function searchDoctorReferredAutoComplete($q, HospitalConfig $config, $services)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.service','s');
        $query->select("e.id as id");
        $query->addSelect("CASE WHEN (e.mobile IS NOT NULL AND s.slug = 'refrerred' ) THEN CONCAT(e.particularCode,' - ',e.name,' (',e.mobile,')') ELSE CONCAT(e.particularCode,' - ',e.name) END  as text");
        $query->where("e.hospitalConfig = :config")->setParameter('config', $config->getId());
        $query->andWhere('e.particularCode LIKE :searchTerm OR e.name LIKE :searchTerm OR e.mobile LIKE :searchTerm');
        $query->setParameter('searchTerm', '%' . trim($q) . '%');
        $query->andWhere('e.status =1');
        $query->andWhere('e.service IN (:services)')->setParameter('services', $services);
        $query->orderBy('e.name', 'DESC');
        $query->setMaxResults('200');
        return $query->getQuery()->getResult();

    }

}



