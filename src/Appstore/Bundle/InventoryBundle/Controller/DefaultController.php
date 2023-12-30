<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

	public function indexAction()
    {
	    /* @var GlobalOption $globalOption */

	    $globalOption = $this->getUser()->getGlobalOption();
	    $em = $this->getDoctrine()->getManager();
	    $data = $_REQUEST;
	    $datetime = new \DateTime("now");
	    $data['startDate'] = $datetime->format('Y-m-d');
	    $data['endDate'] = $datetime->format('Y-m-d');

	    $user = $this->getUser();
	    $salesCashOverview = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->reportSalesOverview($user,$data);
	    $purchaseCashOverview = $this->getDoctrine()->getRepository('InventoryBundle:Purchase')->reportPurchaseOverview($user,$data);
	    $transactionMethods = array(1);
	    $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
	    $expenditureOverview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
	    $salesUserReport = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->salesUserReport($user,array('startDate'=>$data['startDate'],'endDate'=>$data['endDate']));
	    //   $userEntities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,$data);
	    $startMonthDate = $datetime->format('Y-m-01 00:00:00');
	    $endMonthDate = $datetime->format('Y-m-t 23:59:59');
	    $medicineSalesMonthly = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->inventorySalesMonthly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	    $medicinePurchaseMonthly = $this->getDoctrine()->getRepository('InventoryBundle:Purchase')->inventoryPurchaseMonthly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	    $shortMedicineCount = $this->getDoctrine()->getRepository('InventoryBundle:Item')->inventoryShortListCount($user);

	    $employees = $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());
	    $entities = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->currentMonthSales($user,$data);
	    $userSalesAmount = array();
	    foreach($entities as $row) {
		    $userSalesAmount[$row['salesBy'].$row['month']] = $row['total'];
	    }

	    $medicineSalesMonthlyArr = array();
	    foreach($medicineSalesMonthly as $row) {
		    $medicineSalesMonthlyArr[$row['month']] = $row['total'];
	    }
	    $medicinePurchaseMonthlyArr = array();
	    foreach($medicinePurchaseMonthly as $row) {
		    $medicinePurchaseMonthlyArr[$row['month']] = $row['total'];
	    }

	    return $this->render('InventoryBundle:Default:index.html.twig', array(

	    	'option'                    => $user->getGlobalOption() ,
		    'globalOption'              => $globalOption,
		    'transactionCashOverviews'  => $transactionCashOverview,
		    'expenditureOverview'       => $expenditureOverview ,
		    'salesCashOverview'         => $salesCashOverview ,
		    'purchaseCashOverview'      => $purchaseCashOverview ,
		    'medicineSalesMonthly'      => $medicineSalesMonthlyArr ,
		    'medicinePurchaseMonthly'   => $medicinePurchaseMonthlyArr ,
		    'salesUserReport'           => $salesUserReport ,
		    'userSalesAmount'           => $userSalesAmount ,
		    'employees'                 => $employees ,
		    'shortMedicineCount'        => $shortMedicineCount ,
		    'searchForm'                => $data ,

	    ));

    }

	public function stockResetAction()
	{
		set_time_limit(0);
		ignore_user_abort(true);
		$em = $this->getDoctrine()->getManager();
		$inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
		$items = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->findItemWithPurchaseQuantity($inventory);
		foreach ($items as $row){
			$item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->find($row['itemId']);
			$salesQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'sales');
			$salesReturnQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'salesReturn');
			$purchaseReturnQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'purchaseReturn');
			$damageQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'damage');
			$item->setPurchaseQuantity($row['quantity']);
			$item->setSalesQuantity($salesQnt);
			$item->setSalesQuantityReturn($salesReturnQnt);
			$item->setPurchaseQuantityReturn($purchaseReturnQnt);
			$item->setPurchaseQuantityReturn($damageQnt);
			$remainingQnt = ($item->getPurchaseQuantity() + $item->getSalesQuantityReturn()) - ($item->getSalesQuantity() + $item->getPurchaseQuantityReturn()+$item->getDamageQuantity());
			$item->setRemainingQnt($remainingQnt);
			$em->flush();
		}
		return $this->redirect($this->generateUrl('homepage'));
	}


    public function salesResetAction()
    {

	    set_time_limit(0);
	    ignore_user_abort(true);
    	$em = $this->getDoctrine()->getManager();
	    $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
		$this->getDoctrine()->getRepository('AccountingBundle:Transaction')->removeTransaction($this->getUser()->getGlobalOption(),'Sales');
	    $entities = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findBy(array('inventoryConfig'=> $config, 'process'=>'Done'));

	    foreach ($entities as $entity){

	    	$purchaseAmount = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->getItemPurchasePrice($entity);
		    $entity->setPurchasePrice($purchaseAmount);
		    $profit = ( $entity->getTotal()-($entity->getVat() + $purchaseAmount));
		    $entity->setProfit($profit);
		    $entity->setDue($entity->getTotal() - $entity->getPayment());
		    $entity->setUpdated($entity->getCreated());
		    if(empty($entity->getPayment())){
		    	 $entity->setTransactionMethod(NULL);
		    }
		    $em->flush();
		    $accountSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->resetAccountSales($entity);
		    $em->getRepository('AccountingBundle:Transaction')->resetSalesTransaction($this->getUser()->getGlobalOption() , $entity, $accountSales);
	    }
	    exit;
	    return $this->redirect($this->generateUrl('homepage'));
    }

    public function copyToItemStockAction()
    {

        set_time_limit(0);
        ignore_user_abort(true);
        $option = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->find($_REQUEST['option']);
        $config = $option->getMedicineConfig();
        if($option and !empty($config)) {
            $fromConfig = $config->getId();
            $toConfig = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
            $this->getDoctrine()->getRepository('InventoryBundle:Item')->processStockMigration($fromConfig,$toConfig);
            return new Response('success');
        }
        return new Response('failed');
    }


    public function copyStockToEcommerceAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $option = $this->getUser()->getGlobalOption();
        return new Response('success');

    }


    public function categoryDeleteAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();
        $StockItem = $em->createQuery('DELETE ProductProductBundle:Category e WHERE e.inventoryConfig = '.$config);
        $StockItem->execute();
        return $this->redirect($this->generateUrl('item'));
    }


     public function itemToecommerceAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $option = $this->getUser()->getGlobalOption();
        return new Response('success');

    }


    public function masterItemDeleteAction()
    {
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();
        $em = $this->getDoctrine()->getManager();
        $StockItem = $em->createQuery('DELETE InventoryBundle:Product e WHERE e.inventoryConfig = '.$config);
        $StockItem->execute();
        return $this->redirect($this->generateUrl('item'));
    }
    public function stockItemDeleteAction()
    {
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();
        $em = $this->getDoctrine()->getManager();
        $StockItem = $em->createQuery('DELETE InventoryBundle:Item e WHERE e.inventoryConfig = '.$config);
        $StockItem->execute();
        return $this->redirect($this->generateUrl('item'));

    }

}
