<?php


namespace Appstore\Bundle\BusinessBundle\Controller;


use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class ReportController extends Controller
{

	public function paginate($entities)
	{
		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
			$entities,
			$this->get('request')->query->get('page', 1)/*page number*/,
			25  /*limit per page*/
		);
		$pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
		return $pagination;
	}

    public function systemOverviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3);
        $datetime = new \DateTime("now");
        $dataIncome['startDate'] = $user->getGlobalOption()->getCreated()->format('Y-m-d');
        $dataIncome['endDate'] = $datetime->format('Y-m-d');
        $income = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportBusinessTotalIncome($this->getUser(),$dataIncome);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
        $currentStockPrice = $em->getRepository('BusinessBundle:BusinessParticular')->reportCurrentStockPrice($user);
        $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($user, $data);
        $accountSales = $em->getRepository('AccountingBundle:AccountSales')->salesOverview($user, $data);
        $accountExpenditure = $em->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user, $data);

        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:systemOverview.html.twig', array(
                'option'                => $user->getGlobalOption(),
                'income'     => $income,
                'transactionCashOverviews'     => $transactionCashOverview,
                'currentStockPrice'     => $currentStockPrice,
                'accountPurchase'       => $accountPurchase,
                'accountSales'          => $accountSales,
                'accountExpenditure'    => $accountExpenditure,
                'searchForm'            => $data,
            ));

        }else{

            $html = $this->renderView(
                'BusinessBundle:Report:systemOverviewPdf.html.twig', array(
                'option'                        => $user->getGlobalOption(),
                'income'                        => $income,
                'transactionCashOverviews'      => $transactionCashOverview,
                'currentStockPrice'             => $currentStockPrice,
                'accountPurchase'               => $accountPurchase,
                'accountSales'                  => $accountSales,
                'accountExpenditure'            => $accountExpenditure,
                'searchForm'                    => $data,
            ));
            $this->downloadPdf($html,'systemOverview.pdf');
        }
    }

    public function purchaseOverviewAction(){

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();

        $purchaseCashOverview   = $em->getRepository('BusinessBundle:BusinessPurchase')->reportPurchaseOverview($user,$data);
        $transactionCash        = $em->getRepository('BusinessBundle:BusinessPurchase')->reportPurchaseTransactionOverview($user,$data);
        $vendors        = $em->getRepository('BusinessBundle:BusinessPurchase')->reportPurchaseVendor($user,$data);
        $purchaseMode           = $em->getRepository('BusinessBundle:BusinessPurchase')->reportPurchaseModeOverview($user,$data);

        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:purchase/overview.html.twig', array(
                'option'                            => $user->getGlobalOption() ,
                'purchaseCashOverview'              => $purchaseCashOverview ,
                'transactionCash'                   => $transactionCash ,
                'vendors'                           => $vendors ,
                'purchaseMode'                      => $purchaseMode ,
                'stockMode'                         => '' ,
                'searchForm'                        => $data,

            ));

        }else{

            $html = $this->renderView(
                'BusinessBundle:Report:purchase/overviewPdf.html.twig', array(
                'globalOption'                            => $user->getGlobalOption() ,
                'purchaseCashOverview'              => $purchaseCashOverview ,
                'transactionCash'                   => $transactionCash ,
                'vendors'                           => $vendors ,
                'purchaseMode'                      => $purchaseMode ,
                'stockMode'                         => '' ,
                'searchForm'                        => $data,
            ));
            $this->downloadPdf($html,'systemOverview.pdf');
        }
    }

    public function salesOverviewAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $cashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data);
        $sales = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesOverview($user,$data);
        $customerSalesSummary = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportCustomerSales($user,$data);
        $purchasePrice = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesItemPurchaseSalesOverview($user,$data);


        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/salesSummary.html.twig', array(
                'option'                    => $user->getGlobalOption() ,
                'cashOverview'              => $cashOverview ,
                'salesPrice'                => $sales ,
                'customerSalesSummary'      => $customerSalesSummary ,
                'purchasePrice'             => $purchasePrice ,
                'branches'                  => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'                => $data ,
            ));

        }else{

            $html = $this->renderView(
                'BusinessBundle:Report:sales/salesSummaryPdf.html.twig', array(
                    'globalOption'                  => $this->getUser()->getGlobalOption(),
                    'option'                    => $user->getGlobalOption() ,
                    'cashOverview'              => $cashOverview ,
                    'salesPrice'                => $sales['summary'] ,
                    'customerSalesSummary'      => $sales['customerSalesSummary'] ,
                    'purchasePrice'             => $purchasePrice ,
                    'branches'                  => $this->getUser()->getGlobalOption()->getBranches(),
                    'searchForm'                => $data ,
                )
            );
            $this->downloadPdf($html,'salesSummaryPdf.pdf');
        }

    }

    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */
    public function stockPriceReportAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->currentStockPrice($config,$data);
        $type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
        $category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('businessConfig' => $config ,'status'=>1));
        return $this->render('BusinessBundle:Report:stock-price.html.twig', array(
            'pagination' => $entities,
            'types' => $type,
            'categories' => $category,
            'config' => $config,
            'searchForm' => $data,
        ));

    }



	public function salesDetailsAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->salesReport($user,$data);
		$pagination = $this->paginate($entities);
		$salesPurchasePrice = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->salesPurchasePriceReport($user,$data,$pagination);
		$purchaseSalesPrice = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesItemPurchaseSalesOverview($user,$data);
		$transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
		$cashOverview = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesOverview($user,$data);


        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/sales.html.twig', array(
                'option'                => $user->getGlobalOption() ,
                'entities'              => $pagination ,
                'purchasePrice'         => $salesPurchasePrice ,
                'cashOverview'          => $cashOverview,
                'purchaseSalesPrice'    => $purchaseSalesPrice,
                'transactionMethods'    => $transactionMethods ,
                'branches'              => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'            => $data,
            ));

        }else{
            $html = $this->renderView(
                'BusinessBundle:Report:sales/salesPdf.html.twig', array(
                    'globalOption'                  => $this->getUser()->getGlobalOption(),
                    'option'                => $user->getGlobalOption() ,
                    'entities'              => $pagination ,
                    'purchasePrice'         => $salesPurchasePrice ,
                    'cashOverview'          => $cashOverview,
                    'purchaseSalesPrice'    => $purchaseSalesPrice,
                    'transactionMethods'    => $transactionMethods ,
                    'branches'              => $this->getUser()->getGlobalOption()->getBranches(),
                    'searchForm'            => $data,
                )
            );
            $this->downloadPdf($html,'salesPdf.pdf');
        }
	}

    public function stockItemReportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        $damages = $em->getRepository('BusinessBundle:BusinessDistributionReturnItem')->returnRemainingStock($config);
        $category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('businessConfig'=>$config,'status'=>1));
        return $this->render('BusinessBundle:Report:stock.html.twig', array(
            'pagination' => $pagination,
            'damages' => $damages,
            'categories' => $category,
            'searchForm' => $data,
        ));
    }

	public function salesStockItemAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $purchaseSalesPrice = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesItemPurchaseSalesOverview($user,$data);
		$cashOverview = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesOverview($user,$data);
		$entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportSalesStockItem($user,$data);
		$type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
		$category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('businessConfig'=>$config,'status'=>1));
		return $this->render('BusinessBundle:Report:sales/salesStock.html.twig', array(
			'option'  => $user->getGlobalOption() ,
			'entities' => $entities,
			'cashOverview' => $cashOverview,
			'purchaseSalesItem' => $purchaseSalesPrice,
			'types' => $type,
			'categories' => $category,
			'branches' => $this->getUser()->getGlobalOption()->getBranches(),
			'searchForm' => $data,
		));
	}

    public function salesCommissionStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportCommissionSalesStockItem($user,$data);
        $pagination = $this->paginate($entities);
        $vendors = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->findBy(array('globalOption'=>$user->getGlobalOption(),'status'=>1),array('companyName'=>'ASC'));

        if(empty($data['pdf'])){
            return $this->render('BusinessBundle:Report:sales/commissionSalesStock.html.twig', array(
                'option'  => $user->getGlobalOption() ,
                'entities' => $pagination,
                'vendors' => $vendors,
                'searchForm' => $data,
            ));

        }else{

            $html = $this->renderView(
                'BusinessBundle:Report:sales/commissionSalesStockPdf.html.twig', array(
                    'option'  => $user->getGlobalOption() ,
                    'entities' => $pagination,
                    'vendors' => $vendors,
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'commissionSalesStock.pdf');
        }
    }

	public function productLedgerAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportCustomerSalesItem($user,$data);
		$pagination = $this->paginate($entities);
		$type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
		$category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('businessConfig'=>$config,'status'=>1));
		return $this->render('BusinessBundle:Report:productLedger.html.twig', array(
			'option'  => $user->getGlobalOption() ,
			'entities' => $pagination,
			'types' => $type,
			'categories' => $category,
			'branches' => $this->getUser()->getGlobalOption()->getBranches(),
			'searchForm' => $data,
		));
	}


	public function salesUserAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$salesPurchasePrice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->salesUserPurchasePriceReport($user,$data);
		$entities = $em->getRepository('BusinessBundle:BusinessInvoice')->salesUserReport($user,$data);
		return $this->render('BusinessBundle:Report:sales/salesUser.html.twig', array(
			'option'  => $user->getGlobalOption() ,
			'entities'      => $entities ,
			'salesPurchasePrice'      => $salesPurchasePrice ,
			'branches' => $this->getUser()->getGlobalOption()->getBranches(),
			'searchForm'    => $data ,
		));
	}

    public function salesSrAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessParticular')->salesSrReport($user,$data);
        $executives = $this->getDoctrine()->getRepository('BusinessBundle:Marketing')->findBy(array('businessConfig' => $user->getGlobalOption()->getBusinessConfig()));
        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/salesSr.html.twig', array(
                'option'            => $user->getGlobalOption() ,
                'entities'          => $entities ,
                'executives'        => $executives ,
                'branches'          => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'        => $data ,
            ));

        }else{

            $marketing = $this->getDoctrine()->getRepository('BusinessBundle:Marketing')->find($data['marketing']);
            $html = $this->renderView(
                'BusinessBundle:Report:sales/salesSrPdf.html.twig', array(
                    'option'        => $user->getGlobalOption() ,
                    'marketing'     => $marketing ,
                    'entities'      => $entities ,
                    'searchForm'    => $data ,
                )
            );
            $this->downloadPdf($html,'sales-sr.pdf');
        }

    }

    public function salesDsrAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessParticular')->salesSrReport($user,$data);
        $executives = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findBy(array('globalOption' => $user->getGlobalOption()));

        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/salesDsr.html.twig', array(
                'option'            => $user->getGlobalOption() ,
                'entities'          => $entities,
                'executives'        => $executives,
                'branches'          => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'        => $data,
            ));

        }else{

            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->find($data['customer']);
            $html = $this->renderView(
                'BusinessBundle:Report:sales/salesDsrPdf.html.twig', array(
                    'option'        => $user->getGlobalOption(),
                    'marketing'     => $customer,
                    'entities'      => $entities,
                    'searchForm'    => $data,
                )
            );
            $this->downloadPdf($html,'sales-dsr.pdf');

        }
    }

    public function salesAreaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessParticular')->salesSrReport($user,$data);
        $executives = $this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->findBy(array('businessConfig' => $user->getGlobalOption()->getBusinessConfig()));
        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/salesArea.html.twig', array(
                'option'            => $user->getGlobalOption() ,
                'entities'          => $entities,
                'executives'        => $executives,
                'branches'          => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'        => $data,
            ));

        }else{

            $customer = $this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->find($data['area']);
            $html = $this->renderView(
                'BusinessBundle:Report:sales/salesAreaPdf.html.twig', array(
                    'option'        => $user->getGlobalOption(),
                    'marketing'     => $customer,
                    'entities'      => $entities,
                    'searchForm'    => $data,
                )
            );
            $this->downloadPdf($html,'sales-area.pdf');

        }
    }

    public function conditionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->salesConditionReport($user,$data);
        $executives = $this->getDoctrine()->getRepository('AccountingBundle:AccountCondition')->findBy(array('globalOption' => $user->getGlobalOption()));
        if(empty($data['pdf'])){
            return $this->render('BusinessBundle:Report:condition/condition.html.twig', array(
                'option'            => $user->getGlobalOption() ,
                'entities'          => $entities,
                'executives'        => $executives,
                'searchForm'        => $data,
            ));

        }else{

            $html = $this->renderView(
                'BusinessBundle:Report:condition/conditionPdf.html.twig', array(
                    'option'        => $user->getGlobalOption(),
                    'entities'      => $entities,
                    'searchForm'    => $data,
                )
            );
            $this->downloadPdf($html,'sales-area.pdf');

        }
    }

    public function conditionCustomerAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->salesConditionCustomerReport($user,$data);
        $conditions = $this->getDoctrine()->getRepository('AccountingBundle:AccountCondition')->findBy(array('globalOption' => $user->getGlobalOption(),'status'=>1),array('name'=>"ASC"));
        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:condition/customer.html.twig', array(
                'option'            => $user->getGlobalOption() ,
                'entities'          => $entities,
                'conditions'        => $conditions,
                'branches'          => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'        => $data,
            ));

        }else{

            $customer = $this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->find($data['courier']);
            $html = $this->renderView(
                'BusinessBundle:Report:condition/customerPdf.html.twig', array(
                    'option'        => $user->getGlobalOption(),
                    'marketing'     => $customer,
                    'entities'      => $entities,
                    'searchForm'    => $data,
                )
            );
            $this->downloadPdf($html,'sales-courier.pdf');

        }
    }

    public function conditionDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->salesConditionDetailsReport($user,$data);
        //$customers = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findBy(array('globalOption' => $user->getGlobalOption()),array('name'=>"ASC"));
        $conditions = $this->getDoctrine()->getRepository('AccountingBundle:AccountCondition')->findBy(array('globalOption' => $user->getGlobalOption(),'status'=>1),array('name'=>"ASC"));
        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:condition/conditionDetails.html.twig', array(
                'option'            => $user->getGlobalOption() ,
                'entities'          => $entities,
                'conditions'        => $conditions,
                'searchForm'        => $data,
            ));

        }else{

            $customer = $this->getDoctrine()->getRepository('AccountingBundle:AccountCondition')->find($data['courier']);
            $html = $this->renderView(
                'BusinessBundle:Report:condition/detailsPdf.html.twig', array(
                    'option'        => $user->getGlobalOption(),
                    'marketing'     => $customer,
                    'couriers'      => $couriers,
                    'entities'      => $entities,
                    'searchForm'    => $data,
                )
            );
            $this->downloadPdf($html,'sales-courier.pdf');

        }
    }

	public function monthlyUserSalesAction(){
        set_time_limit(0);
        ignore_user_abort(true);
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$config = $user->getGlobalOption()->getBusinessConfig();
		$employees = $em->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());
		$entities = $em->getRepository('BusinessBundle:BusinessInvoice')->currentMonthSales($user,$data);
		$salesAmount = array();
		foreach($entities as $row) {
			$salesAmount[$row['salesBy'].$row['month']] = $row['total'];
		}
		return $this->render('BusinessBundle:Report:sales/salesMonthlyUser.html.twig', array(
			'inventory'      => $config ,
			'salesAmount'      => $salesAmount ,
			'employees'      => $employees ,
			'branches' => $this->getUser()->getGlobalOption()->getBranches(),
			'searchForm'    => $data ,
		));

	}

    public function monthlyProductSalesPriceAction(){
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();
        $stocks = $em->getRepository('BusinessBundle:BusinessParticular')->getStockItemReport($config);
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->dailyProductSalesPriceReport($user,$data);
        return $this->render('BusinessBundle:Report:sales/salesDailyStockPrice.html.twig', array(
            'inventory'      => $config ,
            'entities'       => $entities['dailySalesArr'] ,
            'totalItemSales'       => $entities['salesItemTotal'] ,
            'dailyTotalSalesArr'       => $entities['dailyTotalSalesArr'] ,
            'stocks'         => $stocks ,
            'searchForm'     => $data ,
        ));

    }

    public function monthlyProductSalesAction(){
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();
        $stocks = $em->getRepository('BusinessBundle:BusinessParticular')->getStockItemReport($config);
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->dailyProductSalesReport($user,$data);
        return $this->render('BusinessBundle:Report:sales/salesDailyStock.html.twig', array(
            'inventory'      => $config ,
            'entities'       => $entities['dailySalesArr'] ,
            'totalItemSales'       => $entities['salesItemTotal'] ,
            'stocks'         => $stocks ,
            'searchForm'     => $data ,
        ));

    }

    public function monthlyProductSummaryAction(){

        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();
        $stocks = $em->getRepository('BusinessBundle:BusinessParticular')->getStockItemReport($config);
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->monthlyProductSummaryReport($user,$data);
        return $this->render('BusinessBundle:Report:MonthlyStockSummary.html.twig', array(
            'inventory'      => $config ,
            'dailySalesArr'  => $entities['dailySalesArr'] ,
            'dailyPurchaseArr'  => $entities['dailyPurchaseArr'] ,
            'dailyPurchaseReturnArr'  => $entities['dailyPurchaseReturnArr'] ,
            'dailySalesReturnArr'  => $entities['dailySalesReturnArr'] ,
            'damageArr'  => $entities['damageArr'] ,
            'stocks'         => $stocks ,
            'searchForm'     => $data ,
        ));

    }

    public function customerMonthlyProductSalesAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();
        $stocks ="";
        $dailySalesArr ="";
        $salesItemTotal ="";
        $dailySalesSummaryArr ="";
        $monthlySalesSummary ="";
        $customer = "";
        if (isset($_REQUEST['mobile']) and $_REQUEST['mobile']) {
            $mobile = $_REQUEST['mobile'];
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption(), 'mobile' => $mobile));
            $stocks = $em->getRepository('BusinessBundle:BusinessParticular')->getStockItemReport($config);
            $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->customerDailyProductSalesReport($user, $data);
            $dailySalesArr = $entities['dailySalesArr'];
            $salesItemTotal = $entities['salesItemTotal'];
            $dailySalesSummaryArr = $entities['dailySalesSummaryArr'];
            $monthlySalesSummary = $entities['monthlySalesSummary'];
        }
        return $this->render('BusinessBundle:Report:sales/customerSalesDailyStock.html.twig', array(
            'inventory'             => $config ,
            'customer'              => $customer ,
            'entities'              => $dailySalesArr ,
            'totalItemSales'        => $salesItemTotal ,
            'dailySalesSummaryArr'  => $dailySalesSummaryArr ,
            'monthlySalesSummary'   => $monthlySalesSummary ,
            'stocks'                => $stocks ,
            'searchForm'            => $data ,
        ));

    }

	public function purchaseVendorAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->purchaseVendorReport($user,$data);
		return $this->render('BusinessBundle:Report:purchase/purchaseVendor.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'entities'              => $entities ,
			'searchForm'            => $data,
		));
	}


	public function purchaseVendorDetailsAction()
	{
		$data = $_REQUEST;
		$globalOption = $this->getUser()->getGlobalOption();
		$entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($globalOption,$data);
		$pagination = $this->paginate($entities);
		$overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($globalOption,$data);
		$accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent =array(5));
		$transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
		return $this->render('BusinessBundle:Report:purchase/purchase.html.twig', array(
			'globalOption' => $globalOption,
			'entities' => $pagination,
			'accountHead' => $accountHead,
			'transactionMethods' => $transactionMethods,
			'searchForm' => $data,
			'overview' => $overview,
		));
	}



	public function productPurchaseStockSalesAction()
	{

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$config = $user->getGlobalOption()->getMedicineConfig();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->productPurchaseStockSalesReport($user,$data);
		$pagination = $this->paginate($entities);
		$racks = $this->getDoctrine()->getRepository('BusinessBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>'1'));
		$modeFor = $this->getDoctrine()->getRepository('BusinessBundle:MedicineParticularType')->findBy(array('modeFor'=>'brand'));
		return $this->render('BusinessBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'pagination'            => $pagination ,
			'racks'                 => $racks,
			'modeFor'               => $modeFor,
			'searchForm'            => $data,
		));
	}


	public function productPurchaseStockSalesPriceAction()
	{

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$config = $user->getGlobalOption()->getMedicineConfig();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->productPurchaseStockSalesPriceReport($user,$data);
		$pagination = $this->paginate($entities);
		$racks = $this->getDoctrine()->getRepository('BusinessBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>'1'));
		$modeFor = $this->getDoctrine()->getRepository('BusinessBundle:MedicineParticularType')->findBy(array('modeFor'=>'brand'));
		return $this->render('BusinessBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'pagination'              => $pagination ,
			'racks' => $racks,
			'modeFor' => $modeFor,
			'searchForm'            => $data,
		));
	}


	public function purchaseBrandStockSalesAction()
	{

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->purchaseVendorStockReport($user,$data);
		return $this->render('BusinessBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'entities'              => $entities ,
			'searchForm'            => $data,
		));
	}


	public function purchaseBrandAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->purchaseVendorReport($user,$data);
		return $this->render('BusinessBundle:Report:purchase/purchaseBrand.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'entities'              => $entities ,
			'searchForm'            => $data,
		));
	}

	public function purchaseDetailsAction()
	{

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->purchaseReport($user,$data);
		$pagination = $this->paginate($entities);
		$cashOverview = $em->getRepository('BusinessBundle:BusinessPurchase')->reportPurchaseOverview($user,$data);
		$transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
		return $this->render('BusinessBundle:Report:purchase/purchase.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'entities'              => $pagination ,
			'cashOverview'          => $cashOverview,
			'transactionMethods'    => $transactionMethods ,
			'searchForm'            => $data,
		));
	}

    public function vendorCommissionBaseSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportVendorCommissionSalesItem($user,$data);
        $pagination = $this->paginate($entities);
        $vendors = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->findBy(['globalOption' => $this->getUser()->getGlobalOption()],['companyName'=>'ASC']);

        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/vendorCommissionSalesItem.html.twig', array(
                'option'  => $user->getGlobalOption() ,
                'entities' => $pagination,
                'vendors' => $vendors,
                'searchForm' => $data,
            ));

        }else{
            $html = $this->renderView(
                'BusinessBundle:Report:sales/vendorCommissionSalesItemPdf.html.twig', array(
                    'option'  => $user->getGlobalOption() ,
                    'entities' => $entities,
                    'vendors' => $vendors,
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'vendor-commission-sales-item.pdf');

        }

    }

    public function customerCommissionBaseSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportCustomerSalesItem($user,$data);
        $pagination = $this->paginate($entities);
        $type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
        $config = $user->getGlobalOption()->getMedicineConfig();
        $category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('businessConfig'=>$config,'status'=>1));

        return $this->render('BusinessBundle:Report:sales/customerSalesItem.html.twig', array(
            'option'  => $user->getGlobalOption() ,
            'entities' => $pagination,
            'types' => $type,
            'categories' => $category,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));
    }

    public function customerSalesItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportCustomerSalesItem($user,$data);
        $pagination = $this->paginate($entities);
        $type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
        $category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('businessConfig'=>$config,'status'=>1));
        return $this->render('BusinessBundle:Report:sales/customerSalesItem.html.twig', array(
            'option'  => $user->getGlobalOption() ,
            'entities' => $pagination,
            'types' => $type,
            'categories' => $category,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));
    }


    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */
    public function monthlyStockLedgerAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if(empty($data)){
            $compare = new \DateTime();
            $end =  $compare->format('j');
            $data['monthYear'] = $compare->format('Y-m-d');
            $data['month'] =  $compare->format('F');
            $data['year'] = $compare->format('Y');
        }else{
            $month = $data['month'];
            $year = $data['year'];
            $compare = new \DateTime("{$year}-{$month}-01");
            $end =  $compare->format('t');
            $data['monthYear'] = $compare->format('Y-m-d');
        }
        $openingBalance = array();
        $daily = '';
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getProducts($config->getId());
        if(isset($data['name']) and !empty($data['name'])){
            for ($i = 1; $end >= $i ; $i++ ){
                $no = sprintf("%s", str_pad($i,2, '0', STR_PAD_LEFT));
                $start =  $compare->format("Y-m-{$no}");
                $day =  $compare->format("{$no}-m-Y");
                $data['startDate'] = $start;
                $openingBalance[$day] = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->openingDailyQuantity($config,$data);
            }
            $daily = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->monthlyStockLedger($config,$data);
        }
        if(empty($data['pdf'])){
            return $this->render('BusinessBundle:Stock:productLedger.html.twig', array(
                'globalOption'                  => $this->getUser()->getGlobalOption(),
                'openingBalance'                => $openingBalance,
                'particulars'                   => $particulars,
                'entity'                        => $daily,
                'searchForm'                    => $data,
            ));
        }else{
            $html = $this->renderView(
                'BusinessBundle:Stock:productLedgerPdf.html.twig', array(
                    'option'                  => $this->getUser()->getGlobalOption(),
                    'openingBalance'                => $openingBalance,
                    'particulars'                => $particulars,
                    'entity'                        => $daily,
                    'searchForm'                    => $data,
                )
            );
            $this->downloadPdf($html,'productLedgerPdf.pdf');
        }
    }


    /**
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */
    public function stockLedgerAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $pagination = '';
        $entity = '';
        if(isset($data['particular']) and !empty($data['particular'])){
            $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->findOneBy(array('businessConfig' => $config,'id'=> $data['particular']));
            $pagination = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->getStockHistoryLedger($config,$data);
        }
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getProducts($config->getId());
        if(empty($data['pdf'])){
            return $this->render('BusinessBundle:Report/stock:productLedger.html.twig', array(
                'entity' => $entity,
                'pagination' => $pagination,
                'particulars' => $particulars,
                'searchForm' => $data,
            ));
        }else{
            $html = $this->renderView(
                'BusinessBundle:Report/stock:productLedgerPdf.html.twig', array(
                    'option' => $this->getUser()->getGlobalOption(),
                    'entity' => $entity,
                    'pagination' => $pagination,
                    'particulars' => $particulars,
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'productLedgerPdf.pdf');
        }
    }

    public function downloadPdf($html,$fileName = '')
    {
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename={$fileName}");
        echo $pdf;
        return new Response('');
    }


}
