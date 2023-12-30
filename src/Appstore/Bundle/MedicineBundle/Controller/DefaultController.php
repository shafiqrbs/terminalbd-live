<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineParticular;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Dompdf\Dompdf;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

	/**
	 * Lists all HotelCategory entities.
	 *
	 * @Secure(roles="ROLE_MEDICINE,ROLE_DOMAIN");
	 *
	 */

	public function indexAction()
    {

        /* @var GlobalOption $globalOption */

        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $datetime = new \DateTime("now");
        $data['startDate'] = $datetime->format('Y-m-d');
	    $data['endDate'] = $datetime->format('Y-m-d');
	    $income = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMedicineIncome($this->getUser(),$data);
	    $user = $this->getUser();
	    $salesCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user,$data);
        $purchaseCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user,$data);
	    $transactionMethods = array(1);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashMedicineOverview( $this->getUser(),$transactionMethods,$data);
	    $expenditureOverview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
	    $salesUserReport = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,array('startDate'=>$data['startDate'],'endDate'=>$data['endDate']));
	 //   $userEntities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,$data);
	    $startMonthDate = $datetime->format('Y-m-01 00:00:00');
	    $endMonthDate = $datetime->format('Y-m-t 23:59:59');
	    $medicineSalesDaily = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->medicineSalesDaily($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
        $medicineSalesHourly = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->medicineSalesHourly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
        $shortMedicineCount = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findMedicineShortListCount($user);
       // $shortMedicineCount = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->findCurrentShortListCount($user);
	   // $expiryMedicineCount = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->expiryMedicineCount($user);
	    //   $purchaseUserReport = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	    //  $userSalesPurchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));

	    $employees = $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());

	    $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->currentMonthSales($user,$data);
	    $userSalesAmount = array();
	    foreach($entities as $row) {
		    $userSalesAmount[$row['salesBy'].$row['month']] = $row['total'];
	    }
	    return $this->render('MedicineBundle:Default:index.html.twig', array(
            'option'                    => $user->getGlobalOption() ,
            'globalOption'              => $globalOption,
            'income'                    => $income,
            'transactionCashOverviews'  => $transactionCashOverview,
            'expenditureOverview'       => $expenditureOverview ,
            'salesCashOverview'         => $salesCashOverview ,
            'purchaseCashOverview'      => $purchaseCashOverview ,
            'salesUserReport'           => $salesUserReport ,
            'userSalesAmount'           => $userSalesAmount ,
            'employees'                 => $employees ,
            'shortMedicineCount'        => $shortMedicineCount ,
            'expiryMedicineCount'       => '' ,
            'medicineSalesDaily'        => $medicineSalesDaily ,
            'medicineSalesHourly'       => $medicineSalesHourly ,
            'searchForm'                => $data ,
        ));

    }

    public function updateExpirationMedicineAction()
    {
	    $em = $this->getDoctrine()->getManager();
    	$globalOption = $this->getUser()->getGlobalOption();
	    set_time_limit(0);
	    ignore_user_abort(true);
	    $config = $globalOption->getMedicineConfig();
	    $data = array('endDate'=>'2018-10-31');
	    $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->findWithSearch($config,$data);

	    /* @var $entity MedicinePurchase */
	    /* @var $item MedicinePurchaseItem */

	    foreach ($entity->getQuery()->getResult() as $entity){
	    	foreach ($entity->getMedicinePurchaseItems() as $item){
			    $item->setRemainingQuantity(0);
			    $em->flush();
		    }
	    }
	    return $this->redirect($this->generateUrl('homepage'));
	  }

	public function copyToMedicineParticularAction()
	  {
          set_time_limit(0);
          ignore_user_abort(true);

          $option = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->find($_REQUEST['option']);
          $config = $option->getMedicineConfig();
          if($option and !empty($option->getMedicineConfig())){

              $em = $this->getDoctrine()->getManager();
              $existConfig = $this->getUser()->getGlobalOption()->getMedicineConfig();
              $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config));

              /* @var $entity MedicineParticular */

              foreach ($entities as $entity){

                  $newEntity = new MedicineParticular();
                  $newEntity->setMedicineConfig($existConfig);
                  $newEntity->setName($entity->getName());
                  $newEntity->setStatus(1);
                  $newEntity->setParticularType($entity->getParticularType());
                  $newEntity->setSlug($entity->getSlug());
                  $em->persist($newEntity);
                  $em->flush();
              }
              return new Response('success');
          }
          return new Response('failed');
	  }

	public function copyToMedicineStockAction()
	{

		set_time_limit(0);
		ignore_user_abort(true);
        $option = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->find($_REQUEST['option']);
        $config = $option->getMedicineConfig();
        if($option and !empty($config)) {
            $fromConfig = $config->getId();
            $toConfig = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->processStockMigration($fromConfig,$toConfig);
            return new Response('success');
        }
        return new Response('failed');
	}

    public function copyToMedicineVendorAction()
    {

        set_time_limit(0);
        ignore_user_abort(true);
        $option = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->find($_REQUEST['option']);
        $config = $option->getMedicineConfig();
        if($option and !empty($config)) {
            $fromConfig = $config->getId();
            $toConfig = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->copyToMedicineVendor($fromConfig,$toConfig);
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

    public function vendorCustomerAccountAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $vendorResults = '';
        $customerResults = '';
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $vendors = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->listForVendorCustomer($config);
        if(!empty($data) and !empty($data['startDate']) and !empty($data['endDate'])){
            $vendor = array('vendor' => $data['vendor'],'startDate'=>$data['startDate'],'endDate'=>$data['endDate']);
            $vendorLedger = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($this->getUser()->getGlobalOption(),$vendor);
            $vendorResults = $vendorLedger->getResult();
        }
        if(!empty($data) and !empty($data['startDate']) and !empty($data['endDate'])){
            $customer = array('customer' => $data['vendor'],'startDate'=>$data['startDate'],'endDate'=>$data['endDate']);
            $customerLedger = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->findWithSearch($this->getUser(),$customer);
            $customerResults = $customerLedger->getResult();
        }
        return $this->render('MedicineBundle:PurchaseSales:vendorPurchaseSalesLedger.html.twig', array(
            'vendors' => $vendors,
            'vendorLedger' => $vendorResults,
            'customerLedger' => $customerResults,
            'searchForm'            => $data,
            'entity' => '',
        ));
    }

    public function vendorCustomerMedicineAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $vendorResults = '';
        $customerResults = '';
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $vendors = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->listForVendorCustomer($config);
        if(!empty($data) and !empty($data['startDate'])){

            $end = $data['endDate'];
            $date = strtotime($data['startDate']);
            $date = strtotime("+$end day", $date);
            $endDate = date('Y-m-d', $date);
            $vendor = array('vendor' => $data['vendor'],'startDate'=> $data['startDate'],'endDate'=> $endDate);
            $vendorLedger = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->stockLedger($config,$vendor);
            $vendorResults = $vendorLedger->getQuery()->getResult();
        }

        if(!empty($data) and !empty($data['startDate'])){

            $end = $data['endDate'];
            $date = strtotime($data['startDate']);
            $date = strtotime("+$end day", $date);
            $endDate = date('Y-m-d', $date);
            $customer = array('customer' => $data['vendor'],'startDate'=>$data['startDate'],'endDate' => $endDate);
            $customerLedger = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->salesItemLists($this->getUser(),$customer);
            $customerResults = $customerLedger->getQuery()->getResult();
        }

        return $this->render('MedicineBundle:PurchaseSales:vendorPurchaseSalesItem.html.twig', array(
            'vendors' => $vendors,
            'vendorLedger' => $vendorResults,
            'customerLedger' => $customerResults,
            'searchForm'            => $data,
            'entity' => '',
        ));
    }

    public function updateRemainingQuantityAction(){

        set_time_limit(0);
        ignore_user_abort(true);
	    $config = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
	    $this->getDoctrine()->getRepository("MedicineBundle:MedicineStock")->updateRemainingQuantityReset($config);
	    return $this->redirect($this->generateUrl('medicine_stock'));
	}

    public function monthlyStockLedgerAction(){

        set_time_limit(0);
        ignore_user_abort(true);
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
        $this->getDoctrine()->getRepository("MedicineBundle:MedicineStockReport")->monthlyStockLedgerReport($config);
        return $this->redirect($this->generateUrl('medicine_stock'));
    }

    public function yearlyStockLedgerAction(){

        set_time_limit(0);
        ignore_user_abort(true);
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
        $this->getDoctrine()->getRepository("MedicineBundle:MedicineStockReport")->yearlyStockLedgerReport($config);
        return $this->redirect($this->generateUrl('medicine_stock'));
    }

    public function vendorPaymentReceiveAction($vendor)
    {
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getMedicineConfig();
        $data = $_REQUEST;

        $em = $this->getDoctrine()->getManager();
        $amount = $data['amount'];
        $remark = $data['remark'];
        $vendor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->findOneBy(array('medicineConfig'=>$config,'companyName'=>$vendor));
        $method = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        if($data['mode'] == 'receive'){
            $sales = new  AccountSales();
            $sales->setGlobalOption($user->getGlobalOption());
            $sales->setProcessHead('medicine');
            $sales->setCustomer($vendor->getCustomer());
            $sales->setAmount($amount);
            $sales->setProcessType('Due');
            $sales->setTransactionMethod($method);
            $sales->setProcess('approved');
            $sales->setRemark($remark);
            $em->persist($sales);
            $em->flush();
            $em->getRepository('AccountingBundle:AccountSales')->updateCustomerBalance($sales);
            return new Response('success');

        }elseif($data['mode'] == 'payment'){

            $purchase = new  AccountPurchase();
            $purchase->setGlobalOption($user->getGlobalOption());
            $purchase->setProcessHead('medicine');
            $purchase->setCompanyName($vendor->getCompanyName());
            $purchase->setMedicineVendor($vendor);
            $purchase->setPayment($amount);
            $purchase->setProcessType('Due');
            $purchase->setTransactionMethod($method);
            $purchase->setProcess('approved');
            $purchase->setRemark($remark);
            $em->persist($purchase);
            $em->flush();
            $em->getRepository('AccountingBundle:AccountPurchase')->updateVendorBalance($purchase);
            return new Response('success');
        }
        return new Response('failed');


    }

}


