<?php

namespace Appstore\Bundle\InventoryBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;


/**
 * ItemColor controller.
 *
 */
class ReportController extends Controller
{

    public function paginate($entities)
    {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all StockItem entities.
     *
     */
    public function overviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
      //  $purchaseOverview = $em->getRepository('InventoryBundle:PurchaseItem')->findItemWithPurchaseQuantity($inventory);
      //  var_dump($purchaseOverview);
     //   exit;

        $purchaseOverview = $em->getRepository('InventoryBundle:Purchase')->purchaseOverview($inventory,$data);
        $priceOverview = $em->getRepository('InventoryBundle:Item')->getStockPriceOverview($inventory,$data);
        $salesPurchasePrice = $em->getRepository('InventoryBundle:SalesItem')->reportPurchasePrice($this->getUser(),$data);
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        return $this->render('InventoryBundle:Report:stockOverview.html.twig', array(
            'priceOverview' => $priceOverview[0],
            'stockOverview' => $stockOverview,
            'purchaseOverview' => $purchaseOverview,
            'salesPurchasePrice' => $salesPurchasePrice,
        ));
    }



    public function tillStockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        $entities = $em->getRepository('InventoryBundle:StockItem')->stockItem($inventory,$data);
        $pagination = $this->paginate($entities);
        $previousQuantity = $em->getRepository('InventoryBundle:StockItem')->tillStockItem($mode ='previous',$inventory,$data);
        $purchaseQuantity = $em->getRepository('InventoryBundle:StockItem')->tillStockItem($mode ='purchase',$inventory,$data);
        $saleQuantity = $em->getRepository('InventoryBundle:StockItem')->tillStockItem($mode ='sales',$inventory,$data);

        return $this->render('InventoryBundle:Report:tillStock.html.twig', array(
            'entities' => $pagination,
            'previousQuantity' => $previousQuantity,
            'purchaseQuantity' => $purchaseQuantity,
            'saleQuantity' => $saleQuantity,
            'stockOverview' => $stockOverview,
            'searchForm' => $data,
        ));
    }

    public function periodicStockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        $entities = $em->getRepository('InventoryBundle:StockItem')->stockItem($inventory,$data);
        $pagination = $this->paginate($entities);
        $previousQuantity = $em->getRepository('InventoryBundle:StockItem')->periodicStockItem($mode ='previous',$inventory,$data);
        $purchaseQuantity = $em->getRepository('InventoryBundle:StockItem')->periodicStockItem($mode ='purchase',$inventory,$data);
        $saleQuantity = $em->getRepository('InventoryBundle:StockItem')->periodicStockItem($mode ='sales',$inventory,$data);

        return $this->render('InventoryBundle:Report:periodicStock.html.twig', array(
            'entities' => $pagination,
            'previousQuantity' => $previousQuantity,
            'purchaseQuantity' => $purchaseQuantity,
            'saleQuantity' => $saleQuantity,
            'stockOverview' => $stockOverview,
            'searchForm' => $data,
        ));

    }

    public function operationalStockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:StockItem')->stockItem($inventory,$data);
        $pagination = $this->paginate($entities);
        $quantity = $em->getRepository('InventoryBundle:StockItem')->operationalStockItem($inventory,$data);
        return $this->render('InventoryBundle:Report:operationalStock.html.twig', array(
            'entities' => $pagination,
            'quantity' => $quantity,
            'searchForm' => $data,
        ));

    }

    public function groupStockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        //$stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        $entities = $em->getRepository('InventoryBundle:StockItem')->stockGroupItemName($inventory,$data);
        $pagination = $this->paginate($entities);

        $previousQuantity = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='previous',$inventory,$data);
        $purchase = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='purchase',$inventory,$data);
        $purchaseReturn = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='purchaseReturn',$inventory,$data);
        $sale = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='sales',$inventory,$data);
        $salesReturn = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='salesReturn',$inventory,$data);
        $damage = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='damage',$inventory,$data);

        return $this->render('InventoryBundle:Report:productGroupStock.html.twig', array(
            'entities' => $pagination,
            'previousQuantity' => $previousQuantity,
            'purchase' => $purchase,
            'purchaseReturn' => $purchaseReturn,
            'sale' => $sale,
            'salesReturn' => $salesReturn,
            'damage' => $damage,
            'searchForm' => $data,
        ));

    }

    public function stockAction($group)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:StockItem')->getGroupStock($inventory,$group);
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        return $this->render('InventoryBundle:Report:'.$group.'.html.twig', array(
            'entities' => $entities,
            'stockOverview' => $stockOverview,
            'searchForm' => $data,
        ));
    }

    public function stockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        $entities = $em->getRepository('InventoryBundle:Item')->reportFindWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Report:stock.html.twig', array(
            'entities' => $pagination,
            'stockOverview' => $stockOverview,
            'searchForm' => $data,
        ));
    }

    public function purchaseOverviewAction()
    {
	    $em = $this->getDoctrine()->getManager();
	    $data = $_REQUEST;
	    $user = $this->getUser();
	    $inventory = $user->getGlobalOption()->getInventoryConfig()->getId();
	    $cashOverview = $em->getRepository('InventoryBundle:Purchase')->purchaseOverview($inventory,$data);
	    $transactionCash = $em->getRepository('InventoryBundle:Purchase')->reportTransactionOverview($inventory,$data);
	    $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
	    return $this->render('InventoryBundle:Report:purchase/overview.html.twig', array(
		    'inventory' => $inventory,
		    'cashOverview'              => $cashOverview ,
		    'transactionCash'           => $transactionCash ,
		    'transactionMethods'        => $transactionMethods ,
		    'option'                    => $this->getUser()->getGlobalOption(),
		    'searchForm'                => $data ,
	    ));
    }


    public function purchaseAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities       = $em->getRepository('InventoryBundle:StockItem')->getProcessStock($inventory,$data);
        $pagination = $this->paginate($entities);
        $purchaseQuantity   = $em->getRepository('InventoryBundle:StockItem')->getGroupPurchaseItemStock($inventory,$data);
        return $this->render('InventoryBundle:Report:purchase.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'purchaseQuantity' => $purchaseQuantity,
        ));
    }

    public function salesTransactionOverviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $cashOverview = $em->getRepository('InventoryBundle:Sales')->reportSalesOverview($user,$data);
        $purchaseSalesPrice = $em->getRepository('InventoryBundle:Sales')->reportSalesItemPurchaseSalesOverview($user,$data);
        $transactionCash = $em->getRepository('InventoryBundle:Sales')->reportSalesTransactionOverview($user,$data);
        $salesMode = $em->getRepository('InventoryBundle:Sales')->reportSalesModeOverview($user,$data);
        $salesProcess = $em->getRepository('InventoryBundle:Sales')->reportSalesProcessOverview($user,$data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('InventoryBundle:Report:sales/salesOverview.html.twig', array(
            'inventory' => $inventory,
            'cashOverview'              => $cashOverview ,
            'purchaseSalesPrice'        => $purchaseSalesPrice ,
            'transactionCash'           => $transactionCash ,
            'salesMode'                 => $salesMode ,
            'salesProcess'              => $salesProcess ,
            'transactionMethods'        => $transactionMethods ,
            'branches'                  => $this->getUser()->getGlobalOption()->getBranches(),
            'option'                    => $this->getUser()->getGlobalOption(),
            'searchForm'                => $data ,
        ));
    }

    public function salesTransactionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Sales')->salesReport($user,$data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        $purchaseSalesPrice = $em->getRepository('InventoryBundle:Sales')->reportSalesItemPurchaseSalesOverview($user,$data);
        $cashOverview = $em->getRepository('InventoryBundle:Sales')->reportSalesOverview($user,$data);

        return $this->render('InventoryBundle:Report:sales/sales.html.twig', array(
            'option'                    => $this->getUser()->getGlobalOption(),
            'inventory'             => $inventory ,
            'entities'              => $pagination ,
            'cashOverview'          => $cashOverview,
            'purchaseSalesPrice'    => $purchaseSalesPrice,
            'transactionMethods'    => $transactionMethods ,
            'branches'              => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'            => $data,
        ));
    }

    public function salesStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $purchaseSalesPrice = $em->getRepository('InventoryBundle:Sales')->reportSalesItemPurchaseSalesOverview($user,$data);
        $cashOverview = $em->getRepository('InventoryBundle:Sales')->reportSalesOverview($user,$data);
        $entities = $em->getRepository('InventoryBundle:Sales')->reportSalesItem($user,$data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Report:sales/salesStock.html.twig', array(
            'option'                => $this->getUser()->getGlobalOption(),
            'inventory'             => $inventory,
            'entities'              => $pagination,
            'cashOverview'          => $cashOverview,
            'purchaseSalesItem'     => $purchaseSalesPrice,
            'branches'              => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'            => $data
        ));
    }

    public function salesItemDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();
        if($inventory->getSalesMode() == "stock"){
            $entities = $em->getRepository('InventoryBundle:Sales')->reportSalesItemDetails($user,$data);
        }else{
            $entities = $em->getRepository('InventoryBundle:Sales')->reportSalesItemPurchaseItemDetails($user,$data);
        }
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Report:sales/salesItemDetails.html.twig', array(
            'option'                => $this->getUser()->getGlobalOption(),
            'inventory'             => $inventory,
            'entities'              => $pagination,
            'branches'              => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'            => $data,
        ));
    }

    public function dailySalesProfitAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $sales = $em->getRepository('InventoryBundle:Sales')->inventorySalesDaily($user,$data);
        $salesPurchase = $em->getRepository('InventoryBundle:SalesItem')->inventorySalesDaily($user,$data);
        $expenses = $em->getRepository('AccountingBundle:Expenditure')->monthlyExpenditure($user,$data);
        if(empty($data['download'])){
            return $this->render('InventoryBundle:Report:sales/dailySalesProfit.html.twig', array(
                'option'                    => $this->getUser()->getGlobalOption(),
                'monthlySales'              => $sales,
                'monthlySalesPurchase'      => $salesPurchase,
                'expenses'                  => $expenses,
                'searchForm'                => $data,
            ));
        }else{
            $html = $this->renderView(
                'InventoryBundle:Report:sales/dailySalesProfitPdf.html.twig', array(
                    'globalOption'              => $this->getUser()->getGlobalOption(),
                    'monthlySales'              => $sales,
                    'monthlySalesPurchase'      => $salesPurchase,
                    'expenses'                  => $expenses,
                    'searchForm'                => $data,
                )
            );
            $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
            $snappy          = new Pdf($wkhtmltopdfPath);
            $pdf             = $snappy->getOutputFromHtml($html);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="dailySalesProfitPdf.pdf"');
            echo $pdf;
            return new Response('');
        }

    }

    public function monthlySalesProfitAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $sales = $em->getRepository('InventoryBundle:Sales')->inventorySalesMonthly($user,$data);
        $purchaseEntities = $em->getRepository('InventoryBundle:SalesItem')->inventorySalesMonthly($user,$data);
        $expenses = $em->getRepository('AccountingBundle:Expenditure')->yearlyExpenditure($user,$data);
        if(empty($data['download'])){
            return $this->render('InventoryBundle:Report:sales/monthlySalesProfit.html.twig', array(
                'option'                    => $this->getUser()->getGlobalOption(),
                'monthlySales'              => $sales,
                'monthlySalesPurchase'      => $purchaseEntities,
                'expenses'                  => $expenses,
                'searchForm'                => $data,
            ));
        }else{
            $html = $this->renderView(
                'InventoryBundle:Report:sales/monthlySalesProfitPdf.html.twig', array(
                    'globalOption'              => $this->getUser()->getGlobalOption(),
                    'monthlySales'              => $sales,
                    'monthlySalesPurchase'      => $purchaseEntities,
                    'expenses'                  => $expenses,
                    'searchForm'                => $data,
                )
            );
            $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
            $snappy          = new Pdf($wkhtmltopdfPath);
            $pdf             = $snappy->getOutputFromHtml($html);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="monthlySalesProfitPdf.pdf"');
            echo $pdf;
            return new Response('');

        }

    }


    public function dailySalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('InventoryBundle:Sales')->inventorySalesDaily($user,$data);
        return $this->render('InventoryBundle:Report:sales/dailySales.html.twig', array(
            'entities'      => $entities ,
            'searchForm'    => $data ,
        ));
    }

    public function monthlySalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('InventoryBundle:Sales')->inventorySalesMonthly($user,$data);
        return $this->render('InventoryBundle:Report:sales/monthlySales.html.twig', array(
            'entities'      => $entities ,
            'searchForm'    => $data ,
        ));
    }

    public function salesUserAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $salesPurchasePrice = $em->getRepository('InventoryBundle:Sales')->salesUserPurchasePriceReport($user,$data);
        $entities = $em->getRepository('InventoryBundle:Sales')->salesUserReport($user,$data);
        return $this->render('InventoryBundle:Report:sales/salesUser.html.twig', array(
            'inventory'      => $inventory ,
            'entities'      => $entities ,
            'salesPurchasePrice'      => $salesPurchasePrice ,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'    => $data ,
        ));
    }

    public function monthlyUserSalesAction(){

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $employees = $em->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());
        $entities = $em->getRepository('InventoryBundle:Sales')->monthlySales($user,$data);
        $salesAmount = array();
        foreach($entities as $row) {
            $salesAmount[$row['salesBy'].$row['month']] = $row['total'];
        }

        return $this->render('InventoryBundle:Report:sales/salesMonthlyUser.html.twig', array(
            'inventory'      => $inventory ,
            'salesAmount'      => $salesAmount ,
            'employees'      => $employees ,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'    => $data ,
        ));

    }

    public function categoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

    }

    /**
     * Lists all ItemColor entities.
     *
     */
    public function overviewinAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();

        $entities = $em->getRepository('InventoryBundle:StockItem')->getStockPriceOverview($inventory,$data);
        return $this->render('InventoryBundle:Report:itemVendor.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data
        ));
    }

    /**
     * Lists all ItemColor entities.
     *
     */
    public function overviAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockPriceOverview($inventory,$data);
        if(!empty($data['group'])){
            $twig = 'stockOverview';
        }else{
            $twig = $data['group'];
        }
        return $this->render('InventoryBundle:Report:'.$twig.'.html.twig', array(
            'stockOverview' => $stockOverview,
            'searchForm' => $data
        ));
    }



    public function pdfIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportIncome($globalOption,$data);
        $html = $this->renderView(
            'AccountingBundle:Report:incomePdf.html.twig', array(
                'overview' => $overview,
                'print' => ''
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="incomePdf.pdf"');
        echo $pdf;

    }


    public function printIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report:incomePdf.html.twig', array(
            'overview' => $overview,
            'print' => '<script>window.print();</script>'
        ));

    }

}
