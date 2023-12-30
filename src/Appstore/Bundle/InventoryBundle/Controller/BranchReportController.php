<?php

namespace Appstore\Bundle\InventoryBundle\Controller;


use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Snappy\Pdf;


/**
 * ItemColor controller.
 *
 */
class BranchReportController extends Controller
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
        $branch = $this->getUser()->getProfile()->getBranches();

        $stockOverview = $em->getRepository('InventoryBundle:Delivery')->getStockOverview($inventory,$branch,$data);
        $salesOngoingOverview = $em->getRepository('InventoryBundle:Delivery')->getSalesOngoingOverview($inventory,$branch,$data);
        $salesOverview = $em->getRepository('InventoryBundle:Delivery')->getSalesOverview($inventory,$branch,$data);
        $salesReturnOverview = $em->getRepository('InventoryBundle:Delivery')->getSalesReturnOverview($inventory,$branch,$data);
        $returnOverview = $em->getRepository('InventoryBundle:Delivery')->getReturnOverview($inventory,$branch,$data);

        $stockOverview = array('stock'=>$stockOverview['quantity'] , 'ongoing'=> $salesOngoingOverview['quantity'],'sales' => $salesOverview['quantity'] , 'return'=> $returnOverview['quantity'],'salesReturn' => $salesReturnOverview['quantity']) ;
        return $this->render('InventoryBundle:BranchReport:stockOverview.html.twig', array(
            'stockOverview' => $stockOverview
        ));
    }


    public function stockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities =  $em->getRepository('InventoryBundle:Delivery')->stockItem( $this->getUser(), $group = 'item',$data);
        $pagination = $this->paginate($entities);
        $stockSalesItem =  $em->getRepository('InventoryBundle:Delivery')->stockSalesItem( $this->getUser(), $group = 'item',$data);
        $salesReturnItem = $em->getRepository('InventoryBundle:Delivery')->stockSalesReturnItem( $this->getUser(), $group = 'item',$data);
        $stockOngoingItem =  $em->getRepository('InventoryBundle:Delivery')->stockOngoingItem( $this->getUser(), $group = 'item',$data);
        $stockReturnItem =  $em->getRepository('InventoryBundle:Delivery')->stockReturnItem( $this->getUser(), $group = 'item',$data);

        return $this->render('InventoryBundle:BranchReport:stock.html.twig', array(
            'entities'                  => $pagination,
            'stockOngoingItem'          => $stockOngoingItem,
            'stockSalesItem'            => $stockSalesItem,
            'salesReturnItem'           => $salesReturnItem,
            'stockReturnItem'           => $stockReturnItem,
            'searchForm'                => $data,
        ));
    }

     public function stockBarcodeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $entities =  $em->getRepository('InventoryBundle:Delivery')->stockItem( $this->getUser(), $group = 'barcode',$data);
        $pagination = $this->paginate($entities);

        $stockSalesItem =  $em->getRepository('InventoryBundle:Delivery')->stockSalesItem( $this->getUser(),$group = 'barcode',$data);
        $salesReturnItem = $em->getRepository('InventoryBundle:Delivery')->stockSalesReturnItem( $this->getUser(),$group = 'barcode',$data);
        $stockOngoingItem =  $em->getRepository('InventoryBundle:Delivery')->stockOngoingItem( $this->getUser(),$group = 'barcode',$data);
        $stockReturnItem =  $em->getRepository('InventoryBundle:Delivery')->stockReturnItem( $this->getUser(),$group = 'barcode',$data);

        return $this->render('InventoryBundle:BranchReport:barcodeWiseStock.html.twig', array(
            'entities'                  => $pagination,
            'stockOngoingItem'          => $stockOngoingItem,
            'stockSalesItem'            => $stockSalesItem,
            'salesReturnItem'           => $salesReturnItem,
            'stockReturnItem'           => $stockReturnItem,
            'searchForm'                => $data,
        ));
    }

    public function barcodeBranchStockAction()
    {

        $em = $this->getDoctrine()->getManager();
        $id = 9699 ;
        $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->find($id);
        $user = $this->getUser();
        $branches = $user->getGlobalOption()->getBranches();
        $barcode = '0526024000551';
        $data = array('barcode'=> $barcode);
        $branchWiseReceiveItem =  $em->getRepository('InventoryBundle:Delivery')->branchWiseReceiveItem($user , $data);
        var_dump($branchWiseReceiveItem);
        exit;
        $branchWiseSalesItem =  $em->getRepository('InventoryBundle:Delivery')->branchWiseSalesItem($user , $purchaseItem);
        $branchWiseSalesReturnItem =  $em->getRepository('InventoryBundle:Delivery')->branchWiseSalesReturnItem($user, $purchaseItem);
        $branchWiseOngoingSalesItem =  $em->getRepository('InventoryBundle:Delivery')->branchWiseOngoingSalesItem($user, $purchaseItem);
        $ongoingSalesItem =  $em->getRepository('InventoryBundle:Delivery')->ongoingSalesItem($user, $purchaseItem);
        $branchWiseDeliveryReturnItem =  $em->getRepository('InventoryBundle:Delivery')->branchWiseDeliveryReturnItem($user,$purchaseItem);
        return $this->render('InventoryBundle:BranchReport:branchWiseBarcodeProductStock.html.twig', array(
            'entity'          => $purchaseItem,
            'branches'          => $branches,
            'branchWiseReceiveItem' => $branchWiseReceiveItem,
            'branchWiseSalesItem' => $branchWiseSalesItem,
            'branchWiseSalesReturnItem' => $branchWiseSalesReturnItem,
            'ongoingSalesItem' => $ongoingSalesItem,
            'branchWiseOngoingSalesItem' => $branchWiseOngoingSalesItem,
            'branchWiseDeliveryReturnItem' => $branchWiseDeliveryReturnItem,
        ));

    }

    public function stockItemAction()
    {
        return $this->render('InventoryBundle:BranchReport:stockDetails.html.twig', array(
            'entity'          => '',
            'entities'      => '',
            'stockOngoingItem' => '',
            'stockSalesItem' => '',
            'stockReturnItem' => '',
        ));

    }

    public function branchItemDetailsAction(Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entities = $em->getRepository('InventoryBundle:Delivery')->stockItemDetails($user,$item);
        $stockSalesItem =  $em->getRepository('InventoryBundle:Delivery')->stockSalesItemDetails($user,$item);
        $stockSalesReturnItem = $em->getRepository('InventoryBundle:Delivery')->stockSalesReturnItemDetails($user,$item);
        $stockOngoingItem =  $em->getRepository('InventoryBundle:Delivery')->stockOngoingItemDetails($user,$item);
        $stockReturnItem =  $em->getRepository('InventoryBundle:Delivery')->stockReturnItemDetails($user,$item);

        return $this->render('InventoryBundle:BranchReport:stockDetails.html.twig', array(
            'entity'          => $item,
            'entities'      => $entities,
            'stockSalesReturnItem' => $stockSalesReturnItem,
            'stockOngoingItem' => $stockOngoingItem,
            'stockSalesItem' => $stockSalesItem,
            'stockReturnItem' => $stockReturnItem,
        ));

    }

    public function branchItemSalesAction(Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entities = $em->getRepository('InventoryBundle:Delivery')->stockItemDetails($user,$item);
        $totalQnt = $em->getRepository('InventoryBundle:Delivery')->stockReceiveSingleItem($user,$item);
        $stockSalesItem =  $em->getRepository('InventoryBundle:Delivery')->stockSalesItemDetails($user,$item);
        $stockSalesReturnItem =  $em->getRepository('InventoryBundle:Delivery')->stockSalesReturnItemDetails($user,$item);
        $stockOngoingItem =  $em->getRepository('InventoryBundle:Delivery')->stockOngoingItemDetails($user,$item);
        $stockReturnItem =  $em->getRepository('InventoryBundle:Delivery')->stockReturnItemDetails($user,$item);

        return $this->render('InventoryBundle:BranchReport:itemSalesDetails.html.twig', array(
            'entity'            => $item,
            'totalQnt'          => $totalQnt,
            'entities'          => $entities,
            'stockReturnSalesItem' => $stockSalesReturnItem,
            'stockOngoingItem'  => $stockOngoingItem,
            'stockSalesItem'    => $stockSalesItem,
            'stockReturnItem'   => $stockReturnItem,
        ));

    }

    public function branchStockItemDetailsAction(Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entities = $em->getRepository('InventoryBundle:Delivery')->stockItemDetails($user,$item);
        $stockSalesItem =  $em->getRepository('InventoryBundle:Delivery')->stockSalesItemDetails($user,$item);
        $stockSalesReturnItem =  $em->getRepository('InventoryBundle:Delivery')->stockSalesReturnItemDetails($user,$item);
        $stockOngoingItem =  $em->getRepository('InventoryBundle:Delivery')->stockOngoingItemDetails($user,$item);
        $stockReturnItem =  $em->getRepository('InventoryBundle:Delivery')->stockReturnItemDetails($user,$item);

        return $this->render('InventoryBundle:BranchReport:itemStockDetails.html.twig', array(
            'entity'          => $item,
            'entities'      => $entities,
            'stockSalesReturnItem' => $stockSalesReturnItem,
            'stockOngoingItem' => $stockOngoingItem,
            'stockSalesItem' => $stockSalesItem,
            'stockReturnItem' => $stockReturnItem,
        ));

    }

    public function branchSalesItemAction()
    {
        return $this->render('InventoryBundle:BranchReport:salesItem.html.twig', array(
            'entity'            => '',
            'entities'          => '',
            'stockOngoingItem'  => '',
            'stockSalesItem'    => '',
        ));

    }


    public function branchSalesItemSearchAction(Item $item )
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $totalQnt = $em->getRepository('InventoryBundle:Delivery')->stockReceiveSingleItem($user,$item);
        $entities =  $em->getRepository('InventoryBundle:Delivery')->stockSalesItemHistory($user,$item);
        $stockSalesReturnItem =  $em->getRepository('InventoryBundle:Delivery')->stockSalesReturnItemHistory($user,$item);

        return $this->render('InventoryBundle:BranchReport:salesItemDetails.html.twig', array(
            'entity'                            => $item,
            'totalQnt'                          => $totalQnt,
            'entities'                          => $entities,
            'stockSalesReturnItem'              => $stockSalesReturnItem
        ));

    }

    public function branchSalesItemBarcodeAction($barcode)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $purchaseItem = $em->getRepository('InventoryBundle:Delivery')->barcodeWithItem($user, $barcode);
        $totalQnt = $em->getRepository('InventoryBundle:Delivery')->stockReceiveSingleItem($user, $purchaseItem->getItem(),$barcode);
        $entities =  $em->getRepository('InventoryBundle:Delivery')->stockSalesItemHistory($user,$purchaseItem->getItem(),$barcode);
        $stockSalesReturnItem =  $em->getRepository('InventoryBundle:Delivery')->stockSalesReturnItemHistory($user,$purchaseItem->getItem(),$barcode);

        return $this->render('InventoryBundle:BranchReport:salesItemDetails.html.twig', array(
            'entity'                            =>  $purchaseItem->getItem(),
            'totalQnt'                          => $totalQnt,
            'entities'                          => $entities,
            'stockSalesReturnItem'              => $stockSalesReturnItem
        ));

    }

    public function branchWiseBarcodeProductStockAction( $id){



        $em = $this->getDoctrine()->getManager();
        $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->find($id);
        $user = $this->getUser();
        $branches = $user->getGlobalOption()->getBranches();
        $branchWiseReceiveItem =  $em->getRepository('InventoryBundle:Delivery')->branchWiseReceiveItem($user , $purchaseItem->getId());
        $branchWiseSalesItem =  $em->getRepository('InventoryBundle:Delivery')->branchWiseSalesItem($user , $purchaseItem);
        $branchWiseSalesReturnItem =  $em->getRepository('InventoryBundle:Delivery')->branchWiseSalesReturnItem($user, $purchaseItem);
        $branchWiseOngoingSalesItem =  $em->getRepository('InventoryBundle:Delivery')->branchWiseOngoingSalesItem($user, $purchaseItem);
        $ongoingSalesItem =  $em->getRepository('InventoryBundle:Delivery')->ongoingSalesItem($user, $purchaseItem);
        $branchWiseDeliveryReturnItem =  $em->getRepository('InventoryBundle:Delivery')->branchWiseDeliveryReturnItem($user,$purchaseItem);
        return $this->render('InventoryBundle:BranchReport:branchWiseBarcodeProductStock.html.twig', array(
            'entity'          => $purchaseItem,
            'branches'          => $branches,
            'branchWiseReceiveItem' => $branchWiseReceiveItem,
            'branchWiseSalesItem' => $branchWiseSalesItem,
            'branchWiseSalesReturnItem' => $branchWiseSalesReturnItem,
            'ongoingSalesItem' => $ongoingSalesItem,
            'branchWiseOngoingSalesItem' => $branchWiseOngoingSalesItem,
            'branchWiseDeliveryReturnItem' => $branchWiseDeliveryReturnItem,
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
