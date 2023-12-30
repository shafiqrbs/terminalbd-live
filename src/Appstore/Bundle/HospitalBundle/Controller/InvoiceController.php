<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceEditType;
use Appstore\Bundle\HospitalBundle\Form\InvoicePaymentType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
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
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_ADMISSION,ROLE_DOMAIN_HOSPITAL_VISIT");
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user , $mode = 'diagnostic' , $data);
        $pagination = $this->paginate($entities);
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital->getId());
      //  $salesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'true','diagnostic');
     //   $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'false','diagnostic');

        $processes = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAdmissionProcess($hospital,'diagnostic',$data);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignProcess($hospital,'assign-doctor');
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignProcess($hospital,'referred-doctor');

        return $this->render('HospitalBundle:Invoice:index.html.twig', array(
          //  'salesTransactionOverview' => $salesTransactionOverview,
          //  'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'entities'                          => $pagination,
            'assignDoctors'                     => $assignDoctors,
            'referredDoctors'                   => $referredDoctors,
            'processes'                         => $processes,
            'employees'                         => $employees,
            'searchForm'                        => $data,
        ));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_ADMISSION,ROLE_DOMAIN_HOSPITAL_VISIT");
     */

    public function appointmentInvoiceAction()
    {
        return $this->redirect($this->generateUrl('hms_prescription'));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_ADMISSION,ROLE_DOMAIN_HOSPITAL_VISIT");
     */
    public function oldPatientDiagnosticAction(Request $request)
    {
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $customerId = $request->request->get('customer');
        $invoiceId = $request->request->get('invoice');
        $patient = $request->request->get('patientId');
        $option = $this->getUser()->getGlobalOption();
        $patientId = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$option,'customerId'=>$patient));
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$option,'mobile'=>$customerId));
        $invoice = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig'=>$hospital,'invoice' => $invoiceId));
        $em = $this->getDoctrine()->getManager();
        $entity = new Invoice();
        if($invoice){
            $customer = $invoice->getCustomer();
            $entity->setCustomer($customer);
            $entity->setMobile($customer->getMobile());
            $hospital = $option->getHospitalConfig();
            $entity->setHospitalConfig($hospital);
            $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(1);
            $entity->setService($service);
            $referredDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital,'name'=>'Self','service' => 6));
            $entity->setReferredDoctor($referredDoctor);
            $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
            $entity->setTransactionMethod($transactionMethod);
            $entity->setPaymentStatus('Pending');
            $entity->setInvoiceMode('diagnostic');
            $entity->setPrintFor('diagnostic');
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('hms_invoice_edit', array('id' => $entity->getId())));
        }elseif($customer){
            $entity->setCustomer($customer);
            $entity->setMobile($customer->getMobile());
            $hospital = $option->getHospitalConfig();
            $entity->setHospitalConfig($hospital);
            $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(1);
            $entity->setService($service);
            $referredDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital,'name'=>'Self','service' => 6));
            $entity->setReferredDoctor($referredDoctor);
            $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
            $entity->setTransactionMethod($transactionMethod);
            $entity->setPaymentStatus('Pending');
            $entity->setInvoiceMode('diagnostic');
            $entity->setPrintFor('diagnostic');
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('hms_invoice_edit', array('id' => $entity->getId())));
        }elseif($patientId){
            $entity->setCustomer($patientId);
            $entity->setMobile($patientId->getMobile());
            $hospital = $option->getHospitalConfig();
            $entity->setHospitalConfig($hospital);
            $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(1);
            $entity->setService($service);
            $referredDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital,'name'=>'Self','service' => 6));
            $entity->setReferredDoctor($referredDoctor);
            $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
            $entity->setTransactionMethod($transactionMethod);
            $entity->setPaymentStatus('Pending');
            $entity->setInvoiceMode('diagnostic');
            $entity->setPrintFor('diagnostic');
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('hms_invoice_edit', array('id' => $entity->getId())));
        }

        return $this->redirect($this->generateUrl('hms_prescription'));


    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_ADMISSION,ROLE_DOMAIN_HOSPITAL_VISIT");
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Invoice();
        $option = $this->getUser()->getGlobalOption();
        $hospital = $option->getHospitalConfig();
        $invoice = isset($_REQUEST['id']) ? $_REQUEST['id']:'';
        if(!empty($invoice)){
            $admission = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'id' => $invoice));
            $entity->setCustomer($admission->getCustomer());
            $entity->setMobile($admission->getCustomer()->getMobile());
            $entity->setParent($admission);
        }
        $entity->setHospitalConfig($hospital);
        $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(1);
        $entity->setService($service);
        $referredDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital,'name'=>'Self','service' => 6));
        $entity->setReferredDoctor($referredDoctor);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setPaymentStatus('Pending');
        $entity->setInvoiceMode('diagnostic');
        $entity->setPrintFor('diagnostic');
        $entity->setCreatedBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_invoice_edit', array('id' => $entity->getId())));

    }


    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_ADMISSION,ROLE_DOMAIN_HOSPITAL_VISIT");
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'id' => $id));
        if (!$entity) { throw $this->createNotFoundException('Unable to find Invoice entity.');}
        $editForm = $this->createEditForm($entity);
        $array = array('In-progress','Created','Revised');
        if (!in_array($entity->getProcess(),$array)) {
            return $this->redirect($this->generateUrl('hms_invoice_show', array('id' => $entity->getId())));
        }
        $em->getRepository('HospitalBundle:InvoiceTransaction')->hmsEditInvoiceTransaction($entity);
        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($hospital,array(1,8,7));
        $referredDoctors    = $em->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5,6));
        return $this->render('HospitalBundle:Invoice:new.html.twig', array(
            'entity' => $entity,
            'particularService' => $services,
            'referredDoctors' => $referredDoctors,
            'form' => $editForm->createView(),
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_LAB,ROLE_DOMAIN_HOSPITAL_DOCTOR,ROLE_DOMAIN_HOSPITAL_OPERATOR");
     */

    public function readyReportAction(Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $particular InvoiceParticular */

        foreach ($entity->getInvoiceParticulars() as $particular){
            $particular->setParticularDeliveredBy($this->getUser());
            $particular->setParticularPreparedBy($this->getUser());
            $particular->setProcess('Done');
            $em->persist($particular);
            $em->flush();
        }
        if($entity->getPaymentStatus() == "Paid"){
            $entity->setProcess('Done');
            $em->persist($entity);
            $em->flush();
            $accountInvoice = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertHospitalFinalAccountInvoice($entity);
        }
        return new Response('done');
    }

    public function commissionRegenerateAction(Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setCommissionApproved(false);
        $em->persist($entity);
        $em->flush();
        return new Response('done');
    }

    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(Invoice $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->getSalesItems($entity);
        $invoiceTransaction = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->getInvoiceTransactionItems($entity);

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
           'invoiceTransaction' => $invoiceTransaction,
           'invoiceParticulars' => $invoiceParticulars ,
           'msg' => $msg ,
           'success' => 'success'
       );

       return $data;

    }


    public function addParticularAction(Request $request, Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));


    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_DOCTOR");
     */

    public function invoiceParticularDeleteAction( $invoice, InvoiceParticular $particular){


        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $entity =  $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->find($invoice);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($entity);
        $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->updateInvoiceTransactionDiscount($entity);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
        $msg = 'Particular deleted successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));

    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discountCalculation = (float)$request->request->get('discount');
        $invoice = $request->request->get('invoice');
        $discountType = $request->request->get('discountType');
        $entity = $em->getRepository('HospitalBundle:Invoice')->find($invoice);
        $subTotal = $entity->getSubTotal();
        $discount = 0;
        if($discountType == 'flat' and $discountCalculation > 0 ){
            $total = ($subTotal  - $discountCalculation);
            $discount = $discountCalculation;
        }elseif($discountType == 'percentage' and $discountCalculation > 0 ){
            $discount = ($subTotal * $discountCalculation)/100;
            $total = ($subTotal  - $discount);
        }
        if( $subTotal > $discount and $discount > 0 ){
              $entity->setDiscountType($discountType);
              $entity->setDiscountCalculation($discountCalculation);
              $entity->setDiscount($discount);
              $entity->setTotal($total);
              $entity->setDue($entity->getTotal() - $entity->getPayment());
              $em->flush();
              $msg = 'Discount added successfully';
        }else{
            $msg = 'Discount is not use properly';
        }
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));
        exit;
    }

    public function updateAction(Request $request, Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $referredId = $request->request->get('referredId');
        $data = $request->request->all()['hospitalInvoice'];
        $referred = $request->request->all()['referred'];
        if($editForm->isValid() and !empty($entity->getInvoiceParticulars()) and in_array($entity->getProcess(),array('Created','In-progress','Pending','Revised'))) {
            $referred = $request->request->all()['referred'];
            if(!empty($referred['name'])) {
                $mobile = '';
                if(!empty($data['mobile'])){
                    $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
                }
                $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findHmsExistingReferred($entity->getHospitalConfig() , $mobile,$data);
                $entity->setReferredDoctor($referred);
            }elseif(!empty($referredId)){
                $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $entity->getHospitalConfig() , 'service' => 6, 'id' => $referredId ));
                $entity->setReferredDoctor($referred);
            }
            $deliveryDateTime = $request->request->get('deliveryDateTime');
            $datetime = (new \DateTime("tomorrow"))->format('d-m-Y 7:30');
            $datetime = empty($deliveryDateTime) ? $datetime : $deliveryDateTime ;
            $entity->setDeliveryDateTime($datetime);
            if($entity->getTotal() > 0){
                $entity->setProcess('In-progress');
            }
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            $em->flush();
            if($entity->getTotal() > 0) {
                $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->updateTransaction($entity);
                $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
                $this->getDoctrine()->getRepository('HospitalBundle:Particular')->insertAccessories($entity);
            }
            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.hms_invoice_sms', new \Setting\Bundle\ToolBundle\Event\HmsInvoiceSmsEvent($entity));
            }
            return $this->redirect($this->generateUrl('hms_invoice_confirm', array('id' => $entity->getId())));
        }elseif (in_array($entity->getProcess(),array('In-progress','Done','Cancel'))){
            return $this->redirect($this->generateUrl('hms_invoice_confirm', array('id' => $entity->getId())));
        }

        $referredDoctors = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $entity->getHospitalConfig(),'status'=>1,'service'=> 6),array('name'=>'ASC'));
        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($entity->getHospitalConfig(),array(1,8,7));
        return $this->render('HospitalBundle:Invoice:new.html.twig', array(
            'entity' => $entity,
            'particularService' => $services,
            'referredDoctors' => $referredDoctors,
            'form' => $editForm->createView(),
        ));

    }

    public function discountDeleteAction(Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->updateInvoiceTransactionDiscount($entity);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
        $entity->setDiscountType('null');
        $entity->setDiscountCalculation(0);
        $em->persist($entity);
        $em->flush();
        $msg = 'Discount deleted successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));
        exit;
    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sales = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $sales = $em->getRepository('HospitalBundle:Invoice')->find($sales);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $purchaseItem = $em->getRepository('HospitalBundle:PurchaseItem')->returnPurchaseItemDetails($inventory, $barcode);
        $checkQuantity = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceItem')->checkInvoiceQuantity($purchaseItem);
        $itemStock = $purchaseItem->getItemStock();

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        $device = '';
        if( $detect->isMobile() || $detect->isTablet() ) {
            $device = 'mobile' ;
        }

        if (!empty($purchaseItem) && $itemStock > $checkQuantity) {

            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceItem')->insertInvoiceItems($sales, $purchaseItem);
            $sales = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($sales);
            $salesItems = $em->getRepository('HospitalBundle:InvoiceItem')->getInvoiceItems($sales,$device);
            $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';

        } else {

            $sales = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($sales);
            $salesItems = $em->getRepository('HospitalBundle:InvoiceItem')->getInvoiceItems($sales,$device);
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
        }

        $salesTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
        $salesSubTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
        $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
        return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'purchaseItem' => $purchaseItem, 'salesItem' => $salesItems,'salesVat' => $vat, 'msg' => $msg , 'success' => 'success')));

    }

    public function showAction(Invoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            return $this->render('HospitalBundle:Invoice:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('hms_invoice'));
        }
    }

    public function confirmAction(Invoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            if(in_array($entity->getProcess(),['Hold','Created','Revised','Pending'])){
                $em = $this->getDoctrine()->getManager();
                $entity->setProcess('In-progress');
                $em->persist($entity);
                $em->flush();
            }
            $editForm = $this->createInvoicePaymentForm($entity, new InvoiceTransaction());
            return $this->render('HospitalBundle:Invoice:confirm.html.twig', array(
                'entity' => $entity,
                'paymentForm' => $editForm->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('hms_invoice'));
        }
    }


    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createInvoicePaymentForm(Invoice $invoice, InvoiceTransaction $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new InvoicePaymentType($globalOption), $entity, array(
            'action' => $this->generateUrl('hms_add_invoice_payment', array('id' => $invoice->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoicePayment',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new InvoiceEditType($globalOption), $entity, array(
            'action' => $this->generateUrl('hms_invoice_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function addPaymentAction(Request $request , Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $payment = (float)$data['invoicePayment']['payment'];
        $discount = (float)$data['invoicePayment']['discount'];
        $remark = $request->request->get('remark');
        $discount = $discount !="" ? $discount : 0 ;
        $transaction =  new InvoiceTransaction();
        $transactionForm = $this->createInvoicePaymentForm($entity,$transaction);
        $transactionForm->handleRequest($request);
        if ($transactionForm->isValid() and (!empty($entity) and !empty($payment)) or (!empty($entity) and !empty($discount))) {
            $em = $this->getDoctrine()->getManager();
            $code = $this->getDoctrine()->getRepository(InvoiceTransaction::class)->getLastCode($entity);
            $transaction->setHmsInvoice($entity);
            $transaction->setCode($code + 1);
            $transaction->setProcess("In-progress");
            $transactionCode = sprintf("%s", str_pad($entity->getCode(),2, '0', STR_PAD_LEFT));
            $transaction->setTransactionCode($transactionCode);
            $em->persist($transaction);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hms_invoice_confirm',array('id'=>$entity->getId())));

    }


    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN_HOSPITAL_ADMIN,ROLE_DOMAIN");
     */
    public function deleteAction(Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $em->getRepository('HospitalBundle:InvoiceTransaction')->hmsEditInvoiceTransaction($entity);
        $entity->setIsDelete(1);
        $entity->setInvoiceMode('delete');
        $em->persist($entity);
        $em->flush();
        return new Response(json_encode(array('success' => 'success')));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN_HOSPITAL_ADMIN,ROLE_DOMAIN");
     */
    public function diagnoesticInvoiceReverseAction($invoice){

        /*
         * Stock Details
         * Invoice  Transaction Update
         * Medicine Invoice
         * Account Sales
         * Transaction
         * Delete Journal & Account Purchase
         */

        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital, 'invoice' => $invoice));
        $entity->setRevised(true);
        $entity->setProcess('In-progress');
        $entity->setPaymentStatus('Due');
        $entity->setDue($entity->getTotal());
        $entity->setPaymentInWord(null);
        $entity->setCommission(0);
        $entity->setCommissionApproved(0);
        $entity->setPayment(null);
        $em->flush();
        $template = $this->get('twig')->render('HospitalBundle:Reverse:reverse.html.twig',array(
            'entity' => $entity,
        ));
        $em->getRepository('HospitalBundle:HmsReverse')->insertInvoice($entity,$template);
        $em->getRepository('HospitalBundle:InvoiceTransaction')->hmsSalesTransactionReverse($entity);
        $em->getRepository('HospitalBundle:InvoiceParticular')->hmsInvoiceParticularReverse($entity);
        return $this->redirect($this->generateUrl('hms_invoice_edit',array('id'=>$entity->getId())));
    }

    public function invoiceReverseAction(Invoice $invoice)
    {
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:HmsReverse')->findOneBy(array('hospitalConfig' => $hospital, 'hmsInvoice' => $invoice));
        return $this->render('HospitalBundle:Reverse:show.html.twig', array(
            'entity' => $entity,
        ));
    }

    public function invoiceReverseShowAction(Invoice $invoice)
    {
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:HmsReverse')->findOneBy(array('hospitalConfig' => $hospital, 'hmsInvoice' => $invoice));
        return $this->render('HospitalBundle:Reverse:show.html.twig', array(
            'entity' => $entity,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN_HOSPITAL_ADMIN,ROLE_DOMAIN");
     */

    public function deleteEmptyInvoiceAction()
    {
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findBy(array('hospitalConfig' => $hospital, 'process' => 'Created','invoiceMode'=>'diagnostic'));
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $entity->setIsDelete(1);
            $entity->setInvoiceMode('delete');
            $em->persist($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hms_invoice'));
    }


    public function statusSelectAction()
    {
        $items  = array();
        $items[]= array('value' => 'In-progress','text'=>'In-progress');
        $items[]= array('value' => 'Done','text'=>'Done');
        $items[]= array('value' => 'Canceled','text'=>'Canceled');
        return new JsonResponse($items);
    }

    public function invoiceProcessUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HospitalBundle:Invoice')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $entity->setProcess($data['value']);
        $em->flush();
        exit;

    }

    public function addPatientAction(Request $request,Invoice $invoice)
    {
        $data = $request->request->all();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->patientInsertUpdate($data,$invoice);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->patientAdmissionUpdate($data,$invoice);
        return new Response(json_encode(array('patient' => $customer->getId())));

    }

    public function getBarcode($value)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($value);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(25);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,'.$code .'" />';
        return $data;
    }

    public function invoicePrintAction(Invoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();

        if($entity->getHospitalConfig()->getId() != $hospital->getId()){
            return $this->redirect($this->generateUrl('hms_invoice'));
        }
        $barcode = $this->getBarcode($entity->getInvoice());
        $patientId = $this->getBarcode($entity->getCustomer()->getCustomerId());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());

        $invoiceDetails = ['Pathology' => ['items' => [], 'total'=> 0, 'hasQuantity' => false ]];

        foreach ($entity->getInvoiceParticulars() as $item) {

            /** @var InvoiceParticular $item */

            $serviceName = $item->getParticular()->getService()->getName();
            $hasQuantity = $item->getParticular()->getService()->getHasQuantity();

            if(!isset($invoiceDetails[$serviceName])) {
                $invoiceDetails[$serviceName]['items'] = [];
                $invoiceDetails[$serviceName]['total'] = 0;
                $invoiceDetails[$serviceName]['hasQuantity'] = ( $hasQuantity == 1);
            }

            $invoiceDetails[$serviceName]['items'][] = $item;
            $invoiceDetails[$serviceName]['total'] += $item->getSubTotal();
        }

        if(count($invoiceDetails['Pathology']['items']) == 0) {
            unset($invoiceDetails['Pathology']);
        }
        $lastTransaction = 0 ;
        $inWordTransaction='';
        if(!empty($entity->getInvoiceTransactions()) and count($entity->getInvoiceTransactions()) > 0){
            $transaction = $entity->getInvoiceTransactions();
            if(!empty($transaction[0]->getPayment())){
                $lastTransaction = $transaction[0]->getPayment();
                $inWordTransaction = $this->get('settong.toolManageRepo')->intToWords($lastTransaction);
            }
        }

        if($hospital->isCustomPrint() == 1){
            $template = "Print/{$hospital->getGlobalOption()->getId()}:{$entity->getPrintFor()}";
        }else{
            $template = "Print:{$entity->getPrintFor()}";
        }
        return $this->render("HospitalBundle:{$template}.html.twig", array(
            'entity'                => $entity,
            'invoiceDetails'        => $invoiceDetails,
            'invoiceBarcode'        => $barcode,
            'patientBarcode'        => $patientId,
            'inWords'               => $inWords,
            'lastTransaction'       => $lastTransaction,
            'inWordTransaction'     => $inWordTransaction,
            'global'     => $hospital->getGlobalOption(),
        ));
    }

    public function appointmentPrintAction(Invoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();

        if($entity->getHospitalConfig()->getId() != $hospital->getId()){
            return $this->redirect($this->generateUrl('hms_invoice'));
        }
        $barcode = $this->getBarcode($entity->getInvoice());
        $patientId = $this->getBarcode($entity->getCustomer()->getCustomerId());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());

        if($hospital->isCustomPrint() == 1){
            $template = "Print/{$hospital->getGlobalOption()->getId()}:{$entity->getPrintFor()}";
        }else{
            $template = "Print:{$entity->getPrintFor()}";
        }
        return $this->render("HospitalBundle:{$template}.html.twig", array(
            'entity'                => $entity,
            'config'                => $entity->getHospitalConfig(),
            'global'                => $entity->getHospitalConfig()->getGlobalOption(),
            'invoiceBarcode'        => $barcode,
            'patientBarcode'        => $patientId,
            'inWords'               => $inWords,

        ));
    }

    public function invoiceTransactionApproveAction(InvoiceTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$transaction) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $transaction->setCreatedBy($this->getUser());
        $transaction->setProcess('Done');
        $em->persist($transaction);
        $em->flush();
        if($transaction->getPayment() > 0){
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->admissionInvoiceTransactionUpdate($transaction);
        }
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($transaction->getHmsInvoice());
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($transaction->getHmsInvoice());
        $this->getDoctrine()->getRepository('HospitalBundle:Particular')->admittedPatientAccessories($transaction);
        exit;
    }

    public function invoiceAppointmentApproveAction(Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setProcess('Done');
        $entity->setApprovedBy($this->getUser());
        $entity->setCommissionApproved(1);
        $em->persist($entity);
        $em->flush();
        exit;
    }

    public function invoiceApproveAction(Invoice $invoice)
    {
        if($invoice->getPaymentStatus() == 'Paid' and $invoice->getReportCount() == $invoice->getDeliveryCount()){
            $em = $this->getDoctrine()->getManager();
            $invoice->setApprovedBy($this->getUser());
            $invoice->setProcess('Done');
            if($invoice->getHospitalConfig()->isCommissionAutoApproved() == 1){
                $invoice->setCommissionApproved(1);
            }
            $em->persist($invoice);
            $em->flush();
            $accountInvoice = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertHospitalFinalAccountInvoice($invoice);
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->hmsSalesFinal($invoice, $accountInvoice);
        }
        exit;
    }

    public function invoiceGroupReverseAction(){

        /*
         * Stock Details
         * Invoice  Transaction Update
         * Medicine Invoice
         * Account Sales
         * Transaction
         * Delete Journal & Account Purchase
         */

        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $data = ['created'=>'2019-07-04'];
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists($user, $mode = 'diagnostic', $data);
        $pagination = $entities->getQuery()->getResult();
            /* @var $entity Invoice */
            foreach ($pagination as $entity):
                $entity->setProcess('Revised');
                $entity->setRevised(true);
                $em->flush();
                $em->getRepository('HospitalBundle:InvoiceTransaction')->hmsSalesTransactionReverse($entity);
            endforeach;
        exit;
    }

    public function invoiceGroupApprovedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $data = ['startDate'=>'2019-07-04','endDate'=>'2019-07-04'];
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists($user, $mode = 'diagnostic', $data);
        $pagination = $entities->getQuery()->getResult();
        /* @var $entity Invoice  */
        foreach ($pagination as $invoice):
            if ($invoice->getProcess() == "Revised") {
                $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->insertTransaction($invoice);
                $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($invoice);
                $invoice->setApprovedBy($this->getUser());
                $invoice->setProcess('Done');
                $invoice->setCommissionApproved(true);
                $em->persist($invoice);
                $em->flush();
                $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateCommissionPayment($invoice);
                $accountInvoice = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertHospitalFinalAccountInvoice($invoice);
                $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->hmsSalesFinal($invoice, $accountInvoice);
            }
        endforeach;
        exit;
    }
    public function doctorInvoiceGroupApprovedAction()
    {
        $user = $this->getUser();
        $data = ['startDate'=>'2019-07-04','endDate'=>'2019-07-04'];
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->findWithList($user,$data);
        $pagination = $entities->getQuery()->getResult();
        /* @var $entity DoctorInvoice */
        foreach ($pagination as $entity):
            if($entity->getExpenditure()){
                $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->accountReverse($entity->getExpenditure());
                $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->removeDoctorExpenditure($entity);
            }
            $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->insertCommissionPayment($entity);
        endforeach;
        exit;
    }


}

