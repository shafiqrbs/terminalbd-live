<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturn;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturnItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessVendorStock;
use Appstore\Bundle\BusinessBundle\Entity\BusinessVendorStockItem;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * BusinessPurchaseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessPurchaseRepository extends EntityRepository
{



	protected function handleSearchBetween($qb,$data)
	{

		$grn = isset($data['grn'])? $data['grn'] :'';
		$vendor = isset($data['vendor'])? $data['vendor'] :'';
		$business = isset($data['name'])? $data['name'] :'';
		$brand = isset($data['brandName'])? $data['brandName'] :'';
		$mode = isset($data['mode'])? $data['mode'] :'';
		$vendorId = isset($data['vendorId'])? $data['vendorId'] :'';
		$startDate = isset($data['startDate'])? $data['startDate'] :'';
		$endDate = isset($data['endDate'])? $data['endDate'] :'';

		if (!empty($grn)) {
			$qb->andWhere($qb->expr()->like("e.grn", "'%$grn%'"  ));
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
		if(!empty($vendor)){
			$qb->join('e.vendor','v');
			$qb->andWhere($qb->expr()->like("v.companyName", "'%$vendor%'"  ));
		}
		if(!empty($vendorId)){
			$qb->join('e.vendor','v');
			$qb->andWhere("v.id = :vendorId")->setParameter('vendorId', $vendorId);
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


	public function findWithSearch(User $user, $data)
	{
		$config = $user->getGlobalOption()->getBusinessConfig()->getId();
		$qb = $this->createQueryBuilder('e');
		$qb->where('e.businessConfig = :config')->setParameter('config', $config) ;
		$this->handleSearchBetween($qb,$data);
		$qb->orderBy('e.updated','DESC');
		$qb->getQuery();
		return  $qb;
	}


	public function updatePurchaseTotalPrice(BusinessPurchase $entity)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('BusinessBundle:BusinessPurchaseItem','si')
            ->select('sum(si.purchaseSubTotal) as total')
            ->where('si.businessPurchase = :entity')
            ->setParameter('entity', $entity ->getId())
            ->getQuery()->getSingleResult();

        if($total['total'] > 0){
            $subTotal = round($total['total'],2);
            $entity->setSubTotal($subTotal);
            $entity->setDiscount($this->getUpdateDiscount($entity,$subTotal));
            $entity->setNetTotal($entity->getSubTotal() - $entity->getDiscount());
            $entity->setDue($entity->getNetTotal() - $entity->getPayment() );

        }else{

            $entity->setSubTotal(0);
            $entity->setNetTotal(0);
            $entity->setDue(0);
            $entity->setDiscount(0);
        }

        $em->persist($entity);
        $em->flush();

        return $entity;

    }

    public function getUpdateDiscount(BusinessPurchase $invoice,$subTotal)
    {
        if($invoice->getDiscountType() == 'flat'){
            $discount = $invoice->getDiscountCalculation();
        }else{
            $discount = ($subTotal * $invoice->getDiscountCalculation())/100;
        }
        return $discount;
    }

	public function monthlyPurchase(User $user , $data =array())
	{

		$config =  $user->getGlobalOption()->getBusinessConfig()->getId();
		$compare = new \DateTime();
		$year =  $compare->format('Y');
		$year = isset($data['year'])? $data['year'] :$year;
		$sql = "SELECT MONTH (purchase.created) as month,SUM(purchase.netTotal) AS total
                FROM business_purchase as purchase
                WHERE purchase.businessConfig_id = :config AND purchase.process = :process  AND YEAR(purchase.updated) =:year
                GROUP BY month ORDER BY month ASC";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->bindValue('config', $config);
		$stmt->bindValue('process', 'Approved');
		$stmt->bindValue('year', $year);
		$stmt->execute();
		$result =  $stmt->fetchAll();
		return $result;


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

	public function reportPurchaseTransactionOverview(User $user , $data = array())
	{

		$global =  $user->getGlobalOption()->getId();
        if(empty($data)){
            $datetime = new \DateTime("now");
            $start = $datetime->format('Y-m-d 00:00:00');
            $end = $datetime->format('Y-m-d 23:59:59');
        }else{
            $start = date('Y-m-d',strtotime($data['startDate']));
            $end = date('Y-m-d',strtotime($data['endDate']));
        }

		$qb = $this->_em->createQueryBuilder();
		$qb->from('AccountingBundle:AccountPurchase','e');
		$qb->join('e.transactionMethod','t');
		$qb->select('t.name as transactionName , sum(e.purchaseAmount) as total ,sum(e.payment) as totalPayment');
		$qb->where('e.globalOption = :config');
		$qb->setParameter('config', $global);
		$qb->andWhere('e.process = :process');
		$qb->setParameter('process', 'approved');
		$this->handleSearchBetween($qb,$data);
		$qb->groupBy("e.transactionMethod");
		$res = $qb->getQuery();
		return $summary = $res->getArrayResult();
	}

	public function reportPurchaseVendor(User $user , $data = array())
	{

		$config =  $user->getGlobalOption()->getBusinessConfig()->getId();
        if(empty($data)){
            $datetime = new \DateTime("now");
            $start = $datetime->format('Y-m-d 00:00:00');
            $end = $datetime->format('Y-m-d 23:59:59');
        }else{
            $start = date('Y-m-d',strtotime($data['startDate']));
            $end = date('Y-m-d',strtotime($data['endDate']));
        }
        $process = "Approved";
        $sql = "SELECT sales.vendor_id as customer,c.name as customerName,c.mobile as customerMobile,c.address as customerAddress, SUM(sales.subTotal) AS subTotal, SUM(sales.netTotal) AS total, SUM(sales.discount) AS discount, SUM(sales.payment) AS receive, SUM(sales.due) AS due
                FROM business_purchase as sales
                JOIN account_vendor as c ON c.id = sales.vendor_id
                WHERE sales.businessConfig_id = :config AND  sales.process = :process AND date (sales.created) >=:start AND date(sales.created) <=:end
                GROUP BY customer ORDER BY customer ASC";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process',$process);
        $stmt->bindValue('start', $start);
        $stmt->bindValue('end', $end);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        $customerSalesSummary = array();
        foreach($result as $row) {
            $id = "{$row['customer']}";
            $customerSalesSummary[$id] = $row;
        }
       return $customerSalesSummary;

	}

	public function reportPurchaseProcessOverview(User $user,$data)
	{
		$config =  $user->getGlobalOption()->getBusinessConfig()->getId();
		$qb = $this->createQueryBuilder('s');
		$qb->select('e.process as name , sum(e.subTotal) as subTotal , sum(e.netTotal) as total ,sum(e.payment) as totalPayment , count(e.id) as totalVoucher, sum(e.due) as totalDue, sum(e.discount) as totalDiscount');
		$qb->where('e.businessConfig = :config');
		$qb->setParameter('config', $config);
		$this->handleSearchBetween($qb,$data);
		$qb->groupBy("e.process");
		$res = $qb->getQuery();
		return $result = $res->getArrayResult();
	}

	public function reportPurchaseModeOverview(User $user,$data)
	{
		$config =  $user->getGlobalOption()->getBusinessConfig()->getId();
		$qb = $this->createQueryBuilder('e');
		$qb->select('e.mode as name ,sum(e.netTotal) as total , sum(e.payment) as totalPayment ,  sum(e.due) as totalDue, sum(e.discount) as totalDiscount');
		$qb->where('e.businessConfig = :config');
		$qb->setParameter('config', $config);
		$qb->andWhere('e.process = :process');
		$qb->setParameter('process', 'Approved');
		$this->handleSearchBetween($qb,$data);
		$qb->groupBy("e.mode");
		$res = $qb->getQuery();
		return $result = $res->getArrayResult();
	}



	public function purchaseVendorReport(User $user , $data = array())
	{

		$global =  $user->getGlobalOption()->getId();
		$qb = $this->_em->createQueryBuilder();
		$qb->from('AccountingBundle:AccountPurchase','e');
		$qb->join('e.accountVendor','t');
		$qb->select('t.companyName as companyName ,t.name as vendorName ,t.mobile as vendorMobile , sum(e.purchaseAmount) as total ,sum(e.payment) as payment');
		$qb->where('e.globalOption = :config');
		$qb->setParameter('config', $global);
		$qb->andWhere('e.process = :process');
		$qb->setParameter('process', 'approved');
		$this->handleSearchBetween($qb,$data);
		$qb->groupBy("e.accountVendor");
		$qb->orderBy("t.companyName",'ASC');
		$res = $qb->getQuery();
		return $result = $res->getArrayResult();

	}

	public function insertCommissionPurchase(BusinessInvoice $invoice)
    {

        $em = $this->_em;
        $config = $invoice->getBusinessConfig();

        /* @var $rows BusinessInvoiceParticular */

        foreach ($invoice->getBusinessInvoiceParticulars() as $rows){
            if($rows->getVendor()){
                $em->getRepository('BusinessBundle:BusinessVendorStockItem')->insertStockSalesItems($rows);
                /*$exist = $this->checkInstantPurchaseToday($rows->getVendor());
                if($exist['status'] == 'valid'){
                    $purchase = $em->getRepository('BusinessBundle:BusinessPurchase')->find($exist['purchase']);
                }else{
                    $purchase = $this->purchaseCommissionInvoiceGenerate($invoice,$rows->getVendor());
                }
                $particularId   = $rows->getBusinessParticular()->getId();
                $price          = $rows->getPurchasePrice();
                $quantity       = $rows->getTotalQuantity();
                if($invoice->getBusinessConfig()->getBusinessModel() == 'commission'){
                    $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price);
                    $em->getRepository('BusinessBundle:BusinessPurchaseItem')->insertPurchaseItems($purchase,$invoiceItems);
                    $em->getRepository('BusinessBundle:BusinessVendorStockItem')->insertStockSalesItems($rows);
                }
                $em->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($purchase);*/
            }

        }

    }

    public function insertVendorStockLotPurchase(BusinessVendorStock $vendorStock)
    {

        $em = $this->_em;
        $config = $vendorStock->getBusinessConfig();

        /* @var $rows BusinessInvoiceParticular */

        $em = $this->_em;
        $entity = new BusinessPurchase();
        $entity->setBusinessConfig($config);
        $entity->setVendor($vendorStock->getVendor());
        $entity->setProcess('Commission');
        $entity->setCommissionInvoice(true);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $em->persist($entity);
        $em->flush();

        /* @var $item BusinessVendorStockItem */
        /* @var $row BusinessInvoiceParticular */

        foreach ($vendorStock->getBusinessVendorStockItems() as $item){
            foreach ($item->getBusinessInvoiceParticulars() as $row){

                if($row->getVendor()){
                    $price = ($row->getPrice() - $vendorStock->getCommission());
                    $invoiceItems = array('particularId' => $row->getBusinessParticular()->getId() , 'quantity' => $row->getQuantity(),'price' => $price);
                    $em->getRepository('BusinessBundle:BusinessPurchaseItem')->insertPurchaseItems($entity,$invoiceItems);

                }
            }
        }
        $em->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($entity);

    }

    public function purchaseCommissionInvoiceGenerate(BusinessInvoice $invoice  , AccountVendor $vendor)
    {
        $em = $this->_em;
        $entity = new BusinessPurchase();
        $entity->setBusinessConfig($invoice->getBusinessConfig());
        $entity->setVendor($vendor);
        $entity->setCreatedBy($invoice->getCreatedBy());
        $entity->setReceiveDate($invoice->getUpdated());
        $entity->setProcess('Commission');
        $entity->setCommissionInvoice(true);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    public function checkInstantPurchaseToday(AccountVendor $vendor)
    {
        $compare = new \DateTime();
        $today =  $compare->format('Y-m-d');
        $sql = "SELECT id
                FROM business_purchase as purchase
                WHERE purchase.vendor_id = :vendor AND purchase.process = :process  AND DATE (purchase.created) =:receive";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('vendor', $vendor->getId());
        $stmt->bindValue('process', 'Commission');
        $stmt->bindValue('receive', $today);
        $stmt->execute();
        $result =  $stmt->fetch();
        if(!empty($result['id'])){
            return $data = array('purchase' => $result['id'],'status'=>'valid');
        }else{
            return $data = array('status'=>'in-valid');
        }

    }

    public function checkInstantPurchaseReverse($config)
    {
        $compare = new \DateTime();
        $today =  $compare->format('Y-m-d');
        $em = $this->_em;
        $purchase = $em->createQuery("DELETE BusinessBundle:BusinessPurchase e WHERE e.businessConfig = {$config} AND e.process = 'Commission' AND e.created LIKE '%{$today}%' ");
        $purchase->execute();
    }

    public function insertPurchaseReturn(BusinessInvoiceReturn $return)
    {
        $em = $this->_em;
        if($return->getInvoiceReturnItems()){
            $entity = new BusinessPurchase();
            $entity->setBusinessConfig($return->getBusinessConfig());
            $vendor = $em->getRepository('AccountingBundle:AccountVendor')->insertSalesReturnVendor($return->getBusinessConfig()->getGlobalOption());
            $entity->setBusinessConfig($return->getBusinessConfig());
            $entity->setVendor($vendor);
            $entity->setMode('Sales-Return');
            $entity->setMemo($return->getInvoice());
            $method = $em->getRepository('SettingToolBundle:TransactionMethod')->findOneBy(array('slug'=>'cash'));
            $entity->setTransactionMethod($method);
            $entity->setSubTotal($return->getPayment());
            $entity->setNetTotal($return->getPayment());
            $entity->setPayment($return->getPayment());
            $entity->setCreatedBy($return->getCreatedBy());
            $entity->setApprovedBy($return->getCreatedBy());
            $entity->setProcess("Approved");
            $em->persist($entity);
            $em->flush();
            return $entity;

        }
        return false;


    }



}