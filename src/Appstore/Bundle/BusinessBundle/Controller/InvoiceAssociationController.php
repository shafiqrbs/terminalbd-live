<?php

namespace Appstore\Bundle\BusinessBundle\Controller;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Form\CustomerInvoiceType;
use Knp\Snappy\Pdf;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * BusinessInvoiceController controller.
 *
 */
class InvoiceAssociationController extends Controller
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
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
     *
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $data['createdBy'] = $user->getId();
        $entities = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->invoiceLists( $user,$data);
        $pagination = $this->paginate($entities);
        return $this->render('CustomerBundle:Invoice:index.html.twig', array(
            'globalOption' => $this->getUser()->getGlobalOption(),
            'entities' => $pagination,
            'salesTransactionOverview' => '',
            'previousSalesTransactionOverview' => '',
            'searchForm' => $data,
        ));
    }


    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('user'=>$this->getUser()->getId()));

        $lastInvoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->getLastInvoiceParticular($customer->getId());

        $entity = new BusinessInvoice();
        $editForm = $this->createCreateForm($entity);
        $outstanding = 0;
        return $this->render("CustomerBundle:Invoice:new.html.twig", array(
            'globalOption' => $this->getUser()->getGlobalOption(),
            'lastInvoice' => $lastInvoice,
            'entity' => $entity,
            'customer' => $customer,
            'outstanding' => $outstanding,
            'form' => $editForm->createView(),
        ));

    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param BusinessInvoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessInvoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new CustomerInvoiceType($globalOption), $entity, array(
            'action' => $this->generateUrl('customerweb_invoice_create', array('shop' => $globalOption->getSlug())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
                'enctype' => 'multipart/form-data',

            )
        ));
        return $form;
    }

    public function createAction(Request $request)
    {
        $data = $request->request->all();
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();

        $entity = new BusinessInvoice();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('user'=>$user->getId()));
        $lastInvoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->getLastInvoiceParticular($customer->getId());

        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        if (($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setBusinessConfig($config);
            $entity->setCustomer($customer);
            $entity->setMobile($customer->getMobile());
            $entity->setReceived($data['paymentTotal']);
            $entity->setDue(0);
            $entity->setEndDate(new \DateTime("now"));
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertStudentMonthlyParticular($entity,$lastInvoice, $data);
            $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($entity);
            return $this->redirect($this->generateUrl('customerweb_invoice', array('shop' => $user->getGlobalOption()->getSlug())));
        }
        $this->get('session')->getFlashBag()->add(
            'warning', "Payment information does not valid"
        );
        return $this->render("CustomerBundle:Invoice:new.html.twig", array(
            'globalOption' => $user->getGlobalOption(),
            'entity' => $entity,
            'form' => $form->createView(),
        ));


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
        $form = $this->createForm(new CustomerInvoiceType($globalOption), $entity, array(
            'action' => $this->generateUrl('customerweb_invoice_update', array('shop' => $globalOption->getSlug(),'id' =>$entity->getId())),
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
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->findOneBy(array( 'businessConfig' => $config , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
  	    return $this->render("CustomerBundle:Invoice:new.html.twig", array(
            'globalOption' => $this->getUser()->getGlobalOption(),
            'entity' => $entity,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
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
	        $em->flush();
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertCustomerInvoiceParticular($entity, $data);
            $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($entity);
            return $this->redirect($this->generateUrl('customerweb_invoice', array('shop' => $globalOption->getSlug())));
        }
        $config = $entity->getBusinessConfig();
        return $this->render("CustomerBundle:Invoice:new.html.twig", array(
            'globalOption' => $globalOption,
            'entity' => $entity,
            'form' => $editForm->createView(),
        ));

    }

	/**
	 * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
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
		exit;
	}


    /**
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
     */


    public function showAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if ($config->getId() == $entity->getBusinessConfig()->getId()) {
            return $this->render('BusinessBundle:Invoice:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('business_invoice'));
        }

    }

    /**
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
     */

    public function deleteAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response(json_encode(array('success' => 'success')));
    }

    public function particularSearchAction(BusinessParticular $particular)
    {
	    $unit = !empty($particular->getUnit() && !empty($particular->getUnit()->getName())) ? $particular->getUnit()->getName():'Unit';
        return new Response(json_encode(array('purchasePrice'=> $particular->getPurchasePrice(), 'salesPrice'=> $particular->getSalesPrice(),'quantity'=> 1,'unit' => $unit)));
    }

    public function returnResultData(BusinessInvoice $entity, $msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->getSalesItems($entity);
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
        $due = $entity->getDue() > 0 ? $entity->getDue() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
           'subTotal' => $subTotal,
           'netTotal' => $netTotal,
           'payment' => $payment ,
           'due' => $due,
           'vat' => $vat,
           'discount' => $discount,
           'invoiceParticulars' => $invoiceParticulars ,
           'msg' => $msg ,
           'success' => 'success'
       );

       return $data;

    }

    /**
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
     */

    public function addEventAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $request->request->all();
        $invoice->setEventType($data['eventType']);
        $invoice->setStartDate($data['startDate']);
        $invoice->setEndDate($data['endDate']);
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
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
     */


    public function addParticularAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $particular = $request->request->get('customParticular');
        $price = $request->request->get('price');
        $unit = $request->request->get('unit');
        $quantity = $request->request->get('quantity');
        $invoiceItems = array('particular' => $particular, 'quantity' => $quantity,'price' => $price,'unit' => $unit);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertInvoiceParticular($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;

    }

    /**
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
     */

    public function invoiceParticularDeleteAction(BusinessInvoice $invoice, BusinessInvoiceParticular $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
	    $invoice = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($invoice);
        $result = $this->returnResultData($invoice,$msg ='');
        return new Response(json_encode($result));
        exit;
    }

    /**
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
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
		$sales->setIsReversed(true);
		$sales->setProcess('Created');
		$em->flush();
		$template = $this->get('twig')->render('BusinessBundle:Invoice:reverse.html.twig', array(
			'entity' => $sales,
			'config' => $sales->getBusinessConfig(),
		));
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->insertInvoiceProductItem($sales);
		$em->getRepository('BusinessBundle:BusinessReverse')->salesReverse($sales, $template);
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
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
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
        $particular = $request->request->get('particular');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('salesPrice');
        if(!empty($particular)){
            $invoiceItems = array('accessories' => $particular ,'quantity' => $quantity,'price' => $price);
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertStockItem($invoice,$invoiceItems);
            $invoice = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($invoice);
            $msg = 'Particular added successfully';
            $result = $this->returnResultData($invoice,$msg);
            return new Response(json_encode($result));
        }
        exit;

    }

    /**
     * @Secure(roles="ROLE_MEMBER,ROLE_DOMAIN");
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
        exit;

    }

    public function bannerSignAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $particular = $request->request->get('particular');
        $quantity = $request->request->get('quantity');
        $salesPrice = $request->request->get('salesPrice');
        $width = $request->request->get('width');
        $height = $request->request->get('height');
        if(!empty($particular)){
            $invoiceItems = array('particular' => $particular ,'quantity' => $quantity,'salesPrice'=> $salesPrice, 'width'=> $width,'height'=> $height);
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
        exit;
    }

    public function getBarcode($value)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($value);
        $barcode->setType(BarcodeGenerator::Code39Extended);
        $barcode->setScale(1);
        $barcode->setThickness(25);
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
                $template = 'invoice-'.$config->getGlobalOption()->getId();
	        }else{
                $template = !empty($config->getInvoiceType()) ? $config->getInvoiceType():'print';
            }
            $amountInWords = '';
            if($entity->getProcess() == "Quotation"){
                $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            }
            $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($this->getUser()->getGlobalOption(),$entity->getCustomer());
            $balance = empty($result) ? 0 : $result;
            return  $this->render("BusinessBundle:Print:{$template}.html.twig",
                array(
                    'config' => $config,
                    'entity' => $entity,
                    'balance' => $balance,
                    'amountInWords' => $amountInWords,
                    'print' => $print,
                )
            );
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

    public function invoiceGroupApprovedAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();
        $data = ['startDate' => '2019-07-04','endateDate' => '2019-07-04','process'=>"Revised"];
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->invoiceLists( $this->getUser() , $data);
        $pagination = $entities->getQuery()->getResult();
        /* @var $entity BusinessInvoice */
        foreach ($pagination as $entity):
            $accountSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessAccountInvoice($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesGlobalTransaction($accountSales);
        endforeach;
        exit;
    }

}

