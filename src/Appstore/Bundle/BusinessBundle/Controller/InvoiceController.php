<?php

namespace Appstore\Bundle\BusinessBundle\Controller;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturnItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\WearHouse;
use Appstore\Bundle\BusinessBundle\Form\InvoiceType;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Knp\Snappy\Pdf;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * BusinessInvoiceController controller.
 *
 */
class InvoiceController extends Controller
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
     * Lists all BusinessCategory entities.
     *
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     *
     */
    
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->invoiceLists( $config->getId(),$data);
        $pagination = $this->paginate($entities);
        $view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'new';
        return $this->render("BusinessBundle:Invoice/{$view}:index.html.twig", array(
            'entities' => $pagination,
            'salesTransactionOverview' => '',
            'previousSalesTransactionOverview' => '',
            'searchForm' => $data,
        ));
    }

    public function invoiceConditionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $global = $this->getUser()->getGlobalOption();
        $couriers = $this->getDoctrine()->getRepository('AccountingBundle:AccountCondition')->findBy(array('globalOption' => $global,'status'=>1),array('name'=>"ASC"));
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->invoiceConditionLists( $config->getId(),$data);
        $pagination = $this->paginate($entities);
        $view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'new';
        return $this->render("BusinessBundle:Invoice/{$view}:condition.html.twig", array(
            'entities' => $pagination,
            'couriers' => $couriers,
            'salesTransactionOverview' => '',
            'previousSalesTransactionOverview' => '',
            'searchForm' => $data,
        ));

    }

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new BusinessInvoice();
        $option = $this->getUser()->getGlobalOption();
        $config = $option->getBusinessConfig();
        $entity->setCreatedBy($this->getUser());
        $entity->setSalesBy($this->getUser());
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($this->getUser()->getGlobalOption());
        $entity->setCustomer($customer);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setBusinessConfig($config);
        $entity->setPaymentStatus('Pending');
        $entity->setComment($config->getInvoiceComment());
        $em->persist($entity);
        $em->flush();
        if($config->getBusinessModel() == "distribution"){
           // $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($config, $type = array('post-production','pre-production','stock','service','virtual'));
         //   $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertDistributionItem($entity,$particulars);
        }
        return $this->redirect($this->generateUrl('business_invoice_edit', array('id' => $entity->getId())));

    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param BusinessInvoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessInvoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new InvoiceType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('business_invoice_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
                'enctype' => 'multipart/form-data',

            )
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */

    public function editAction($id)
    {
        $balance = 0;
        $em = $this->getDoctrine()->getManager();
        $global = $this->getUser()->getGlobalOption();
        $config = $global->getBusinessConfig();
        $entity = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->findOneBy(array( 'businessConfig' => $config , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        if (in_array($entity->getProcess(), array('Done','Delivered','Canceled'))) {
            return $this->redirect($this->generateUrl('business_invoice_show', array('id' => $entity->getId())));
        }
        $vendors = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->findBy(array('globalOption' => $this->getUser()->getGlobalOption(),'status'=>1),array('companyName'=>"ASC"));
        $areas = $this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->findBy(array('businessConfig' => $config,'status'=>1),array('name'=>"ASC"));
        $marketings = $this->getDoctrine()->getRepository('BusinessBundle:Marketing')->findBy(array('businessConfig' => $config,'status'=>1),array('name'=>"ASC"));
        $couriers = $this->getDoctrine()->getRepository('AccountingBundle:AccountCondition')->findBy(array('globalOption' => $global,'status'=>1),array('name'=>"ASC"));
        $stores = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStore')->findBy(array('businessConfig' => $config,'status'=>1),array('name'=>"ASC"));
        $customers = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findBy(array('globalOption' => $global),array('name'=>"ASC"));
        if($entity->getCustomer()){
            $outstanding = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($this->getUser()->getGlobalOption(),$entity->getCustomer());
            $balance = empty($outstanding) ? 0 : $outstanding;
        }
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindStockItem($config, $type = array('production','stock','service','virtual','pre-production','post-production'));
        $returnQty = $em->getRepository('BusinessBundle:BusinessDistributionReturnItem')->returnRemainingStock($config->getId());
        $view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'new';
        $wearhouses = $this->getDoctrine()->getRepository(WearHouse::class)->findBy(array('businessConfig' => $config),array('name'=>'ASC'));

        return $this->render("BusinessBundle:Invoice/{$view}:new.html.twig", array(
            'config' => $config,
            'entity' => $entity,
            'wearhouses' => $wearhouses,
            'vendors' => $vendors,
            'balance' => $balance,
            'customers' => $customers,
            'areas' => $areas,
            'marketings' => $marketings,
            'couriers' => $couriers,
            'stores' => $stores,
            'particulars' => $particulars,
            'returnQty' => $returnQty,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */

    public function updateAction(Request $request, BusinessInvoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Business Ivoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {
            $done = array('Done','Delivered');
            $distribution = array('Done','Delivered','Challan');
            if (!empty($data['customerMobile'])){
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($globalOption, $mobile, $data);
                $entity->setCustomer($customer);
            }
            if(isset($data['area']) and !empty($data['area'])){
             $area = $data['area'];
             $areaExist =$this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->find($area);
             if($areaExist){ $entity->setArea($areaExist);}
            }
            if(isset($data['printPreviousDue']) and empty($data['printPreviousDue'])){
              $entity->isPrintPreviousDue(false);
            }
            if(isset($data['marketing']) and !empty($data['marketing'])){
             $marketing = $data['marketing'];
             $marketingExist =$this->getDoctrine()->getRepository('BusinessBundle:Marketing')->find($marketing);
             if($marketingExist){ $entity->setMarketing($marketingExist);}
            }
            if(isset($data['courier']) and !empty($data['courier'])){
                $courier = $data['courier'];
                $courierExist =$this->getDoctrine()->getRepository('BusinessBundle:Courier')->find($courier);
                if($courierExist){ $entity->setCourier($courierExist);}
            }
            if(isset($data['condition']) and !empty($data['condition'])){
                $courier = $data['condition'];
                $condition =$this->getDoctrine()->getRepository('AccountingBundle:AccountCondition')->find($courier);
                if($condition){ $entity->setCondition($condition);}
            }
            if(isset($data['invoice']) and !empty($data['invoice'])){
                $invoice = $data['invoice'];
                if($invoice){ $entity->setInvoice($invoice);}
            }
            if(isset($data['created']) and !empty($data['created'])){
                $created = $data['created'];
                if($invoice){
                    $date = new \DateTime($created);
                    $entity->setCreated($date);
                    $entity->setUpdated($date);
                }
            }
            if(isset($data['vendor']) and !empty($data['vendor'])){
                $vendor = $data['vendor'];
                $vendorExist =$this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->find($vendor);
                if($vendorExist){ $entity->setVendor($vendorExist);}
            }
            if (in_array($entity->getProcess(), $done)) {
                $entity->setApprovedBy($this->getUser());
            }
            $entity->setTotal(round($entity->getTotal(),2));

            if($entity->getReceived()){
	            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getReceived());
	            $entity->setPaymentInWord($amountInWords);
            }
            if($entity->getProcess() == 'Condition'){
                $entity->setIsCondition(true);
            }else{
                $entity->setIsCondition(false);
            }
            $vatAmount = 0;
            if($entity->getBusinessConfig()->getVatPercent() > 0){
                $vatAmount =$this-> getTaxTariffCalculation($entity->getSubTotal(),$entity->getBusinessConfig()->getVatPercent());
                $entity->setVat($vatAmount);
            }
            if($entity->getBusinessConfig()->getAitPercent() > 0){
                $vatAmount =$this-> getTaxTariffCalculation($entity->getSubTotal(),$entity->getBusinessConfig()->getAitPercent());
                $entity->setAit($vatAmount);
            }
            $total = round($vatAmount + $entity->getSubTotal() + $entity->getAit() - $entity->getDiscount());
            $entity->setTotal($total);
            if($entity->getTotal() <= $entity->getReceived()){
                $entity->setReceived(round($entity->getTotal()));
                $entity->setDue(0);
                $entity->setPaymentStatus('Paid');
            }else{
                $entity->setPaymentStatus('Due');
                $due = round($entity->getTotal() - $entity->getReceived()- $entity->getTloPrice());
                $entity->setDue($due);
            }
	        $em->flush();
            if(in_array($entity->getProcess(), $done) and $entity->getBusinessConfig()->getBusinessModel() == 'distribution') {
                $this->approveDistributionAction($entity);
            }elseif(in_array($entity->getProcess(), $done)) {
                if($entity->getBusinessConfig()->getBusinessModel() == 'commission'){
                    $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->insertCommissionPurchase($entity);
                }
                $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessAccountInvoice($entity);
                if($entity->getBusinessConfig()->isStockHistory() == 1 ) {
                    $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->processInsertSalesItem($entity);
                }
            }
            if(in_array($entity->getProcess(), array('Condition','In-progress','Delivered'))) {
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->insertInvoiceProductItem($entity);
            }
            if($entity->getCondition() and ($entity->getProcess() == "Condition")){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountConditionLedger')->insertBusinessConditionInvoice($entity);
            }
            $inProgress = array('Hold', 'Created');
            if (in_array($entity->getProcess(), $inProgress)) {
                return $this->redirect($this->generateUrl('business_invoice_new'));
            } else {
                return $this->redirect($this->generateUrl('business_invoice_show', array('id' => $entity->getId())));
            }
        }

        $config = $entity->getBusinessConfig();
        $vendors = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->findBy(array('globalOption' => $this->getUser()->getGlobalOption(),'status'=>1),array('companyName'=>"ASC"));
        $areas = $this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->findBy(array('businessConfig' => $config,'status'=>1),array('name'=>"ASC"));
        $marketings = $this->getDoctrine()->getRepository('BusinessBundle:Marketing')->findBy(array('businessConfig' => $config,'status'=>1),array('name'=>"ASC"));
        $couriers = $this->getDoctrine()->getRepository('AccountingBundle:AccountCondition')->findBy(array('globalOption' => $global,'status'=>1),array('name'=>"ASC"));
        $stores = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStore')->findBy(array('businessConfig' => $config,'status'=>1),array('name'=>"ASC"));
        $customers = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findBy(array('globalOption' => $this->getUser()->getGlobalOption()),array('name'=>"ASC"));
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($config, $type = array('production','stock','service','virtual','pre-production','post-production'));
        $view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'new';
        $wearhouses = $this->getDoctrine()->getRepository(WearHouse::class)->findBy(array('businessConfig' => $config),array('name'=>'ASC'));
        return $this->render("BusinessBundle:Invoice/{$view}:new.html.twig", array(
            'config' => $config,
            'entity' => $entity,
            'vendors' => $vendors,
            'areas' => $areas,
            'couriers' => $couriers,
            'stores' => $stores,
            'wearhouses' => $wearhouses,
            'customers' => $customers,
            'marketings' => $marketings,
            'particulars' => $particulars,
            'form' => $editForm->createView(),
        ));

    }


    private function getTaxTariffCalculation($subTotal,$tariff)
    {
        $value = 0;
        $value = (($subTotal * $tariff)/100);
        return $value;
    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }


    /**
	 * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
	 */

	public function invoiceDiscountUpdateAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$discountType = $request->request->get('discountType');
		$discountCal = (float)$request->request->get('discount');
		$invoice = $request->request->get('invoice');

		/* @var $entity BusinessInvoice */

		$entity = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->find($invoice);
		$subTotal = $entity->getSubTotal();
		if($discountType == 'flat'){
			$total = ($subTotal  - $discountCal);
			$discount = $discountCal;
		}else{
			$discount = ($subTotal*$discountCal)/100;
			$total = ($subTotal  - $discount);
		}
		$vat = 0;
		if($total > $discount ){

			$entity->setDiscountType($discountType);
			$entity->setDiscountCalculation($discountCal);
			$entity->setDiscount(round($discount));
			$entity->setTotal(round($total + $vat));
			$entity->setDue(round($total + $vat));

		}else{

			$entity->setDiscountType('flat');
			$entity->setDiscountCalculation(0);
			$entity->setDiscount(round($discount));
			$entity->setTotal(round($entity->getSubTotal() + $vat));
			$entity->setDue(round($entity->getTotal()));
		}

		$em->flush();
		$msg = 'Discount successfully';
		$result = $this->returnResultData($entity,$msg);
		return new Response(json_encode($result));
	}


    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */


    public function showAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $config BusinessConfig */
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if ($config->getId() == $entity->getBusinessConfig()->getId()) {

            $view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'default';
            return $this->render("BusinessBundle:Invoice/{$view}:show.html.twig", array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('business_invoice'));
        }

    }

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */

    public function deleteAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $this->getDoctrine()->getRepository(BusinessInvoiceParticular::class)->removeSalesItem($entity);
        if($entity->getInvoiceReturn()){
            foreach ($entity->getInvoiceReturnItems() as $row){
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceReturnItem')->removeSalesReturnItem($row);
            }
        }
        $em->remove($entity);
        $em->flush();
        return new Response(json_encode(array('success' => 'success')));
    }

    public function customerUpdateAction()
    {
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository("BusinessBundle:BusinessInvoice")->find($data['invoice']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $customer = $this->getDoctrine()->getRepository("DomainUserBundle:Customer")->find($data['customer']);
        $entity->setCustomer($customer);
        $em->flush();
        return new Response(json_encode(array('success' => 'success')));
    }

    public function customerMobileUpdateAction()
    {
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository("BusinessBundle:BusinessInvoice")->find($data['invoice']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $option = $this->getUser()->getGlobalOption();
        $customer = $this->getDoctrine()->getRepository("DomainUserBundle:Customer")->findOneBy(array('globalOption'=>$option,'mobile'=>$data['customer']));
        if($customer){
            $entity->setCustomer($customer);
            $em->flush();
        }
        return new Response(json_encode(array('success' => 'success')));
    }

    public function wearhouseUpdateAction(BusinessInvoiceParticular $particular)
    {
        $em = $this->getDoctrine()->getManager();

        $wearHouse = $this->getDoctrine()->getRepository(WearHouse::class)->find($_REQUEST['waerhouse']);
        $particular->setWearHouse($wearHouse);
        $em->flush();
        return new Response("success");
    }

     public function particularSearchAction(BusinessParticular $particular)
    {
        $returnQty = $this->getDoctrine()->getRepository('BusinessBundle:BusinessDistributionReturnItem')->returnRemainingStockItem($particular->getBusinessConfig()->getId(),$particular->getId());
        $remain = ($particular->getRemainingQuantity() - $returnQty);
        $unit = !empty($particular->getUnit() && !empty($particular->getUnit()->getName())) ? $particular->getUnit()->getName():'Unit';
        return new Response(json_encode(array('purchasePrice'=> $particular->getPurchasePrice(), 'salesPrice'=> $particular->getSalesPrice(),'remainQnt'=> $remain,'quantity'=> 1,'unit' => $unit)));
    }

    public function returnResultData(BusinessInvoice $entity, $msg=''){

       // $invoiceParticulars = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->getSalesItems($entity);
        //$view = !empty($config->getBusinessModel()) ? $config->getBusinessModel() : 'item-data';
        $wearhouses = $this->getDoctrine()->getRepository(WearHouse::class)->findBy(array('businessConfig' => $entity->getBusinessConfig(),'status'=>1),array('name'=>'ASC'));
        $html =  $this->renderView("BusinessBundle:Invoice/item:data.html.twig", array(
            'config' => $entity->getBusinessConfig(),
            'entity' => $entity,
            'wearhouses' => $wearhouses,
        ));
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $tloPrice = $entity->getTloPrice() > 0 ? $entity->getTloPrice() : 0;
        $netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
        $due = $entity->getDue() > 0 ? $entity->getDue() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
           'subTotal' => $subTotal,
           'tloPrice' => $tloPrice,
           'netTotal' => ($netTotal-$tloPrice),
           'payment' => $payment ,
           'due' => $due,
           'vat' => $vat,
           'discount' => $discount,
           'invoiceParticulars' => $html ,
           'msg' => $msg ,
           'success' => 'success'
       );
       return $data;

    }

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */

    public function addEventAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $request->request->all();
        $startDate = empty($data['startDate']) ? new \DateTime("now") : new \DateTime($data['startDate']);
        $endDate = empty($data['endDate']) ? new \DateTime("now") : new \DateTime($data['endDate']);
        $invoice->setEventType($data['eventType']);
        $invoice->setStartDate($startDate);
        $invoice->setEndDate($endDate);
        $invoice->setVenue($data['venue']);
        if(!empty($data['customerName']) and !empty($data['customerMobile'])){
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($globalOption, $mobile, $data);
            $invoice->setCustomer($customer);
        }elseif (!empty($data['mobile'])){
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile));
            $invoice->setCustomer($customer);
        }
        $em->flush();
        exit;

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
        $invoice =  $this->getDoctrine()->getRepository(BusinessInvoice::class)->find($id);
        $stock = $this->getDoctrine()->getRepository(BusinessParticular::class)->findOneBy(array('businessConfig' => $invoice->getBusinessConfig(),'particularCode'=>$barcode));
        $particular = $stock->getId();
        $price = $stock->getPrice();
        $unit = ($stock->getUnit()) ? $stock->getUnit()->getName():'';
        $quantity = 1;
        $description = '';
        $invoiceItems = array('particular' => $particular, 'quantity' => $quantity,'price' => $price,'unit' => $unit,'description' => $description,'wearhouse' => $wearhouse);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertInvoiceParticular($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));

    }

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */
    public function addParticularAction(Request $request, BusinessInvoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $wearhouse = (isset($data['wearhouse']) and $data['wearhouse']) ? $data['wearhouse']:'';
        $particular = $request->request->get('customParticular');
        $price = $request->request->get('price');
        $unit = $request->request->get('unit');
        $quantity = $request->request->get('quantity');
        $description = $request->request->get('description');

        $invoiceItems = array('particular' => $particular, 'quantity' => $quantity,'price' => $price,'unit' => $unit,'description' => $description,'wearhouse' => $wearhouse);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertInvoiceParticular($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));

    }

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */

    public function invoiceParticularDeleteAction(BusinessInvoice $invoice, BusinessInvoiceParticular $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        if($particular->getBusinessParticular()){
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseReturnItem')->deletePurchaseReturnItem($particular);
        }
        $em->remove($particular);
        $em->flush();
	    $invoice = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($invoice);
        $result = $this->returnResultData($invoice,$msg ='');
        return new Response(json_encode($result));

    }



    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */

	public function invoiceReverseAction(BusinessInvoice $sales)
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

		set_time_limit(0);
		$em = $this->getDoctrine()->getManager();
		$this->getDoctrine()->getRepository('BusinessBundle:BusinessProductionExpense')->removeProductionExpense($sales);
		$this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->accountBusinessSalesReverse($sales);
		$this->getDoctrine()->getRepository('BusinessBundle:BusinessStoreLedger')->storeInvoiceReverse($sales);
        if($sales->getInvoiceReturn()){
            foreach ($sales->getInvoiceReturnItems() as $row){
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceReturnItem')->removeSalesReturnItem($row);
            }
        }
        $sales->setIsReversed(true);
		$sales->setProcess('Created');
		$sales->setApprovedBy(NULL);
		$em->flush();
		$template = $this->get('twig')->render('BusinessBundle:Invoice:reverse.html.twig', array(
			'entity' => $sales,
			'config' => $sales->getBusinessConfig(),
		));
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->insertInvoiceProductItem($sales);
		$em->getRepository('BusinessBundle:BusinessReverse')->salesReverse($sales, $template);
        if($sales->getBusinessConfig()->isStockHistory() == 1 ) {
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->processReverseSalesItem($sales);
        }
        if($sales->getCondition()){
            $this->getDoctrine()->getRepository('AccountingBundle:AccountConditionLedger')->accountInvoiceReverse($sales);
        }
		return $this->redirect($this->generateUrl('business_invoice_edit',array('id' => $sales->getId())));
	}

	public function invoiceReverseShowAction($id)
	{
		$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
		$entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessReverse')->findOneBy(array('businessConfig' => $config, 'businessInvoice' => $id));
		return $this->render('BusinessBundle:Reverse:sales.html.twig', array(
			'entity' => $entity,
		));
	}

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */

    public function deleteEmptyInvoiceAction()
    {
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->findBy(array( 'businessConfig' => $config, 'process' => 'Created'));
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('business_invoice'));
    }

    public function invoicePrintPdfAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if ($config->getId() == $entity->getBusinessConfig()->getId()) {

            /** @var  $invoiceParticularArr */
            $invoiceParticularArr = array();

            /** @var $row BusinessInvoiceParticular */
            if (!empty($entity->getInvoiceParticulars())) {
                foreach ($entity->getInvoiceParticulars() as $row):
                    if (!empty($row->getBusinessParticular())) {
                        $invoiceParticularArr[$row->getBusinessParticular()->getId()] = $row;
                    }
                endforeach;
            }

            $services = $em->getRepository('BusinessBundle:BusinessService')->findBy(array('businessConfig' => $config, 'serviceShow' => 1, 'status' => 1), array('serviceSorting' => 'ASC'));
            $treatmentSchedule = $em->getRepository('BusinessBundle:BusinessTreatmentPlan')->findTodaySchedule($config);

            if ($config->isCustomPrescription() == 1) {
                $template = $config->getGlobalOption()->getSlug();
            } else {
                $template = 'print';
            }
            $html = $this->renderView(
                'BusinessBundle:Print:dental-care.html.twig', array(
                    'entity' => $entity,
                    'invoiceParticularArr' => $invoiceParticularArr,
                    'services' => $services,
                    'treatmentSchedule' => $treatmentSchedule,
                )
            );
            $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
            $snappy = new Pdf($wkhtmltopdfPath);
            $pdf = $snappy->getOutputFromHtml($html);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="incomePdf.pdf"');
            echo $pdf;

            return new Response('');
        }
    }

    public function autoParticularSearchAction()
    {
        $q = $_REQUEST['term'];
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->searchAutoComplete($config,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[] = ['value' => $entity['id']];
        endforeach;
        return new JsonResponse($items);

    }

    public function autoUnitSearchAction()
    {
        $q = $_REQUEST['term'];
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('SettingToolBundle:ProductUnit')->searchAutoComplete($config,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    public function addAccessoriesAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $wearhouse = (isset($data['wearhouse']) and $data['wearhouse']) ? $data['wearhouse']:'';
        $particular = $request->request->get('particular');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('salesPrice');
        $description = $request->request->get('description');
        $bonusQuantity = $request->request->get('bonusQuantity');
        if(!empty($particular)){
            $invoiceItems = array('accessories' => $particular ,'quantity' => $quantity,'price' => $price,'description' => $description,'wearhouse' => $wearhouse,'bonusQuantity' => $bonusQuantity);
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertStockItem($invoice,$invoiceItems);
            $invoice = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($invoice);
            $msg = 'Particular added successfully';
            $result = $this->returnResultData($invoice,$msg);
            return new Response(json_encode($result));
        }
        exit;

    }

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */
    public function addParticularCommissionAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $particular = $request->request->get('particular');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('salesPrice');
        $vendor = $request->request->get('vendor');
        $stockGrn = $request->request->get('stockGrn');
        $invoiceItems = array('particular' => $particular, 'quantity' => $quantity,'price' => $price,'vendor' => $vendor,'stockGrn' => $stockGrn);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertCommissionInvoiceParticular($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));


    }

    public function bannerSignAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $particular = $request->request->get('particular');
        $quantity = $request->request->get('quantity');
        $salesPrice = $request->request->get('salesPrice');
        $width = $request->request->get('width');
        $height = $request->request->get('height');
        $description = $request->request->get('description');
        if(!empty($particular)){
            $invoiceItems = array('particular' => $particular ,'quantity' => $quantity,'salesPrice'=> $salesPrice,'description'=> $description, 'width'=> $width,'height'=> $height);
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertBannerSignItem($invoice,$invoiceItems);
	        $invoice = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($invoice);
            $msg = 'Particular added successfully';
            $result = $this->returnResultData($invoice,$msg);
            return new Response(json_encode($result));
        }
        exit;

    }

    public function invoiceItemUpdateAction(Request $request)
    {

        $data = $request->request->all();
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->updateInvoiceItems($data);
        $invoice = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));

    }



    public function getBarcode($value)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($value);
        $barcode->setType(BarcodeGenerator::Code39Extended);
        $barcode->setScale(1);
        $barcode->setThickness(50);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,'.$code .'" />';
        return $data;
    }

    public function invoicePrintAction(BusinessInvoice $entity)
    {

        $em = $this->getDoctrine()->getManager();

        /* @var $config BusinessConfig */

        $print = (isset($_REQUEST['print']) and !empty($_REQUEST['print'])) ?  $_REQUEST['print'] : 'print';

        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if ($config->getId() == $entity->getBusinessConfig()->getId()) {

	        if($config->isCustomInvoicePrint() == 1){
                $template = $config->getGlobalOption()->getSubDomain();
	        }else{
                $template = 'print';
            }
            $amountInWords = '';
            if($entity->getProcess() == "Quotation"){
                $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            }
            $customerBarcode = $this->getBarcode($entity->getCustomer()->getMobile());
            $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($this->getUser()->getGlobalOption(),$entity->getCustomer());
            $balance = empty($result) ? 0 : $result;
            $balanceWords = $this->get('settong.toolManageRepo')->intToWords($balance);
            return  $this->render("BusinessBundle:Print:{$template}.html.twig",
                array(
                    'config' => $config,
                    'entity' => $entity,
                    'balance' => $balance,
                    'customerBarcode' => $customerBarcode,
                    'amountInWords' => $amountInWords,
                    'balanceWords' => $balanceWords,
                    'print' => $print,
                )
            );
        }

    }

    public function posPrintAction(BusinessInvoice $entity)
    {

        $em = $this->getDoctrine()->getManager();

        /* @var $config BusinessConfig */
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();

        if($config->isCustomInvoicePrint() == 1){
            $template = $config->getGlobalOption()->getSubDomain();
        }else{
            $template = 'print';
        }
        if ($config->getId() == $entity->getBusinessConfig()->getId()) {
            $response = $this->renderView(
                "BusinessBundle:PosPrint:{$template}.html.twig", array(
                    'entity'         => $entity,
                    'printMode'         => 'print',
                )
            );
            return new Response($response);
        }

    }

    public function invoiceChalanAction(BusinessInvoice $entity)
	{
		$em = $this->getDoctrine()->getManager();

		/* @var $config BusinessConfig */

		$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
		if ($config->getId() == $entity->getBusinessConfig()->getId()) {
			if($config->isCustomInvoicePrint() == 1){
				$template = $config->getGlobalOption()->getSlug();
			}else{
				$template = !empty($config->getInvoiceType()) ? $config->getInvoiceType():'print';
			}
			$result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($config->getGlobalOption(), $data = array('mobile' => $entity->getCustomer()->getMobile()));
			$balance = empty($result) ? 0 : $result[0]['customerBalance'];
			return  $this->render("BusinessBundle:Print:{$template}.html.twig",
				[
					'config'    => $config,
					'entity'    => $entity,
					'balance'   => $balance,
					'print'     => 'chalan',
                ]
			);
		}
	}

	public function  commissionProcessAction()
    {
        set_time_limit(0);
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $data = [];
        $entities = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->invoiceLists( $user,$data);
        $config = $user->getGlobalOption()->getBusinessConfig()->getId();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->checkInstantPurchaseReverse($config);
        foreach ($entities->getQuery()->getResult() as $entity):
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->insertCommissionPurchase($entity);
        endforeach;
        return $this->redirect($this->generateUrl('business_invoice'));
    }


    public function invoiceGroupReverseAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();
        $data = ['startDate' => '2019-07-04','endateDate' => '2019-07-04','process'=>"Done"];
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->invoiceLists( $this->getUser() , $data);
        $pagination = $entities->getQuery()->getResult();

        /* @var $sales BusinessInvoice */

        foreach ($pagination as $sales):
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessProductionExpense')->removeProductionExpense($sales);
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->accountBusinessSalesReverse($sales);
            $sales->setIsReversed(true);
            $sales->setProcess('Revised');
            $em->flush();
          endforeach;
        exit;

    }

    public function approveAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if($entity and empty($entity->getApprovedBy())){
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->insertInvoiceProductItem($entity);
            $result = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->invoiceDistributionTotalPrice($entity);
            if(($entity->getBusinessConfig()->getBusinessModel() == 'distribution' and $result['damageQnt'] > 0) or ($entity->getBusinessConfig()->getBusinessModel() == 'distribution' and $result['spoilQnt'] > 0) ){
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseReturn')->insertInvoiceDamageItem($entity) ;
            }
            if($entity->getTloPrice() > 0 and !empty($entity->getVendor())){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->insertTloAdjustment($entity);
            }
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessAccountInvoice($entity);

            if($entity->getBusinessConfig()->isStockHistory() == 1 ) {
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->processInsertSalesItem($entity);
            }
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess("Delivered");
            $entity->setApprovedBy($this->getUser());
            $em->persist($entity);
            $em->flush();
            /* @var $global GlobalOption */
            $global = $this->getUser()->getGlobalOption();
            if($entity->getBusinessConfig()->getBusinessModel() == 'association' and $global->getSmsSenderTotal() and $global->getSmsSenderTotal()->getRemaining() > 0 and $global->getNotificationConfig()->getSmsActive() == 1) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.association_invoice', new \Setting\Bundle\ToolBundle\Event\AssociationInvoiceSmsEvent($entity));
            }
            return new Response("Success");
        }
        return new Response("Failed");

    }

    public function receivedAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if($entity and empty($entity->getApprovedBy())){
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess("Delivered");
            $entity->setApprovedBy($this->getUser());
            $entity->setReceived($entity->getTotal());
            $entity->setDue(0);
            $em->persist($entity);
            $em->flush();
            if($entity->getCondition() and $entity->getProcess() == "Delivered"){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountConditionLedger')->insertConditionInvoiceReceive($entity);
            }
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessAccountInvoice($entity);
            return new Response("Success");
        }
        return new Response("Failed");

    }

    public function returnedAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if($entity and empty($entity->getApprovedBy())){
            set_time_limit(0);
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('Returned');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->insertInvoiceProductItem($entity);
            if($entity->getCondition() and $entity->getProcess() == "Returned"){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountConditionLedger')->insertConditionInvoiceReturn($entity);
            }
            return new Response("Success");
        }
        return new Response("Failed");

    }

    public function invoiceGroupApprovedAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();
        $data = ['startDate' => '2019-07-04','endateDate' => '2019-07-04','process' => "Revised"];
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->invoiceLists( $this->getUser() , $data);
        $pagination = $entities->getQuery()->getResult();

        /* @var $entity BusinessInvoice */

        foreach ($pagination as $entity):
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessAccountInvoice($entity);
        endforeach;
        exit;
    }

    public function approveDistributionAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($entity){
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->insertInvoiceProductItem($entity);
            if($entity->getTloPrice() > 0 and !empty($entity->getVendor())){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->insertTloAdjustment($entity);
            }
            $result = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->invoiceDistributionTotalPrice($entity);
            if($result['damageQnt'] > 0 or $result['spoilQnt'] > 0) {
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessDistributionReturnItem')->insertUpdateDistributionReturnItem($entity);
            }
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessAccountInvoice($entity);
            if($entity->getSalesReturn() > 0 ){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessAccountSalesReturn($entity);
            }
            if($entity->getStoreLedgers()){
                foreach ($entity->getStoreLedgers() as $ledger){
                    $this->getDoctrine()->getRepository('BusinessBundle:BusinessStoreLedger')->approveStorePayment($ledger,$user);
                    if($ledger->getTransactionType() == "Receive"){
                        $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertStorePayment($entity,$ledger);
                    }
                }
            }
            if($entity->getInvoiceReturn() and $entity->getSalesReturn() > 0){
                /* @var $row BusinessInvoiceReturnItem */
                foreach ($entity->getInvoiceReturnItems() as $row){
                    $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceReturnItem')->approveSalesReturnItem($row);
                }
                $invoiceReturn = $entity->getInvoiceReturn();
                $invoiceReturn->setAdjustment($entity->getSalesReturn());
                $invoiceReturn->setProcess('Approved');
                $em->flush();
            }
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess("Delivered");
            $entity->setApprovedBy($this->getUser());
            $em->persist($entity);
            $em->flush();
            if($entity->getBusinessConfig()->isStockHistory() == 1 ){
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->processInsertSalesItem($entity);
            }
            return new Response("Success");
        }
        return new Response("invalid");

    }

    public function invoiceCustomerSelectAction()
    {
        $config = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository(Customer::class)->findBy(
            array('globalOption' => $config),array('name'=>'ASC')
        );
        $type = '';
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity->getId(),'text'=> $entity->getName());
        endforeach;
        return new JsonResponse($items);
    }

    public function invoiceCustomerInlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(BusinessInvoice::class)->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $customer = $em->getRepository(Customer::class)->find($data['value']);
        $entity->setCustomer($customer);
        $em->persist($entity);
        $em->flush();
        return new Response('success');

    }


}

