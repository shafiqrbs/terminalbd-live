<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\BusinessBundle\Entity\WearHouse;
use Appstore\Bundle\BusinessBundle\Form\PurchaseOpeningType;
use Appstore\Bundle\BusinessBundle\Form\PurchaseType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class PurchaseController extends Controller
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
     * Lists all Vendor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $data = $_REQUEST;
	    $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->findWithSearch($this->getUser(),$data);
        $pagination = $this->paginate($entities);
        return $this->render('BusinessBundle:Purchase:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * Lists all Vendor entities.
     *
     */
    public function purchaseItemAction()
    {
        $em = $this->getDoctrine()->getManager();
	    $data = $_REQUEST;
	    $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->findWithSearch($this->getUser(),$data);
        $pagination = $this->paginate($entities);
	    $view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'index';
        if(empty($data['pdf'])){
            return $this->render("BusinessBundle:Purchase/PurchaseItem:{$view}.html.twig", array(
                'entities' => $pagination,
                'searchForm' => $data,
            ));

        }else{
            $html = $this->renderView(
                'BusinessBundle:Purchase/PurchaseItem:signPdf.html.twig', array(
                    'globalOption'          => $this->getUser()->getGlobalOption(),
                    'entities' => $pagination,
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'purchase-item.pdf');
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



    /**
     * Creates a new Vendor entity.
     *
     */
    public function createAction(Request $request)
    {
       
    }


    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new BusinessPurchase();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity->setBusinessConfig($config);
        $entity->setCreatedBy($this->getUser());
        $receiveDate = new \DateTime('now');
        $entity->setReceiveDate($receiveDate);
        $entity->setProcess('Created');
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('business_purchase_edit', array('id' => $entity->getId())));

    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $em->getRepository('BusinessBundle:BusinessPurchase')->findOneBy(array('businessConfig' => $config , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($config,$type = array('consumable','stock'));
	    $view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'new';
        $vendors = $this->getDoctrine()->getRepository(AccountVendor::class)->findBy(array('globalOption'=>$this->getUser()->getGlobalOption()));
        $wearhouses = $this->getDoctrine()->getRepository(WearHouse::class)->findBy(array('businessConfig' => $config),array('name'=>'ASC'));
	    return $this->render("BusinessBundle:Purchase:{$view}.html.twig", array(
            'entity' => $entity,
            'config' => $config,
            'vendors' => $vendors,
            'wearhouses' => $wearhouses,
            'id' => 'purchase',
            'particulars' => $particulars,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessPurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseType($globalOption), $entity, array(
            'action' => $this->generateUrl('business_purchase_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function returnResultData(BusinessPurchase $invoice, $msg=''){
        $config = $invoice->getBusinessConfig();
        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
        $netTotal = $invoice->getNetTotal() > 0 ? $invoice->getNetTotal() : 0;
        $due = $invoice->getDue() > 0 ? $invoice->getDue() : 0;
        $discount = $invoice->getDiscount() > 0 ? $invoice->getDiscount() : 0;
        $view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'new';

        $html = $this->renderView(
            "BusinessBundle:Purchase/item:{$view}.html.twig", array(
                'entity' => $invoice,
            )
        );
        $data = array(
            'subTotal' => $subTotal,
            'netTotal' => $netTotal,
            'due' => $due,
            'discount' => $discount,
            'invoiceParticulars' => $html ,
            'msg' => $msg ,
            'success' => 'success'
        );
        return $data;

    }

    public function particularSearchAction(BusinessParticular $particular)
    {
	    $unit = !empty($particular->getUnit() && !empty($particular->getUnit()->getName())) ? $particular->getUnit()->getName():'Unit';
	    return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getSalesPrice(), 'purchasePrice'=> $particular->getPurchasePrice(), 'quantity'=> 1 , 'unit'=> $unit)));
    }

    public function updateItemAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $request->request->get('purchaseItem');
        $quantity = empty($request->request->get('quantity'))?0 :$request->request->get('quantity');
        $price = floatval(empty($request->request->get('price')) ? 0 : $request->request->get('price'));
        $bonusQty = empty($request->request->get('bonusQuantity')) ? 0 : $request->request->get('bonusQuantity');

        /* @var $purchaseItem BusinessPurchaseItem */

        $purchaseItem = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->find($item);
        $purchaseItem->setQuantity($quantity);
        $purchaseItem->setPurchasePrice($price);
        $purchaseItem->setPurchaseSubTotal($price * $quantity);
        $purchaseItem->setBonusQuantity($bonusQty);
        $em->persist($purchaseItem);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($purchaseItem->getBusinessPurchase());
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }


    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */
    public function addBarcodeProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $wearhouse = '';
        $data = $_REQUEST;
        $id = $data['invoice'];
        $barcode = $data['barcode'];
        $invoice =  $this->getDoctrine()->getRepository(BusinessPurchase::class)->find($id);
        $stock = $this->getDoctrine()->getRepository(BusinessParticular::class)->findOneBy(array('businessConfig' => $invoice->getBusinessConfig(),'particularCode'=>$barcode));
        $particularId = $stock->getId();
        $price = $stock->getPrice();
        $quantity = 1;
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price,'wearhouse' => $wearhouse);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->insertPurchaseItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    public function addParticularAction(Request $request, BusinessPurchase $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $wearhouse = (isset($data['wearhouse']) and $data['wearhouse']) ? $data['wearhouse']:'';
        if($invoice->getBusinessConfig()->isUnitPrice() == 1){
            $price = $request->request->get('unitPrice');
        }else{
            $price = $request->request->get('purchasePrice');
        }
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price,'wearhouse' => $wearhouse);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->insertPurchaseItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    public function sawmillParticularAction(Request $request, BusinessPurchase $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $width = $request->request->get('width');
        $height = $request->request->get('height');
        $length = $request->request->get('length');
        $particularType = $request->request->get('particularType');
        $price = $request->request->get('purchasePrice');
        $invoiceItems = array('particularId' => $particularId ,'particularType' => $particularType, 'width' => $width,'height' => $height,'length' => $length,'price' => $price);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->insertSawmillPurchaseItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    public function insertPurchaseDistributionItemsAction(Request $request, BusinessPurchase $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $bonusQuantity = $request->request->get('bonusQuantity');
        if($invoice->getBusinessConfig()->isUnitPrice() == 1){
            $price = $request->request->get('unitPrice');
        }else{
            $price = $request->request->get('purchasePrice');
        }
        $invoiceItems = array('particularId' => $particularId ,'quantity' => $quantity,'bonusQuantity' => $bonusQuantity,'price' => $price);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->insertPurchaseDistributionItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
    }

    public function signParticularAction(Request $request, BusinessPurchase $invoice)
    {
        $em = $this->getDoctrine()->getManager();
	    $invoiceItems = $request->request->all();
	    $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->insertSignPurchaseItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    public function invoiceParticularDeleteAction(BusinessPurchase $invoice, BusinessPurchaseItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    /**
     * @Secure(roles="ROLE_BUSINESS")
     */
    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discountType = $request->request->get('discountType');
        $discountCal = (float)$request->request->get('discount');
        $invoice = $request->request->get('invoice');

        /* @var $entity BusinessPurchase */

        $entity = $em->getRepository('BusinessBundle:BusinessPurchase')->find($invoice);
        $subTotal = $entity->getSubTotal();
        if($discountType == 'flat'){
            $total = ($subTotal  - $discountCal);
            $discount = $discountCal;
        }else{
            $discount = ($subTotal * $discountCal)/100;
            $total = ($subTotal  - $discount);
        }
        $vat = 0;
        if($total > $discount ){
            $entity->setDiscountType($discountType);
            $entity->setDiscountCalculation($discountCal);
	        $entity->setDiscount(round($discount));
	        $entity->setNetTotal(round($total + $vat));
	        $entity->setDue(round($entity->getNetTotal()));
        }else{
			$entity->setDiscountType('flat');
			$entity->setDiscountCalculation(0);
			$entity->setDiscount(round($discount));
			$entity->setNetTotal(round($total + $vat));
			$entity->setDue(round($entity->getNetTotal()));
		}
	    $em->flush();
    //    $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($entity);
        $msg = 'Discount successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));

    }

    public function updateAction(Request $request, BusinessPurchase $entity)
    {
        $em = $this->getDoctrine()->getManager();
	    $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $data = $request->request->all();
            $entity->setProcess('Done');
            $entity->setNetTotal(round($entity->getNetTotal()));
            $entity->setDue($entity->getNetTotal() - $entity->getPayment());
            $entity->upload();
            if(isset($data['created']) and !empty($data['created'])){
                $created = $data['created'];
                $date = new \DateTime($created);
                $entity->setCreated($date);
                $entity->setUpdated($date);
            }
            $em->flush();
            return $this->redirect($this->generateUrl('business_purchase_show', array('id' => $entity->getId())));
        }
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($entity->getBusinessConfig(),$type = array('consumable','stock'));
	    $view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'new';
	    return $this->render("BusinessBundle:Purchase:{$view}.html.twig", array(
            'entity' => $entity,
            'particulars' => $particulars,
            'form' => $editForm->createView(),
        ));
    }


    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

	    $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $entity = $em->getRepository('BusinessBundle:BusinessPurchase')->findOneBy(array('businessConfig' => $config , 'id' => $id));


	    if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('BusinessBundle:Purchase:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction($id)
    {
        $em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $purchase = $em->getRepository('BusinessBundle:BusinessPurchase')->findOneBy(array('businessConfig' => $config , 'id' => $id));
	    if (!empty($purchase) and empty($purchase->getApprovedBy())) {
            $em = $this->getDoctrine()->getManager();
            $purchase->setProcess('Approved');
            $purchase->setApprovedBy($this->getUser());
            if($purchase->getPayment() === 0 ){
	            $purchase->setTransactionMethod(NULL);
	            $purchase->setAsInvestment(false);
            }
		    $em->flush();
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->updatePurchaseItemPrice($purchase);
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getPurchaseUpdateQnt($purchase);
            if($config->isStockHistory() == 1 ){
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->processInsertPurchaseItem($purchase);
            }
            if($purchase->getAsInvestment() == 1 and $purchase->getPayment() > 0 ){
			    $journal =  $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->insertAccountBusinessPurchaseJournal($purchase);
			    $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertAccountCash($journal,'Journal');
		    }
            $em->getRepository('AccountingBundle:AccountPurchase')->insertBusinessAccountPurchase($purchase);
            return new Response('success');
        } else {
            return new Response('failed');
        }

    }

    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction($id)
    {

    	$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->findOneBy(array('businessConfig' => $config , 'id' => $id));
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $this->getDoctrine()->getRepository(BusinessInvoiceParticular::class)->removePurchaseItem($entity);
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('business_purchase'));
    }

	public function itemApproveAction($id)
    {

    	$config = $this->getUser()->getGlobalOption()->getBusinessConfig()->getId();
	    $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->findOneBy(array('id' => $id));
        $em = $this->getDoctrine()->getManager();
        if ($entity->getBusinessPurchase()->getBusinessConfig()->getId() == $config ) {
	        $entity->setStatus(true);
	        $em->flush();
        }
        exit;
    }

	public function reverseAction($id)
	{

		/*
		 * Item Remove Total quantity
		 * Stock Details
		 * Purchase Item
		 * Purchase Vendor Item
		 * Purchase
		 * Account Purchase
		 * Account Journal
		 * Transaction
		 * Delete Journal & Account Purchase
		 */
		$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
		$purchase = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->findOneBy(array('businessConfig' => $config , 'id' => $id));

		set_time_limit(0);
		ignore_user_abort(true);

		$em = $this->getDoctrine()->getManager();
		if($purchase->getAsInvestment() == 1 ) {
			$this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->removeApprovedBusinessPurchaseJournal($purchase);
		}
		$this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountBusinessPurchaseReverse($purchase);
		$purchase->setIsReversed(true);
		$purchase->setProcess('Created');
		$purchase->setApprovedBy(NULL);
		$em->flush();
		$this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getPurchaseUpdateQnt($purchase);
        if($config->isStockHistory() == 1 ) {
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->processReversePurchaseItem($purchase);
        }
        $template = $this->get('twig')->render('BusinessBundle:Purchase:purchaseReverse.html.twig', array(
			'entity' => $purchase,
			'config' => $purchase->getBusinessConfig(),
		));
		$this->getDoctrine()->getRepository('BusinessBundle:BusinessReverse')->purchaseReverse($purchase, $template);
		return $this->redirect($this->generateUrl('business_purchase_edit',array('id' => $purchase->getId())));
	}


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

    }

    public function autoSearchAction(Request $request)
    {

    }

    public function searchVendorNameAction($vendor)
    {

    }

    public function printAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $em->getRepository('BusinessBundle:BusinessPurchase')->findOneBy(array('businessConfig' => $config , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('BusinessBundle:Purchase:print.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Lists all AccountSales entities.
     *
     */
    public function vendorOpeningAction(Request $request)
    {

        $data = explode( ',', $request->cookies->get( 'barcodes' ) );
        if ( is_null( $data ) ) {
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }
        $amount = 0;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig()->getId();
        $amount = $this->getDoctrine()->getRepository("BusinessBundle:BusinessParticular")->sumOpeningQuantity($config);
        $entity = new BusinessPurchase();
        $editForm = $this->createOpeningForm($entity);
        return $this->render('BusinessBundle:Purchase:opening.html.twig', array(
            'entity' => $entity,
            'amount' => $amount,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param BusinessPurchase $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createOpeningForm(BusinessPurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseOpeningType($globalOption), $entity, array(
            'action' => $this->generateUrl('business_purchase_opening_create'),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchaseForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function openingCreateAction(Request $request)
    {
       /* $data = explode( ',', $request->cookies->get( 'barcodes' ) );
        $ids =  $request->cookies->get( 'barcodes');
        if ( is_null( $data ) ) {
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }*/
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $amount = $this->getDoctrine()->getRepository("BusinessBundle:BusinessParticular")->sumOpeningQuantity($config->getId());
        $em = $this->getDoctrine()->getManager();
        $entity = new BusinessPurchase();
        $form = $this->createOpeningForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entity->setBusinessConfig($config);
            $method = $em->getRepository('SettingToolBundle:TransactionMethod')->findOneBy(array('slug'=>'cash'));
            $entity->setTransactionMethod($method);
            $entity->setSubTotal($amount);
            $entity->setNetTotal($amount);
            $entity->setPayment($amount);
            $entity->setMode("Opening");
            $entity->setProcess("Approved");
            $entity->setApprovedBy($this->getUser());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            $em->getRepository('AccountingBundle:AccountPurchase')->insertBusinessAccountPurchase($entity);
            $this->getDoctrine()->getRepository("BusinessBundle:BusinessParticular")->updateOpeningQuantity($config->getId());
            return $this->redirect($this->generateUrl('business_stock'));
        }
        $this->get('session')->getFlashBag()->add(
            'error',"Required field does not input"
        );
        return $this->render('BusinessBundle:Purchase:opening.html.twig', array(
            'entity' => $entity,
            'amount' => $amount,
            'form' => $form->createView(),
        ));
    }


}
