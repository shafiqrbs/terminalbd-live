<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use JMS\SecurityExtraBundle\Annotation\Secure;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


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

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_REPORT")
     */

    public function salesOverviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $cashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user, $data);

        $salesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user, $data);
        $purchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user, $data);
        return $this->render('MedicineBundle:Report:sales/salesOverview.html.twig', array(
            'option' => $user->getGlobalOption(),
            'cashOverview' => $cashOverview,
            'salesPrice' => $salesPrice,
            'purchasePrice' => $purchasePrice,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));

    }

    public function salesDayBaseAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();

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
        $salesPrice = $em->getRepository('MedicineBundle:MedicineSales')->dailySalesPrice($user, $data);
        $purchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->dailySalesPurchasePrice($user, $data);
        if(empty($data['pdf'])){
            return $this->render('MedicineBundle:Report:sales/dailySales.html.twig', array(
                'option' => $user->getGlobalOption(),
                'salesTrans' => $salesPrice,
                'purchaseTrans' => $purchasePrice,
                'searchForm' => $data,
            ));
        }else{
            $html = $this->renderView(
                'MedicineBundle:Report:sales/dailySalesPdf.html.twig', array(
                    'option' => $user->getGlobalOption(),
                    'salesTrans' => $salesPrice,
                    'purchaseTrans' => $purchasePrice,
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'income.pdf');
        }
    }

    public function salesDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicineSales')->salesReport($user, $data);
        $pagination = $this->paginate($entities);
        $salesPurchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->salesPurchasePriceReport($user, $data, $pagination);
        $purchaseSalesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user, $data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        $banks = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->findBy(array('globalOption' => $user->getGlobalOption(),'status' => 1), array('name' => 'ASC'));
        $mobiles =  $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->findBy(array('globalOption' => $user->getGlobalOption() , 'status' => 1), array('name' => 'ASC'));

        $cashOverview = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user, $data);
        return $this->render('MedicineBundle:Report:sales/sales.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $pagination,
            'banks' => $banks,
            'mobiles' => $mobiles,
            'purchasePrice' => $salesPurchasePrice,
            'cashOverview' => $cashOverview,
            'purchaseSalesPrice' => $purchaseSalesPrice,
            'transactionMethods' => $transactionMethods,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));
    }

    public function salesStockItemAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $purchaseSalesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user, $data);
        $cashOverview = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user, $data);
        $entities = $em->getRepository('MedicineBundle:MedicineSalesItem')->reportSalesStock($user, $data);
        if(empty($data['pdf'])){
            $pagination = $this->paginate($entities);
            return $this->render('MedicineBundle:Report:sales/salesStock.html.twig', array(
                'option' => $user->getGlobalOption(),
                'pagination' => $pagination,
                'cashOverview' => $cashOverview,
                'purchaseSalesItem' => $purchaseSalesPrice,
                'searchForm' => $data,
            ));
        }else{
            $html = $this->renderView(
                'MedicineBundle:Report:sales/salesStockPdf.html.twig', array(
                    'option' => $user->getGlobalOption(),
                    'entities' => $entities,
                    'cashOverview' => $cashOverview,
                    'purchaseSalesItem' => $purchaseSalesPrice,
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'medicine-sales-pdf.pdf');
        }


    }

    public function salesUserAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $salesPurchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user, $data);
        $entities = $em->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user, $data);
        return $this->render('MedicineBundle:Report:sales/salesUser.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $entities,
            'salesPurchasePrice' => $salesPurchasePrice,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));
    }

    public function monthlyUserSalesAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();
        $employees = $em->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());
        $entities = $em->getRepository('MedicineBundle:MedicineSales')->monthlySales($user, $data);
        $salesAmount = array();
        foreach ($entities as $row) {
            $salesAmount[$row['salesBy'] . $row['month']] = $row['total'];
        }
        return $this->render('MedicineBundle:Report:sales/salesMonthlyUser.html.twig', array(
            'inventory' => $config,
            'salesAmount' => $salesAmount,
            'employees' => $employees,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));

    }


    public function purchaseOverviewAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $purchaseCashOverview = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user, $data);
        $transactionCash = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseTransactionOverview($user, $data);
        $purchaseMode = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseModeOverview($user, $data);
        $stockMode = $em->getRepository('MedicineBundle:MedicinePurchase')->reportStockModeOverview($user, $data);
        return $this->render('MedicineBundle:Report:purchase/overview.html.twig', array(
            'option' => $user->getGlobalOption(),
            'purchaseCashOverview' => $purchaseCashOverview,
            'transactionCash' => $transactionCash,
            'purchaseMode' => $purchaseMode,
            'stockMode' => $stockMode,
            'searchForm' => $data,

        ));
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
        $income = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMedicineTotalIncome($this->getUser(),$dataIncome);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
        $currentStockPrice = $em->getRepository('MedicineBundle:MedicineStock')->reportCurrentStockPrice($user);
        $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($user, $data);
        $accountSales = $em->getRepository('AccountingBundle:AccountSales')->salesOverview($user, $data);
        $accountExpenditure = $em->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user, $data);
        return $this->render('MedicineBundle:Report:systemOverview.html.twig', array(
            'option'                => $user->getGlobalOption(),
            'income'     => $income,
            'transactionCashOverviews'     => $transactionCashOverview,
            'currentStockPrice'     => $currentStockPrice,
            'accountPurchase'       => $accountPurchase,
            'accountSales'          => $accountSales,
            'accountExpenditure'    => $accountExpenditure,
            'searchForm'            => $data,
        ));
    }

    public function systemOverviewPdfAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3);
        $datetime = new \DateTime("now");
        $dataIncome['startDate'] = $user->getGlobalOption()->getCreated()->format('Y-m-d');
        $dataIncome['endDate'] = $datetime->format('Y-m-d');
        $income = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMedicineTotalIncome($this->getUser(),$dataIncome);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
        $currentStockPrice = $em->getRepository('MedicineBundle:MedicineStock')->reportCurrentStockPrice($user);
        $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($user, $data);
        $accountSales = $em->getRepository('AccountingBundle:AccountSales')->salesOverview($user, $data);
        $accountExpenditure = $em->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user, $data);
        $html = $this->renderView(
            'MedicineBundle:Report:systemOverviewPdf.html.twig', array(
            'option'                        => $user->getGlobalOption(),
            'income'                        => $income,
            'transactionCashOverviews'      => $transactionCashOverview,
            'currentStockPrice'             => $currentStockPrice,
            'accountPurchase'               => $accountPurchase,
            'accountSales'                  => $accountSales,
            'accountExpenditure'            => $accountExpenditure,
            'searchForm'                    => $data,
        ));
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="system-overview-pdf.pdf"');
        echo $pdf;
        return new Response('');
    }

    public function purchaseVendorAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->purchaseVendorReport($user, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseVendor.html.twig', array(
            'option'        => $user->getGlobalOption(),
            'entities'      => $entities,
            'searchForm'    => $data,
        ));
    }

    public function salesPurchaseChartAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $datetime = new \DateTime("now");
        $data['startDate'] = $datetime->format('Y-m-d');
        $data['endDate'] = $datetime->format('Y-m-d');
        $startMonthDate = $datetime->format('Y-m-01 00:00:00');
        $endMonthDate = $datetime->format('Y-m-t 23:59:59');
        $user = $this->getUser();
        $medicineSalesMonthly = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->medicineSalesMonthly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
        $medicinePurchaseMonthly = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->medicinePurchaseMonthly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
        $employees = $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());
        $salesUserReport = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,array('startDate'=>$data['startDate'],'endDate'=>$data['endDate']));

        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->currentMonthSales($user,$data);
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
        return $this->render('MedicineBundle:Report:sales/monthlyChart.html.twig', array(
            'option'        => $user->getGlobalOption(),
            'medicineSalesMonthly'      => $medicineSalesMonthlyArr ,
            'medicinePurchaseMonthly'   => $medicinePurchaseMonthlyArr ,
            'salesUserReport'           => $salesUserReport ,
            'userSalesAmount'           => $userSalesAmount ,
            'employees'                 => $employees ,
            'searchForm'    => $data,
        ));
    }

    public function topSalesItemAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->topSalesItem($user,$data);
        return $this->render('MedicineBundle:Report:sales/topSalesItem.html.twig', array(
            'option'        => $user->getGlobalOption(),
            'entities'    => $entities,
            'searchForm'    => $data,
        ));
    }

    public function lowSalesItemAction()
    {

        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->slowSalesItem($user,$data);
        $pagination = $this->paginate($entities);
        return $this->render('MedicineBundle:Report:sales/slowSalesItem.html.twig', array(
            'option'        => $user->getGlobalOption(),
            'pagination'    => $pagination,
            'searchForm'    => $data,
        ));
    }


    public function dailySalesChartAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->purchaseVendorReport($user, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseVendor.html.twig', array(
            'option'        => $user->getGlobalOption(),
            'entities'      => $entities,
            'searchForm'    => $data,
        ));
    }

    public function salesVendorCustomerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->salesVendorCustomerReport($user, $data);
        $salesVendors = $em->getRepository('MedicineBundle:MedicinePurchase')->vendorCustomerSalesReport($user, $entities);
        return $this->render('MedicineBundle:Report:sales/salesVendorCustomer.html.twig', array(
            'option'        => $user->getGlobalOption(),
            'entities'      => $entities,
            'salesVendors'  => $salesVendors,
            'searchForm'    => $data,
        ));
    }

    public function purchaseVendorDetailsAction()
    {
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($globalOption, $data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($globalOption, $data);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent = array(5));
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'asc'));
        return $this->render('MedicineBundle:Report:purchase/purchase.html.twig', array(
            'globalOption'          => $globalOption,
            'entities'              => $pagination,
            'accountHead'           => $accountHead,
            'transactionMethods'    => $transactionMethods,
            'searchForm'            => $data,
            'overview'              => $overview,
        ));
    }

    public function purchaseItemReportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $brand = (isset($data['brandName']) and $data['brandName']) ? $data['brandName']:'';
        $vendor = (isset($data['vendor']) and $data['vendor']) ? $data['vendor']:'';
        $pagination = "";
        if($brand or $vendor){
            $pagination = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->purchaseItemReport($config,$data);
        }
        return $this->render('MedicineBundle:Report:purchase/purchaseItem.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));

    }

    public function remainingStockReportAction()
    {
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $brand = (isset($data['brandName']) and $data['brandName']) ? $data['brandName']:'';
        $pagination = "";
        if($brand){
            $pagination = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findWithRemainingStock($config,$brand);
        }
        return $this->render('MedicineBundle:Report:stock/remaining.html.twig', array(
            'globalOption'          => $globalOption,
            'entities'              => $pagination,
        ));
    }


    public function purchaseVendorSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $purchasePrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getPurchaseVendorPrice($user, $data);
        $salesPrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getSalesVendorPrice($user, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseSalesVendor.html.twig', array(
            'option' => $user->getGlobalOption(),
            'purchasePrice' => $purchasePrice,
            'salesPrice' => $salesPrice,
            'searchForm' => $data,
        ));
    }

    public function purchaseBrandSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $brands = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getBrandLists($user);
        $pagination = $this->paginate($brands);
        $purchasePrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getPurchaseBrandReport($user, $pagination, $data);
        $salesPrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getSalesBrandReport($user, $pagination, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseSalesBrand.html.twig', array(
            'option' => $user->getGlobalOption(),
            'purchasePrice' => $purchasePrice,
            'salesPrice' => $salesPrice,
            'brands' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function purchaseBrandSalesDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $purchasePrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getPurchaseBrandDetailsReport($user, $data);
        $pagination = $this->paginate($purchasePrice);
        $salesPrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getSalesBrandDetailsReport($user, $pagination, $data);
        $modeFor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->findBy(array('modeFor' => 'brand'));

        return $this->render('MedicineBundle:Report:purchase/purchaseSalesBrandDetails.html.twig', array(
            'option' => $user->getGlobalOption(),
            'salesPrice' => $salesPrice,
            'pagination' => $pagination,
            'modeFor' => $modeFor,
            'searchForm' => $data,
        ));
    }

    public function productPurchaseStockSalesAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getMedicineConfig();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->productPurchaseStockSalesReport($user, $data);
        $pagination = $this->paginate($entities);
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig' => $config, 'particularType' => '1'));
        $modeFor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->findBy(array('modeFor' => 'brand'));
        return $this->render('MedicineBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
            'option' => $user->getGlobalOption(),
            'pagination' => $pagination,
            'racks' => $racks,
            'modeFor' => $modeFor,
            'searchForm' => $data,
        ));
    }


    public function productPurchaseStockSalesPriceAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getMedicineConfig();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->productPurchaseStockSalesPriceReport($user, $data);
        $pagination = $this->paginate($entities);
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig' => $config, 'particularType' => '1'));
        $modeFor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->findBy(array('modeFor' => 'brand'));
        return $this->render('MedicineBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
            'option' => $user->getGlobalOption(),
            'pagination' => $pagination,
            'racks' => $racks,
            'modeFor' => $modeFor,
            'searchForm' => $data,
        ));
    }


    public function purchaseBrandStockSalesAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->purchaseVendorStockReport($user, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }

    public function brandStockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicineStock')->brandStock($user, $data);
        return $this->render('MedicineBundle:Report:brandStock.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }

    public function brandStockPdfAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicineStock')->brandStock($user, $data);
        $html = $this->renderView(
            'MedicineBundle:Report:brandStockPdf.html.twig', array(
                'option' => $user->getGlobalOption(),
                'entities' => $entities,
                'searchForm' => $data,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="brand-stock-pdf.pdf"');
        echo $pdf;
        return new Response('');

    }

    public function purchaseBrandAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->purchaseVendorReport($user, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseBrand.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }


    public function purchaseDetailsAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->purchaseReport($user, $data);
        $pagination = $this->paginate($entities);
        $cashOverview = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user, $data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('MedicineBundle:Report:purchase/purchase.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $pagination,
            'cashOverview' => $cashOverview,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));
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