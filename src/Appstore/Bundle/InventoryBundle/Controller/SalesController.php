<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\InventoryBundle\Entity\SalesItemSerial;
use Appstore\Bundle\InventoryBundle\Form\SalesType;
use Appstore\Bundle\InventoryBundle\Service\PosItemManager;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Mike42\Escpos\Printer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sales controller.
 *
 */
class SalesController extends Controller
{

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $entities = $em->getRepository('InventoryBundle:Sales')->salesLists( $user , $mode = 'pos', $data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        $config = $user->getGlobalOption()->getInventoryConfig();
        return $this->render('InventoryBundle:Sales:index.html.twig', array(
            'config' => $config,
            'entities' => $pagination,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */


    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Sales();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $globalOption = $this->getUser()->getGlobalOption();
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($globalOption);
        $entity->setCustomer($customer);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setSalesMode('pos');
        $entity->setPaymentStatus('Pending');
        $entity->setProcess('In-progress');
        $entity->setInventoryConfig($inventory);
        $entity->setSalesBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_sales_edit', array('code' => $entity->getInvoice())));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function editAction($code)
    {

        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $em->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'invoice' => $code));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        $editForm = $this->createEditForm($entity);
        $todaySales = $em->getRepository('InventoryBundle:Sales')->todaySales($this->getUser(),$mode = 'pos');
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($this->getUser(),$mode = 'pos');

        return $this->render('InventoryBundle:SalesManual:sales.html.twig', array(
            'entity' => $entity,
            'todaySales' => $todaySales,
            'todaySalesOverview' => $todaySalesOverview,
            'form' => $editForm->createView(),
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:SalesItem')->salesItems($inventory, $data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Sales:salesItem.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function searchAction(Request $request)
    {
        $purchaseItem = "";
        $em = $this->getDoctrine()->getManager();
        $salesInvoice = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $sales = $em->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory,'id'=>$salesInvoice));
        $serialItem = $em->getRepository('InventoryBundle:PurchaseItemSerial')->returnPurchaseItemSerialDetails($inventory, trim($barcode));
        $purchaseItem = "";
        $itemStock = "";
        if(empty($serialItem)){
            $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->returnPurchaseItemDetails($inventory,trim($barcode));
        }
        if(empty($serialItem) and empty($purchaseItem)){
            $itemStock = $em->getRepository('InventoryBundle:Item')->returnStockItemDetails($inventory,trim($barcode));
        }
        if ($serialItem) {
           $pItem = $serialItem->getPurchaseItem();
           $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItemSerial')->findOneBy(array('purchaseItemSerial' => $serialItem));
           if (empty($checkQuantity)) {
                $sales = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertSerialSalesItems($sales, $pItem,$serialItem);
                $salesEntity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
                $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';
            } else {
                $salesEntity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
                $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
            }
            $data = $this->returnResultData($salesEntity, $msg);
            return new Response(json_encode($data));

        }elseif($purchaseItem) {
            /* @var $purchaseItem PurchaseItem */
            $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkPurchaseItemSalesQuantity($purchaseItem);
            $itemStock = $purchaseItem->getQuantity();
            if(!empty($purchaseItem) and $itemStock > 0 and  $itemStock > $checkQuantity) {
                $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertSalesPurchaseItems($sales, $purchaseItem);
                $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
                $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';
            } else {
                $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
                $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
            }
            $data = $this->returnResultData($sales,$msg);
            return new Response(json_encode($data));
        }elseif($itemStock) {
            /* @var $itemStock Item */
            $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkSalesItemQuantity($itemStock);
            if($itemStock->getRemainingQnt() > 0 and  $itemStock->getRemainingQnt() > $checkQuantity) {
                $data = array('quantity'=>1,'salesPrice'=>$itemStock->getSalesPrice(),'purchasePrice'=>$itemStock->getPurchasePrice());
                $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertSalesItems($sales, $itemStock,$data);
                $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
                $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';
            } else {
                $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
                $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
            }
            $data = $this->returnResultData($sales,$msg);
            return new Response(json_encode($data));
        }
        return new Response(json_encode(array('status'=>'in-valid')));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function searchPurchaseItemAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sales = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $sales = $em->getRepository('InventoryBundle:Sales')->find($sales);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->returnPurchaseItemDetails($inventory, $barcode);
        $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkSalesQuantity($purchaseItem);
        $itemStock = $purchaseItem->getItemStock();

        if (!empty($purchaseItem) && $itemStock > $checkQuantity) {

            $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertSalesItems($sales, $purchaseItem);
            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $msg = 'Product added successfully';

        } else {

            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
        }
        $data = $this->returnResultData($sales,$msg);
        return new Response(json_encode($data));

    }

    public function returnResultData($sales,$msg=''){

        //$salesItems = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->getSalesItems($sales);
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $config,'id' => $sales));
        /*
         * $salesItems = $this->renderView('InventoryBundle:Sales:item.html.twig', array(
            'entity' => $entity
        ));
        */
        $salesItems = $this->renderView('InventoryBundle:Sales:sales-pos-item.html.twig', array(
            'entity' => $entity
        ));
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $due = $entity->getDue();
        $vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
            'msg' => $msg,
            'salesSubTotal' => $subTotal,
            'salesTotal' => $netTotal,
            'payment' => $payment ,
            'due' => $due,
            'discount' => $discount,
            'vat' => $vat,
            'salesItems' => $salesItems ,
            'success' => 'success'
        );
        return $data;

    }

    public function returnSalesDataAction($msg=''){

        //$salesItems = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->getSalesItems($sales);
        $sales = $_REQUEST['id'];
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $config,'id' => $sales));
        $salesItems = $this->renderView('InventoryBundle:Sales:sales-pos-item.html.twig', array(
            'entity' => $entity
        ));
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $due = $entity->getDue();
        $vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
            'msg' => $msg,
            'salesSubTotal' => $subTotal,
            'salesTotal' => $netTotal,
            'payment' => $payment ,
            'due' => $due,
            'discount' => $discount,
            'vat' => $vat,
            'salesItems' => $salesItems ,
            'success' => 'success'
        );
        return new Response(json_encode($data));


    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

	public function salesDiscountUpdateAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$discountType = $request->request->get('discountType');
		$discountCal = (float)$request->request->get('discount');
		$sales = $request->request->get('sales');
		$sales = $em->getRepository('InventoryBundle:Sales')->find($sales);
		$subTotal = $sales->getSubTotal();
		$total = 0;
		if($discountType == 'Flat' and $discountCal > 0){
			$total = ($subTotal  - $discountCal);
			$discount = $discountCal;
		}elseif($discountType == 'Percentage' and $discountCal > 0){
			$discount = ($subTotal * $discountCal)/100;
			$total = ($subTotal  - $discount);
		}
		$vat = 0;
		if($total > 0  and $total > $discountCal ){
			if ($sales->getInventoryConfig()->getVatEnable() == 1 && $sales->getInventoryConfig()->getVatPercentage() > 0) {
				$vat = $em->getRepository('InventoryBundle:Sales')->getCulculationVat($sales,$total);
				$sales->setVat($vat);
			}
			$sales->setDiscountType($discountType);
			$sales->setDiscountCalculation($discountCal);
			$sales->setDiscount(round($discount));
			$sales->setTotal(round($total + $vat));
			$sales->setDue(round($sales->getTotal()));

		}else{

			if ($sales->getInventoryConfig()->getVatEnable() == 1 && $sales->getInventoryConfig()->getVatPercentage() > 0) {
				$vat = $em->getRepository('InventoryBundle:Sales')->getCulculationVat($sales->getSubTotal(),$total);
				$sales->setVat($vat);
			}
			$sales->setDiscountType('Flat');
			$sales->setDiscountCalculation(0);
			$sales->setDiscount(0);
			$sales->setTotal(round($subTotal + $vat));
			$sales->setDue(round($sales->getTotal()));
		}
		$em->persist($sales);
		$em->flush();
		$data = $this->returnResultData($sales);
		return new Response(json_encode($data));
		exit;
	}


	/**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $salesItemId = $request->request->get('salesItemId');
        $quantity = $request->request->get('quantity');
        $salesPrice = $request->request->get('salesPrice');
        $customPrice = $request->request->get('customPrice');

        $salesItem = $em->getRepository('InventoryBundle:SalesItem')->find($salesItemId);
        /* @var $salesItem SalesItem */
        if($salesItem->getPurchaseItem()){
            $checkOngoingSalesQuantity = $this->getDoctrine()->getRepository(SalesItem::class)->checkPurchaseItemSalesQuantity($salesItem->getPurchaseItem());
            $itemStock = $salesItem->getPurchaseItem()->getQuantity();
        }else{
            $checkOngoingSalesQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkSalesItemQuantity($salesItem->getItem());
            $itemStock = $salesItem->getItem()->getRemainingQnt();
        }
        $currentRemainingQnt = ($itemStock - ($checkOngoingSalesQuantity + $quantity)) ;
        $sales = $salesItem->getSales();
        $salesItem->setSalesPrice($salesPrice);
        if (!empty($customPrice)) {
            $salesItem->setCustomPrice($customPrice);
        }
        if(!empty($salesItem) && $itemStock > 0 && $currentRemainingQnt >= 0 ){
            $salesItem->setQuantity($quantity);
            $salesItem->setSubTotal($salesItem->getQuantity() * $salesPrice);
            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($salesItem->getSales());
            $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';
        } else {
            $salesItem->setSubTotal($salesItem->getQuantity() * $salesPrice);
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
        }
        $em->persist($salesItem);
        $em->flush();
        $data = $this->returnResultData($sales,$msg);
        return new Response(json_encode($data));

    }


    /**
     * Finds and displays a Sales entity.
     *
     */
    public function showAction(Sales $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();
        if ($inventory == $entity->getInventoryConfig()->getId()) {
            return $this->render('InventoryBundle:Sales:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('inventory_sales'));
        }

    }

    public function resetAction(Sales $sales)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($sales->getSalesItems() as $salesItem ) {
            $em->remove($salesItem);
        }
        $em->flush();
        $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
        return $this->redirect($this->generateUrl('inventory_sales_edit', array('code' => $sales->getInvoice())));

    }

    public function salesInlineMobileUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:Sales')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['value']);
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findExistingCustomer($entity, $mobile);
        $entity->setCustomer($customer);
        $entity->setMobile($mobile);
        $em->flush();
        exit;

    }

    /**
     * Creates a form to edit a Sales entity.wq
     *
     * @param Sales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Sales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new SalesType($globalOption), $entity, array(
            'action' => $this->generateUrl('inventory_sales_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'id' => 'posForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function updateAction(Request $request, Sales $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid() and $data['paymentTotal'] > 0 ) {
            if ($entity->getInventoryConfig()->getVatEnable() == 1 && $entity->getInventoryConfig()->getVatPercentage() > 0) {
                $vat = $em->getRepository('InventoryBundle:Sales')->getCulculationVat($entity,$data['paymentTotal']);
                $entity->setVat($vat);
            }
            $due = $data['dueAmount'] == '' ? 0 :$data['dueAmount'];
            $entity->setDue($due);
            $entity->setTotal($data['paymentTotal']);
            $entity->setPayment($entity->getTotal() - $entity->getDue());

            if ($entity->getTotal() <= $data['paymentAmount']) {
                $entity->setPaymentStatus('Paid');
            } else if ($entity->getTotal() - $entity->getDue()) {
                $entity->setPaymentStatus('Due');
            }
            $entity->setProcess('Done');
            if (empty($data['sales']['salesBy'])) {
                $entity->setSalesBy($this->getUser());
            }
            if ($entity->getTransactionMethod()->getId() != 4) {
                $entity->setApprovedBy($this->getUser());
            }
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            $em->flush();

            if (in_array('CustomerSales', $entity->getInventoryConfig()->getDeliveryProcess())) {
                if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch('setting_tool.post.posorder_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
                }
            }
            if ($entity->getTransactionMethod()->getId() == 4) {

                return $this->redirect($this->generateUrl('inventory_sales_show', array('id' => $entity->getId())));

            }else{

                $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
                $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
               // $em->getRepository('InventoryBundle:GoodsItem')->updateEcommerceItem($entity);
                $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
              //  $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
                return $this->redirect($this->generateUrl('inventory_sales_new'));
            }
        }

        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $todaySales = $em->getRepository('InventoryBundle:Sales')->todaySales($inventory,$mode='pos');
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($inventory , $mode='pos');
        return $this->render('InventoryBundle:Sales:pos.html.twig', array(
            'entity' => $entity,
            'todaySales' => $todaySales,
            'todaySalesOverview' => $todaySalesOverview,
            'form' => $editForm->createView(),
        ));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_APPROVE")
     */

    public function approveAction(Sales $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPaymentStatus('Paid');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
            $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
            $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
            return new Response('success');
        } else {
            return new Response('failed');
        }

    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function deleteAction(Sales $sales)
    {

        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        if (!$sales and $config->getId() != $sales->getInventoryConfig()->getId()) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        if($sales->getSalesItems()){
            $template = $this->get('twig')->render('InventoryBundle:Reverse:salesReverse.html.twig', array(
                'entity' => $sales,
                'inventoryConfig' => $config,
            ));
            foreach ($sales->getSalesItems() as $item){
                $this->getDoctrine()->getRepository(SalesItemSerial::class)->deleteSalesItemSerial($item);
            }
            $exist = $this->getDoctrine()->getRepository(SalesItem::class)->findBy(array('sales' => $sales->getId()));
            if($exist){
                $em->remove($exist);
                $em->flush();
            }
            $sales->setContent($template);
            $sales->setProcess("Delete");
            $sales->setIsDelete(true);
            $em->persist($sales);
            $em->flush();
        }else{
            $em->remove($sales);
            $em->flush();
        }
        return new Response('success');
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function itemSerialNoUpdateAction(SalesItem  $salesItem)
    {
        $serial = $_REQUEST['serial'];
        $em = $this->getDoctrine()->getManager();
        if(!empty($serial)){
            $ser = implode(",",$serial);
	        $salesItem->setSerialNo($ser);
	        $salesItem->setAssuranceType($salesItem->getPurchaseItem()->getAssuranceType());
	        $salesItem->setAssuranceFromVendor($salesItem->getPurchaseItem()->getAssuranceFromVendor());
	        $salesItem->setAssuranceToCustomer($salesItem->getPurchaseItem()->getAssuranceToCustomer());
	        $salesItem->setExpiredDate($salesItem->getPurchaseItem()->getExpiredDate());
            $em->persist($salesItem);
	        $em->flush();
        }else{
            $salesItem->setSerialNo(NULL);
            $salesItem->setAssuranceType(NULL);
            $salesItem->setAssuranceFromVendor(NULL);
            $salesItem->setAssuranceToCustomer(NULL);
            $salesItem->setExpiredDate(NULL);
            $em->persist($salesItem);
            $em->flush();
        }
        return new Response('success');
    }

    /**
     * Deletes a SalesItem entity.
     *
     */
    public function itemDeleteAction(Sales $sales, $salesItem)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:SalesItem')->find($salesItem);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
       // $this->getDoctrine()->getRepository(SalesItemSerial::class)->deleteSalesItemSerial($entity);
        $em->remove($entity);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
        $data = $this->returnResultData($invoice);
        return new Response(json_encode($data));

    }

    public function itemPurchaseDetailsAction(Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        $item = $request->request->get('item');
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $customer = isset($_REQUEST['customer']) ? $_REQUEST['customer'] : '';

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        $device = '';
        if( $detect->isMobile() || $detect->isTablet() ) {
            $device = 'mobile' ;
        }
        $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->itemPurchaseDetails($securityContext,$inventory, $item, $customer, $device);
        return new Response($data);
    }

    public function branchStockItemDetailsAction(Item $item)
    {
        $user = $this->getUser();
        $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->itemDeliveryPurchaseDetails($user,$item);
        return new Response($data);
    }

    public function getBarcode($invoice)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($invoice);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(34);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,' . $code . '" />';
        return $data;
    }

    public function salesInlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:Sales')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $entity->setCourierInvoice($data['value']);
        $em->flush();
        exit;

    }

    public function salesInlineProcessUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:Sales')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        if ($data['value'] == 'Done' or $data['value'] == 'Returned'){
            $entity->setProcess($data['value']);
        }elseif (!empty($entity->getCourierInvoice()) and $data['value'] == 'Courier'){
            $entity->setProcess($data['value']);
        }
        $em->flush();
        if($entity->getProcess() == 'Courier'){
            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.courier_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }
        if($entity->getProcess() == 'Done' or $entity->getProcess() == 'Delivered' ){
            $this->approvedOrder($entity);
            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.process_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }elseif($entity->getProcess() == 'Returned'){
            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.process_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }
        exit;

    }

    public function approvedOrder(Sales $entity)
    {
        if (!empty($entity)) {

            $em = $this->getDoctrine()->getManager();

            $entity->setPaymentStatus('Paid');
            $entity->setProcess('Done');
            $entity->setPayment($entity->getTotal());
            $entity->setDue(0);
            $entity->setApprovedBy($this->getUser());
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            $em->flush();
            $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
            $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
            $em->getRepository('InventoryBundle:GoodsItem')->updateEcommerceItem($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
           // $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

    public function returnCancelOrder(Sales $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPaymentStatus('Cancel');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

    public function salesInvoiceSearchAction()
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
        exit;
    }

    public function searchSalesInvoiceNameAction($invoice)
    {
        return new JsonResponse(array(
            'id'        => $invoice,
            'text'      => $invoice
        ));
    }

    public function salesSelectAction()
    {
        $items  = array();
        $items[]= array('value' => 'In-progress','text'=>'In-progress');
        $items[]= array('value' => 'Courier','text'=>'Courier');
        $items[]= array('value' => 'Delivered','text'=>'Delivered');
        $items[]= array('value' => 'Done','text'=>'Done');
        $items[]= array('value' => 'Returned','text'=>'Returned');
        $items[]= array('value' => 'Canceled','text'=>'Canceled');
        return new JsonResponse($items);
    }

    public function invoicePrintAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $em->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'invoice' => $invoice));
        $barcode = $this->getBarcode($entity->getInvoice());
        $totalAmount = ( $entity->getTotal() + $entity->getDeliveryCharge());
        $inWard = $this->get('settong.toolManageRepo')->intToWords($totalAmount);
        if($inventory->isCustomPrint() == 1){
            $print = $this->getUser()->getGlobalOption()->getSlug();
        }else{
            $print = 'invoice';
        }
        return $this->render('InventoryBundle:SalesPrint:invoice.html.twig', array(
            'entity'      => $entity,
            'inventory'   => $inventory,
            'barcode'     => $barcode,
            'inWard'      => $inWard,
        ));
    }

    public function chalanPrintAction($invoice)
    {

        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $config,'invoice' => $invoice));
        $barcode = $this->getBarcode($entity->getInvoice());
        $totalAmount = ( $entity->getTotal() + $entity->getDeliveryCharge());
        $inWard = $this->get('settong.toolManageRepo')->intToWords($totalAmount);

        return $this->render('InventoryBundle:SalesPrint:chalan.html.twig', array(
            'entity'      => $entity,
            'barcode'     => $barcode,
            'inWard'     => $inWard,
        ));
    }

    public function printAction($code)
    {

        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();


        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'invoice' => $code));
        $option = $entity->getInventoryConfig()->getGlobalOption();
        $this->approvedOrder($entity);

        /** ===================Company Information=================================== */

        $address        = $inventory->getAddress();
        $vatRegNo       = $inventory->getVatRegNo();
        $companyName    = $option->getName();
        $mobile         = $option->getHotline();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $due                = $entity->getDue();
        $payment            = $entity->getPayment();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getSalesBy();

        /* Information for the receipt */

        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Add Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $grandTotal     = new PosItemManager('Net Payable: ','Tk.',number_format($total));
        $payment        = new PosItemManager('Received: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($due));


        /* Date is kept the same for testing */
        $date = date('d-m-Y h:i:s A');

        /* Name of shop */
        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        $printer -> text($address."\n");
        $printer -> selectPrintMode();
        $printer -> text($mobile."\n");
        $printer -> feed();

        /* Title of receipt */
        if(!empty($vatRegNo)){
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            if(!empty($vatRegNo)){
                $printer -> text("BIN No - ".$vatRegNo." Mushak - 6.3\n\n");
            }
        }
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer->setFont(Printer::FONT_B);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setEmphasis(true);
        $printer -> text("Invoice no. {$entity->getInvoice()}\n");
        $printer -> setEmphasis(false);
        $printer->setFont(Printer::FONT_B);
        $printer -> text("Date: {$date}\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text(new PosItemManager('Item Name', 'Qty', 'Amount'));
        $printer -> text("---------------------------------------------------------------\n");
        $i=1;
        foreach ( $entity->getSalesItems() as $row){
            $product = "{$i}. {$row->getPurchaseItem()->getBarcode()} - {$row->getItem()->getName()}";
            $printer -> text(new PosItemManager($product,$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> text("---------------------------------------------------------------\n");
        $printer -> text($subTotal);
        $printer -> setEmphasis(false);
        if($vat){
            $printer->text($vat);
            $printer -> setEmphasis(false);
        }
        if($discount){
            $printer->text($discount);
            $printer -> setEmphasis(false);
        }
        $printer -> text("---------------------------------------------------------------\n");
        $printer -> text($grandTotal);
        $printer -> text($payment);
        $printer -> text("---------------------------------------------------------------\n");
        if($entity->getDue() > 0){
            $printer -> text($due);
        }
        $printer->text("\n");
        $printer->text($transaction);
        $printer->selectPrintMode();
        $printer -> feed();
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales By: ".$salesBy."\n");
        $printer -> text("Thank you for shopping\n");
        if($website){
            $printer -> text("Please visit www.".$website."\n");
        }
        $printer -> text("Powered by - www.terminalbd.com - 01815-308090 \n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return new Response($response);

    }

    public function posPrint(Sales $entity){



        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();


        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $option = $entity->getInventoryConfig()->getGlobalOption();

        /** ===================Company Information=================================== */
        if(!empty($entity->getBranches())){

            $branch = $entity->getBranches();
            $branchName     = $branch->getName();
            $address1       = $branch->getAddress();
            $thana          = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getName():'';
            $district       = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }else{

            $address1       = $option->getContactPage()->getAddress1();
            $thana          = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getName():'';
            $district       = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }

        $vatRegNo       = $inventory->getVatRegNo();
        $companyName    = $option->getName();
        $mobile         = $option->getMobile();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $due                = $entity->getDue();
        $payment            = $entity->getPayment();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getSalesBy()->getProfile()->getName();;


        /** ===================Invoice Sales Item Information========================= */

        $i = 1;
        $items = array();
        foreach ( $entity->getSalesItems() as $row){
            $items[]  = new PosItemManager($i.'. '.$row->getItem()->getName() ,$row->getQuantity(),$row->getSubTotal());
        }

        /* Date is kept the same for testing */
        $date = date('l jS \of F Y h:i:s A');

        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        if(!empty($entity->getBranches())) {
            $printer->text($branchName . "\n");
        }else{
            $printer -> text($address."\n");
        }
        /* $printer -> text($mobile."\n");*/
        $printer -> feed();

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        if(!empty($vatRegNo)){
            $printer -> text("Vat Reg No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
        }
        $printer -> feed();
        /*
        if(!empty($mobile)){
            $printer -> text ( "----------------------------------" );
            $printer -> text("Mobile: ".$mobile. "\n");
            $printer -> setEmphasis(false);
        }
        */

        /* Information for the receipt */

        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Add Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $grandTotal     = new PosItemManager('Net Payable: ','Tk.',number_format($total));
        $payment        = new PosItemManager('Received: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($due));

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        $printer -> text("SALES INVOICE\n\n");
        $printer -> setEmphasis(false);

        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text(new PosItemManager('Item Code', 'Qnt', 'Amount'));
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);;
        $printer -> setEmphasis(false);
        $printer -> feed();
        $i=1;
        foreach ( $entity->getSalesItems() as $row){

            $printer -> setUnderline(Printer::UNDERLINE_NONE);
            $printer -> text( new PosItemManager($i.'. '.$row->getItem()->getName(),"",""));
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer -> text(new PosItemManager($row->getPurchaseItem()->getBarcode(),$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> feed();
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setEmphasis(true);
        $printer -> text ( "\n" );
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($subTotal);
        $printer -> setEmphasis(false);
        if($vat){
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text($vat);
            $printer->setEmphasis(false);
        }
        if($discount){
            $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer->text($discount);
            $printer -> setEmphasis(false);
            $printer -> text ( "\n" );
        }
        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($grandTotal);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);

        $printer->text("\n");
        $printer -> feed();
        $printer->text($transaction);
        $printer->selectPrintMode();


        /* Barcode Print */
        $printer->selectPrintMode ( Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH );
        $printer->text ( "\n" );
        $printer->selectPrintMode ();
        /*$printer->setBarcodeHeight (60);
        $hri = array (Printer::BARCODE_TEXT_BELOW => "");
        $printer -> feed();
        foreach ( $hri as $position => $caption){
            $printer->selectPrintMode ();
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer->text ($caption);
            $printer->setBarcodeTextPosition ( $position );
            $printer->barcode ($invoice , Printer::BARCODE_JAN13 );
            $printer->feed ();
        }*/
        /* Footer */

        $printer -> feed();
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales By: ".$salesBy."\n");
        $printer -> text("Thank you for shopping\n");
        if($website){
            $printer -> text("Please visit www.".$website."\n");
        }
        $printer -> text($date . "\n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return $response;


    }

}

