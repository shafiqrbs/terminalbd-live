<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 * StockItem controller.
 *
 */
class StockItemController extends Controller
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


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Item')->findWithSearch($inventory,$data);
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:StockItem:index.html.twig', array(
            'entities' => $pagination,
            'stockOverview' => $stockOverview,
            'searchForm' => $data,
        ));
    }

    public function itemShortListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Item')->findWithShortListSearch($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:StockItem:shortList.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function barcodeStockAction()
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $data = $_REQUEST;
        $branchStocks ='';
        $entities = $em->getRepository('InventoryBundle:PurchaseItem')->getPurchaseItems($user->getGlobalOption(),$data);
        $pagination = $this->paginate($entities);
        if($entities){
            $branchStocks = $em->getRepository('InventoryBundle:StockItem')->barcodeWiseStock($user,$pagination);
        }
        return $this->render('InventoryBundle:StockItem:barcodeStock.html.twig', array(
            'branchStocks'          => $branchStocks,
            'pagination'            => $pagination,
            'searchForm'            => $data,
        ));
    }

    public function indexResultsAction()
    {
        $datatable = $this->get('app.datatable.stockitem');
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }
    /**
     * Creates a new StockItem entity.
     *
     */

    /**
     * Finds and displays a Item entity.
     *
     */
    public function showAction(Item $item)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Item')->find($item);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        return $this->render('InventoryBundle:StockItem:show.html.twig', array(
            'entity'      => $item,
        ));
    }

    public function barcodeBranchStockAction()
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $data = $_REQUEST;
        $branchStocks ='';
        $entities = $em->getRepository('InventoryBundle:PurchaseItem')->getPurchaseItems($user->getGlobalOption(),$data);
        $pagination = $this->paginate($entities);
        if($entities){
            $branchStocks = $em->getRepository('InventoryBundle:StockItem')->barcodeWiseBranchItem($user,$pagination);
        }
        return $this->render('InventoryBundle:StockItem:barcodeWiseStock.html.twig', array(
            'branchStocks'          => $branchStocks,
            'pagination'            => $pagination,
            'searchForm'            => $data,
        ));

    }

    public function singleBarcodeBranchStockAction($barcode)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $data = array('barcode'=>$barcode);
        $branchStocks ='';
        $entities = $em->getRepository('InventoryBundle:PurchaseItem')->getPurchaseItems($user->getGlobalOption(),$data);
        if($entities){
            $purchaseItem = $entities->getArrayResult()[0] ;
            $branchStocks = $em->getRepository('InventoryBundle:StockItem')->singleBarcodeWiseBranchItem($user, $purchaseItem);
        }
        return new Response($branchStocks);
        exit;

    }


}
