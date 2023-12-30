<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturn;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturnItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseReturn;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseReturnItem;
use Appstore\Bundle\BusinessBundle\Entity\WearHouse;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * BusinessPurchaseItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessInvoiceReturnItemRepository extends EntityRepository
{

    protected function handleSearchBetween($qb,$data)
    {

        $grn = isset($data['grn'])? $data['grn'] :'';
        $business = isset($data['name'])? $data['name'] :'';
        $brand = isset($data['brandName'])? $data['brandName'] :'';
        $mode = isset($data['mode'])? $data['mode'] :'';
        $customer = isset($data['customer'])? $data['customer'] :'';
        $startDate = isset($data['startDate'])? $data['startDate'] :'';
        $endDate = isset($data['endDate'])? $data['endDate'] :'';

        if (!empty($grn)) {
            $qb->andWhere($qb->expr()->like("ir.invoice", "'%$grn%'"  ));
        }
        if(!empty($business)){
            $qb->andWhere($qb->expr()->like("ms.name", "'%$business%'"  ));
        }
        if(!empty($brand)){
            $qb->andWhere($qb->expr()->like("ms.brandName", "'%$brand%'"  ));
        }
        if(!empty($mode)){
            $qb->andWhere($qb->expr()->like("ms.mode", "'%$mode%'"  ));
        }
        if (!empty($customer)) {
            $qb->join('ir.customer','c');
            $qb->andWhere($qb->expr()->like("c.name", "'$customer%'"  ));
        }
        if (!empty($startDate) ) {
            $datetime = new \DateTime($data['startDate']);
            $start = $datetime->format('Y-m-d 00:00:00');
            $qb->andWhere("e.updated >= :startDate");
            $qb->setParameter('startDate', $start);
        }

        if (!empty($endDate)) {
            $datetime = new \DateTime($data['endDate']);
            $end = $datetime->format('Y-m-d 23:59:59');
            $qb->andWhere("e.updated <= :endDate");
            $qb->setParameter('endDate', $end);
        }
    }

    public function findWithSearch($config, $data = array())
    {

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.invoiceReturn','ir');
        $qb->where('ir.businessConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('ir.updated','DESC');
        $qb->getQuery();
        return  $qb;

    }


    public function getCustomerItem(BusinessConfig $config, Customer $vendor)
    {
        $configId = $config->getId();
        $vendorId = $vendor->getId();
        $qb = $this->createQueryBuilder('pi');
        $qb->select('p.id as itemId','SUM(pi.quantity) as quantity','SUM(pi.bonusQuantity) as bonusQuantity');
        $qb->join('pi.invoiceReturn','e');
        $qb->join('pi.particular','p');
        $qb->where('e.businessConfig = :config')->setParameter('config', $configId) ;
        $qb->andWhere('e.customer = :vendorId')->setParameter('vendorId', $vendorId) ;
        $qb->groupBy('p.id');
        $result = $qb->getQuery()->getArrayResult();
        $array = array();
        foreach ($result as $row){
            $array[$row['itemId']] = $row;
        }
        return  $array;
    }

    public function approveSalesReturnItem(BusinessInvoiceReturnItem $store)
    {
        $em = $this->_em;
        $store->setStatus(1);
        $em->flush();
        $mode  = strtolower($store->getItemProcess());
        if($mode == 'stock-return'){
            $arrs = array("damage-return",'stock-return');
            $returnQuantity = $this->returnSalesReturnQuantity($store->getBusinessParticular(),$arrs);
            $em->getRepository("BusinessBundle:BusinessParticular")->updateSalesReturnQuantity($store->getBusinessParticular(),$returnQuantity);
        }elseif($mode == "damage-return"){
            $em->getRepository("BusinessBundle:BusinessDistributionReturnItem")->insertSalesDamageReturnItem($store);
        }elseif($mode == "damage"){
            $em->getRepository("BusinessBundle:BusinessDamage")->insertSalesReturn($store);
        }
    }

    public function removeSalesReturnItem(BusinessInvoiceReturnItem $store)
    {
        $em = $this->_em;
        $store->setStatus(0);
        $em->flush();
        $mode  = strtolower($store->getItemProcess());
        if($mode == 'stock-return'){
            $arrs = array("damage-return",'stock-return');
            $returnQuantity = $this->returnSalesReturnQuantity($store->getBusinessParticular(),$arrs);
            $em->getRepository("BusinessBundle:BusinessParticular")->updateSalesReturnQuantity($store->getBusinessParticular(),$returnQuantity);
        }elseif($mode == "damage-return"){
            $em->getRepository("BusinessBundle:BusinessDistributionReturnItem")->deleteSalesDamageReturnItem($store);
        }elseif($mode == "damage"){
            $em->getRepository("BusinessBundle:BusinessDamage")->insertSalesReturn($store);
        }
    }

    public function returnSalesReturnQuantity(BusinessParticular $particular,$modes)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.invoiceReturn','ir');
        $qb->select('SUM(e.quantity) AS quantity','SUM(e.bonusQuantity) AS bonus');
        $qb->where('e.particular = :particular')->setParameter('particular', $particular->getId());
        $qb->andWhere('e.itemProcess IN (:modes)')->setParameter('modes', $modes);
        $qb->andWhere('ir.process = :approve')->setParameter('approve', 'Approved');
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt;

    }

    public function insertInvoiceReturnItem(BusinessInvoiceReturn $entity, $data)
    {
        $em = $this->_em;

        $itemIds = $data['itemId'];
        $quantity= $data['quantity'];
        $bonusQuantity = $data['bonusQuantity'];
        $price = $data['price'];
        $itemProcess = $data['itemProcess'];
        $wearhouse = (isset($data['wearhouse']) and $data['wearhouse']) ? $data['wearhouse'] : '';
        var_dump($wearhouse);
        foreach ($itemIds as $key  => $itemId):
            $exist = $em->getRepository('BusinessBundle:BusinessInvoiceReturnItem')->findOneBy(array('invoiceReturn'=>$entity,'businessParticular'=>$itemId));
            if($exist and $quantity[$key] > 0){
                $exist->setQuantity($quantity[$key]);
                $exist->setBonusQuantity($bonusQuantity[$key]);
                $exist->setPrice($price[$key]);
                if($wearhouse){
                    $house = $em->getRepository(WearHouse::class)->find($wearhouse[$key]);
                    $exist->setWearHouse($house);
                }
                $exist->setItemProcess($itemProcess[$key]);
                $exist->setSubTotal($price[$key] * $quantity[$key]);
                $em->persist($exist);
                $em->flush();
            }elseif($quantity[$key] > 0 ){
                $product = $em->getRepository('BusinessBundle:BusinessParticular')->find($itemId);
                $item = new BusinessInvoiceReturnItem();
                $item->setInvoiceReturn($entity);
                if($wearhouse){
                    $house = $em->getRepository(WearHouse::class)->find($wearhouse[$key]);
                    $item->setWearHouse($house);
                }
                $item->setParticular($product);
                $item->setBusinessParticular($product);
                $item->setQuantity($quantity[$key]);
                $item->setBonusQuantity($bonusQuantity[$key]);
                $item->setPrice($price[$key]);
                $item->setItemProcess($itemProcess[$key]);
                $item->setSubTotal($price[$key] * $quantity[$key]);
                $em->persist($item);
                $em->flush();
            }
        endforeach;
    }


    public function getMarketingSalesReturnProducts(GlobalOption $globalOption,$data)
    {
        $config = $globalOption->getBusinessConfig()->getId();
        $marketing = (isset($data['marketing']) and $data['marketing']) ? $data['marketing']:'';
        $customerId = (isset($data['customer']) and $data['customer']) ? $data['customer']:'';

        $qb = $this->createQueryBuilder('pi');
        $qb->select('p.id as itemId','SUM(pi.quantity) as quantity');
        $qb->join('pi.invoiceReturn','e');
        $qb->join('e.customer','c');
        $qb->join('pi.businessParticular','p');
        $qb->where('p.businessConfig = :config')->setParameter('config', $config) ;
        if($marketing){
            $qb->andWhere('c.marketing = :marketing')->setParameter('marketing', $marketing) ;
        }
        if($customerId){
            $qb->andWhere('c.id = :customer')->setParameter('customer', $customerId) ;
        }
        $qb->andWhere('e.process IN (:process)');
        $qb->setParameter('process', array('Done','Approved'));
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        if (!empty($data['startDate']) ) {
            $qb->andWhere("e.updated >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("e.updated <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
        $qb->groupBy('p.name');
        $qb->orderBy('p.name','ASC');
        $result = $qb->getQuery()->getArrayResult();
        $returns = array();
        if($result){
            foreach ($result as $row):
                $returns[$row['itemId']] = $row;
            endforeach;
        }
        return  $returns;
    }



}