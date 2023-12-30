<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\InvoicePaymentType;
use Appstore\Bundle\HospitalBundle\Form\NewPatientAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\PatientAdmissionType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class InvoiceAdmissionController extends Controller
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
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user,$mode ='admission', $data);
        $pagination = $this->paginate($entities);
     //   $salesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'true','admission');
     //   $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'false','admission');
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital->getId());
        $cabins = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(2));
        $cabinGroups = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig'=>$hospital,'service'=>2),array('name'=>'ASC'));
        $departments = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getDepartments($hospital);
        $processes = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAdmissionProcess($hospital,'admission',$data);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignProcess($hospital,'assign-doctor');
        $anesthesiaDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignProcess($hospital,'anesthesia-doctor');
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignProcess($hospital,'referred-doctor');

        return $this->render('HospitalBundle:InvoiceAdmission:index.html.twig', array(
            'entities' => $pagination,
           // 'salesTransactionOverview' => $salesTransactionOverview,
           // 'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'assignDoctors'                     => $assignDoctors,
            'anesthesiaDoctors'                     => $anesthesiaDoctors,
            'referredDoctors'                     => $referredDoctors,
            'employees'                         => $employees,
            'cabinGroups' => $cabinGroups,
            'departments' => $departments,
            'cabins' => $cabins,
            'processes' => $processes,
            'option' => $user->getGlobalOption(),
            'searchForm' => $data,
        ));

    }


    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_ADMISSION,ROLE_DOMAIN_HOSPITAL_VISIT");
     */
    public function bookingCabinAction()
    {
        $entity = new Particular();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $pagination = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $config,'service'=> 2),array('name'=>'ASC'));
        $cabins = $this->getDoctrine()->getRepository(Invoice::class)->getBookingCabinLists($config);
        return $this->render('HospitalBundle:InvoiceAdmission:cabin.html.twig', array(
            'pagination' => $pagination,
            'cabins' => $cabins,
            'option' => $this->getUser()->getGlobalOption(),
        ));

    }

    public function downloadAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user,$mode ='admission', $data);
        $pagination = $this->paginate($entities);

        $salesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'true','admission');
        $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'false','admission');

        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $cabins = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(2));
        $cabinGroups = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig'=>$hospital,'service'=>2),array('name'=>'ASC'));

        return $this->render('HospitalBundle:InvoiceAdmission:index.html.twig', array(
            'entities' => $pagination,
            'salesTransactionOverview' => $salesTransactionOverview,
            'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'assignDoctors' => $assignDoctors,
            'referredDoctors' => $referredDoctors,
            'cabinGroups' => $cabinGroups,
            'cabins' => $cabins,
            'option' => $user->getGlobalOption(),
            'searchForm' => $data,
        ));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_ADMISSION,ROLE_DOMAIN_HOSPITAL_VISIT");
     */
    public function newAction()
    {
        $entity = new Invoice();
        $globalOption = $this->getUser()->getGlobalOption();
        $hospital = $globalOption->getHospitalConfig();
        $form   = $this->createCreateForm($entity);
        return $this->render('HospitalBundle:InvoiceAdmission:new.html.twig', array(
            'hospital' => $hospital,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entity = new Invoice();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createCreateForm($entity);
        $editForm->handleRequest($request);
       // $error = $this->getErrorMessages($editForm);
        if ($editForm->isValid()) {
            $entity->setHospitalConfig($hospital);
            $entity->getCustomer()->setGlobalOption($user->getGlobalOption());
            $entity->setProcess('Admitted');
            $entity->setInvoiceMode('admission');
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository(InvoiceTransaction::class)->insertDefaultAdmissionParticular($entity);
            return $this->redirect($this->generateUrl('hms_invoice_admission_daily_invoice',array('invoice' => $entity->getInvoice())));
        }
        return $this->render('HospitalBundle:InvoiceAdmission:new.html.twig', array(
            'entity' => $entity,
            'hospital' => $hospital,
            'form' => $editForm->createView(),
        ));
    }

    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = (string) $child->getErrors(true, false);
            }
        }
        return $errors;
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $cabins = $this->getDoctrine()->getRepository(Invoice::class)->getExistCabin($globalOption->getHospitalConfig());
        $form = $this->createForm(new NewPatientAdmissionType($globalOption,$category ,$location,$cabins), $entity, array(
            'action' => $this->generateUrl('hms_invoice_admitted_create', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'admissionForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    public function cabinAdmissionAction(Particular $cabin)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Invoice();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity->setHospitalConfig($hospital);
        $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(1);
        $entity->setService($service);
        $entity->setCabin($cabin);
        $entity->setPaymentStatus('Pending');
        $entity->setInvoiceMode('admission');
        $entity->setProcess('Admitted');
        $entity->setPrintFor('admission');
        $entity->setCreatedBy($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_invoice_admitted_patient_edit', array('id' => $entity->getId())));

    }

    public function oldPatientAction(Request $request)
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
        if($customer){
            $entity = new Invoice();
            $entity->setHospitalConfig($hospital);
            $entity->setCustomer($customer);
            $entity->setInvoiceMode('admission');
            $entity->setPrintFor('admission');
            $entity->setProcess('Admitted');
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('hms_invoice_admitted_patient_edit', array('id' => $entity->getId())));
        }elseif($invoice){
            $customer = $invoice->getCustomer();
            $entity = new Invoice();
            $entity->setHospitalConfig($hospital);
            $entity->setCustomer($customer);
            $entity->setInvoiceMode('admission');
            $entity->setPrintFor('admission');
            $entity->setProcess('Admitted');
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('hms_invoice_admitted_patient_edit', array('id' => $entity->getId())));
        }elseif($patientId){
            $entity = new Invoice();
            $entity->setHospitalConfig($hospital);
            $entity->setCustomer($patientId);
            $entity->setInvoiceMode('admission');
            $entity->setPrintFor('admission');
            $entity->setProcess('Admitted');
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('hms_invoice_admitted_patient_edit', array('id' => $entity->getId())));
        }
        return $this->redirect($this->generateUrl('hms_invoice_admission'));
    }

    public function admittedPatientAction(Invoice $invoice)
    {
        $customer = $invoice->getCustomer();
        $em = $this->getDoctrine()->getManager();
        $entity = new Invoice();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity->setHospitalConfig($hospital);
        $entity->setCustomer($customer);
        $entity->setParent($invoice);
        $entity->setProcess('Admitted');
        $entity->setInvoiceMode('admission');
        $entity->setPrintFor('admission');
        $entity->setCreatedBy($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_invoice_admitted_patient_edit', array('id' => $entity->getId())));

    }

    public function admittedPatientUpdateAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createAdmissionForm($entity);
        return $this->render('HospitalBundle:InvoiceAdmission:patient.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        ));
    }

    public function updatePatientAction(Request $request, Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createAdmissionForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $entity->setProcess('Admitted');
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('hms_invoice_admission'));
        }
         return $this->render('HospitalBundle:InvoiceAdmission:patient.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_ADMISSION,ROLE_DOMAIN_HOSPITAL_VISIT");
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($hospital,array(2,3,4,8,7));
        $referredDoctors = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $hospital,'status' => 1,'service' => 6),array('name'=>'ASC'));
        return $this->render('HospitalBundle:InvoiceAdmission:invoice.html.twig', array(
            'entity' => $entity,
            'particularService' => $services,
            'referredDoctors' => $referredDoctors,
            'admissionForm' => 'hide',
            'form' => $editForm->createView(),
        ));
    }





    public function patientInvoiceAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
       /* if ($entity->getProcess() != "Admitted" and $entity->getProcess() != "Created") {
            return $this->redirect($this->generateUrl('hms_invoice_admission_show', array('id' => $entity->getId())));
        }*/
        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($hospital,array(2,3,4,8,7));
        $referredDoctors = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $hospital,'status' => 1,'service' => 6),array('name'=>'ASC'));
        return $this->render('HospitalBundle:InvoiceAdmission:new.html.twig', array(
            'entity' => $entity,
            'particularService' => $services,
            'referredDoctors' => $referredDoctors,
            'admissionForm' => 'hide',
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
    private function createEditForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new InvoiceAdmissionType($globalOption,$location,$category), $entity, array(
            'action' => $this->generateUrl('hms_invoice_admission_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
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
    private function createAdmissionForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $admission = $this->getDoctrine()->getRepository('HospitalBundle:Invoice');
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $category = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $form = $this->createForm(new PatientAdmissionType($globalOption,$admission,$location,$category), $entity, array(
            'action' => $this->generateUrl('hms_invoice_admitted_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function updateOldAction(Request $request, Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $referredId = $request->request->get('referredId');
        $data = $request->request->all()['appstore_bundle_hospitalbundle_invoice'];
        if ($editForm->isValid()) {

            if (!empty($data['customer']['name'])) {

                 $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customer']['mobile']);
                 $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findHmsExistingCustomer($this->getUser()->getGlobalOption(), $mobile,$data);
                 $entity->setCustomer($customer);
                 $entity->setMobile($mobile);

            }
            if(!empty($data['referredDoctor']['name']) && !empty($data['referredDoctor']['mobile'])) {

                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['referredDoctor']['mobile']);
                $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findHmsExistingCustomer($entity->getHospitalConfig() , $mobile,$data);
                $entity->setReferredDoctor($referred);
            }else{
                $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $entity->getHospitalConfig() , 'service' => 6, 'id' => $referredId ));
                $entity->setReferredDoctor($referred);
            }
            if($entity->getTotal() > 0 ) {
                $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
                $entity->setProcess('Admitted');
                $entity->setPrintFor('admitted');
                $entity->setPaymentInWord($amountInWords);
            }
            $em->flush();

            if($entity->getTotal() > 0 ){

                $transaction = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->initialInsertInvoiceTransaction($entity);
                $this->getDoctrine()->getRepository('HospitalBundle:AdmissionPatientParticular')->initialUpdateInvoiceParticulars($entity,$transaction);
                $this->getDoctrine()->getRepository('HospitalBundle:Particular')->insertAccessories($entity);
                $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
            }

            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                 $dispatcher = $this->container->get('event_dispatcher');
                 $dispatcher->dispatch('setting_tool.post.hms_invoice_sms', new \Setting\Bundle\ToolBundle\Event\HmsInvoiceSmsEvent($entity));
            }
            if(!empty($entity->getInvoiceParticulars()) and count($entity->getInvoiceParticulars()) > 0){
                return $this->redirect($this->generateUrl('hms_invoice_admission_confirm', array('id' => $entity->getId())));
            }
            return $this->redirect($this->generateUrl('hms_invoice_admission_edit', array('id' => $entity->getId())));
        }

        $referredDoctors = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $entity->getHospitalConfig(),'status'=>1,'service'=> 6),array('name'=>'ASC'));
        $particulars = $em->getRepository('HospitalBundle:Particular')->getServices($entity->getHospitalConfig(),array(2,3,4,8,7));
        return $this->render('HospitalBundle:InvoiceAdmission:new.html.twig', array(
            'entity' => $entity,
            'particularService' => $particulars,
            'referredDoctors' => $referredDoctors,
            'admissionForm' => 'show',
            'form' => $editForm->createView(),
        ));
    }

    public function updateAction(Request $request, Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            if($entity->getReceive() > 0 ){
               // $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->initialInsertAdmissionInvoiceTransaction($entity);
                $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
            }
        }
        $invoiceTransaction = $this->renderView("HospitalBundle:InvoiceAdmission:invoice-transaction.html.twig", array(
            'entity' => $entity
        ));
        $data = array(
            'subTotal' => $entity->getSubTotal(),
            'total' => $entity->getTotal(),
            'payment' => $entity->getPayment() ,
            'due' => $entity->getDue(),
            'discount' => $entity->getDiscount(),
            'invoiceTransaction' => $invoiceTransaction,
        );
        return new Response(json_encode($data));
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
        }elseif($discountType == 'percent' and $discountCalculation > 0 ){
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
        }
        $data = array(
            'subTotal' => $subTotal,
            'total' => $entity->getTotal(),
            'payment' => $entity->getPayment() ,
            'due' => $entity->getDue(),
            'discount' => $discount,
        );
        return new Response(json_encode($data));
    }

    public function particularSearchAction()
    {
        $id = $_REQUEST['id'];
        $particular = $this->getDoctrine()->getRepository("HospitalBundle:Particular")->find($id);
        $quantity = $particular->getQuantity() > 0 ? $particular->getQuantity() :1;
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $quantity, 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(Invoice $entity,$msg=''){

        $invoiceParticulars = $this->renderView("HospitalBundle:InvoiceAdmission:invoice-item.html.twig", array(
            'entity' => $entity
        ));
        $invoiceTransaction = $this->renderView("HospitalBundle:InvoiceAdmission:invoice-transaction.html.twig", array(
            'entity' => $entity
        ));
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
            'subTotal' => $subTotal,
            'total' => $entity->getTotal(),
            'payment' => $entity->getPayment() ,
            'due' => $entity->getDue(),
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
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateAdmissionPaymentReceive($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));

    }

    public function invoiceParticularDeleteAction(Invoice $invoice, InvoiceParticular $particular){
    }

    public function invoiceParticularUpdateAction(Request $request)
    {

    }

    public function invoiceTransactionDeleteAction($invoice, InvoiceTransaction $particular){

        $em = $this->getDoctrine()->getManager();
        $em->remove($particular);
        $em->flush();
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->find($invoice);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateAdmissionPaymentReceive($entity);
        $invoiceTransaction = $this->renderView("HospitalBundle:InvoiceAdmission:invoice-transaction.html.twig", array(
            'entity' => $entity
        ));
        $result = array(
            'invoiceTransaction' => $invoiceTransaction,
            'payment' => $entity->getPayment(),
        );
        return new Response(json_encode($result));

    }

    public function invoiceTransactionApproveAction($invoice ,InvoiceTransaction $particular)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->find($invoice);
        $particular->setApprovedBy($this->getUser());
        $particular->setProcess("Done");
        $em->flush();
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateAdmissionPaymentReceive($entity);
        $invoiceTransaction = $this->renderView("HospitalBundle:InvoiceAdmission:invoice-transaction.html.twig", array(
            'entity' => $entity
        ));
        $data = array(
            'subTotal' => $entity->getSubTotal(),
            'total' => $entity->getTotal(),
            'payment' => $entity->getPayment() ,
            'due' => $entity->getDue(),
            'discount' => $entity->getDiscount(),
            'invoiceTransaction' => $invoiceTransaction,
        );
        return new Response(json_encode($data));
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


    public function showAction(Invoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            return $this->render('HospitalBundle:InvoiceAdmission:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('hms_invoice_admission'));
        }

    }

    public function admissionCardAction(Invoice $entity)
    {

        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        if ($hospital->getId() == $entity->getHospitalConfig()->getId()) {
            if($hospital->isCustomPrint() == 1){
                $template = "Print/{$hospital->getGlobalOption()->getId()}";
            }else{
                $template = "Print";
            }
            $barcode = $this->getBarcode($entity->getCustomer()->getPatientId());
            return $this->render("HospitalBundle:{$template}:admission-card.html.twig", array(
                'config' => $hospital,
                'barcode' => $barcode,
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('hms_invoice_admission'));
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
            'action' => $this->generateUrl('hms_invoice_admission_approved', array('id' => $invoice->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoicePayment',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function confirmAction(Invoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            $editForm = $this->createInvoicePaymentForm($entity, new InvoiceTransaction());
            return $this->render('HospitalBundle:InvoiceAdmission:confirm.html.twig', array(
                'entity' => $entity,
                'paymentForm' => $editForm->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('hms_invoice_admission'));
        }
    }


    public function approveAction(Request $request , Invoice $entity)
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
        return $this->redirect($this->generateUrl('hms_invoice_admission_confirm',array('id'=>$entity->getId())));
    }

    public function dischargeAction($id)
    {
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig'=>$inventory,'id'=>$id));
        /* @var $entity Invoice */
        $entity->setDeliveredBy($this->getUser());
        $entity->setMedicine($data['medicine']);
        $entity->setAdvice($data['advice']);
        $entity->setCaseOfDeath($data['caseOfDeath']);
        $entity->setDoctorComment($data['doctorComment']);
        $entity->setProcess("Release");
        $entity->setDischargeBy($this->getUser());
        $em->flush();
        return new Response('success');
    }

    public function releaseAction($invoice,$process)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        /* @var $entity Invoice */
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig'=>$inventory,'invoice'=>$invoice));
        if($process == 'confirm'){
            if($entity->getProcess() == 'Release'){
                $entity->setProcess('Released');
            }elseif($entity->getProcess() == 'Death'){
                $entity->setProcess('Dead');
            }
            $entity->setApprovedBy($this->getUser());
            $date = new \DateTime("now");
            $entity->setReleaseDate($date);
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->removePendingTransaction($entity);
            $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->insertHospitalFinalAccountInvoice($entity);
        }elseif($process == 'cancel'){
            $entity->setProcess('Admitted');
        }
        $em->flush();
        return new Response('success');
    }

    public function admittedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user,$mode ='admission', $data);
        $pagination = $this->paginate($entities);
        $overview = $em->getRepository('HospitalBundle:DoctorInvoice')->findWithOverview($user,$data);
        $invoiceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithOverview($user,$data);

        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(6));

        return $this->render('HospitalBundle:InvoiceAdmission:admitted.html.twig', array(
            'entities' => $pagination,
            'invoiceOverview' => $invoiceOverview,
            'overview' => $overview,
            'assignDoctors' => $assignDoctors,
            'referredDoctors' => $referredDoctors,
            'searchForm' => $data,
        ));

    }


    public function getBarcode($invoice)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($invoice);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(25);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,'.$code .'" />';
        return $data;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN_HOSPITAL_ADMIN,ROLE_DOMAIN");
     */

    public function deleteEmptyInvoiceAction()
    {
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findBy(array('hospitalConfig' => $hospital, 'process' => 'Created','invoiceMode'=>'admission'));
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $entity->setIsDelete(1);
            $entity->setInvoiceMode('delete');
            $em->persist($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hms_invoice_admission'));
    }


    public function invoicePrintAction(Invoice $entity)
    {

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
        $hospital = $entity->getHospitalConfig();
        if($hospital->isCustomPrint() == 1){
            if(in_array($entity->getProcess(),array('Admitted'))){
                $template = "Print/{$hospital->getGlobalOption()->getId()}:admitted";
            }elseif(in_array($entity->getProcess(),array('Released'))){
                $template = "Print/{$hospital->getGlobalOption()->getId()}:released";
            }elseif(in_array($entity->getProcess(),array('Dead'))){
                $template = "Print/{$hospital->getGlobalOption()->getId()}:dead";
            }
        }else{
            if(in_array($entity->getProcess(),array('Admitted'))){
                $template = "Print:admission";
            }elseif(in_array($entity->getProcess(),array('Released'))){
                $template = "Print:released";
            }elseif(in_array($entity->getProcess(),array('Dead'))){
                $template = "Print:dead";
            }
        }
        return $this->render("HospitalBundle:{$template}.html.twig", array(
            'entity'             => $entity,
            'invoiceDetails'     => $invoiceDetails,
            'invoiceBarcode'     => $barcode,
            'patientBarcode'     => $patientId,
            'inWords'            => $inWords,
        ));
    }

    public function admissionPrintAction(Invoice $entity)
    {

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
        $print = "admitted";
        return $this->render("HospitalBundle:Print:{$print}.html.twig", array(
            'entity'               => $entity,
            'invoiceDetails'        => $invoiceDetails,
            'invoiceBarcode'     => $barcode,
            'patientBarcode'     => $patientId,
            'inWords'           => $inWords,
        ));
    }

    public function paymentReceiveInvoicePrintAction($invoice, InvoiceTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));
        $barcode = $this->getBarcode($entity->getInvoice());
        $patientId = $this->getBarcode($entity->getCustomer()->getCustomerId());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());
        $inWordTransaction = $this->get('settong.toolManageRepo')->intToWords($transaction->getPayment());
        return $this->render('HospitalBundle:Print:receive.html.twig', array(
            'entity'                => $entity,
            'invoiceBarcode'        => $barcode,
            'patientBarcode'        => $patientId,
            'inWords'               => $inWords,
            'transaction'           => $transaction,
            'inWordTransaction'     => $inWordTransaction,
        ));
    }


     public function paymentTransactionInvoicePrintAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));
        $barcode = $this->getBarcode($entity->getInvoice());
        $patientId = $this->getBarcode($entity->getCustomer()->getCustomerId());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());
        return $this->render('HospitalBundle:Print:transaction.html.twig', array(
            'entity'                => $entity,
            'invoiceBarcode'        => $barcode,
            'patientBarcode'        => $patientId,
            'inWords'               => $inWords,
        ));
    }


    public function invoicePrintForAction(Request $request, Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $print = $request->request->get('printFor');
        $invoice->setPrintFor($print);
        $em->persist($invoice);
        $em->flush();
        exit;
    }

    public function checkPatientCabinBookingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cabin = $request->request->get('cabin');
        $invoice = $request->request->get('invoice');
        $status = $em->getRepository('HospitalBundle:Invoice')->checkCabinBooking($invoice,$cabin);
        echo $status;
        exit;
    }

    public function newCheckPatientCabinBookingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        $cabin = $request->request->get('cabin');
        $status = $em->getRepository('HospitalBundle:Invoice')->checkNewCabinBooking($config,$cabin);
        echo $status;
        exit;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN");
     */

    public function admissionInvoiceReverseAction($invoice){

        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital, 'invoice' => $invoice));
        $em->getRepository('HospitalBundle:InvoiceTransaction')->hmsAdmissionSalesTransactionReverse($entity);
        $em->getRepository('HospitalBundle:InvoiceParticular')->hmsInvoiceParticularReverse($entity);
        $entity->setRevised(true);
        $entity->setTotal($entity->getSubTotal());
        $entity->setProcess('Admitted');
        $entity->setPaymentStatus('Due');
        $entity->setDiscount(null);
        $entity->setPaymentInWord(null);
        $entity->setPayment(null);
        $em->flush();
        $template = $this->get('twig')->render('HospitalBundle:Reverse:admission.html.twig',array(
            'entity' => $entity,
        ));
        $em->getRepository('HospitalBundle:HmsReverse')->insertAdmissionInvoice($entity,$template);
        return $this->redirect($this->generateUrl('hms_invoice_admission_confirm',array('id' => $entity->getId())));

    }


    public function admissionInvoiceReverseShowAction($invoice)
    {
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $admission = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital, 'invoice' => $invoice));
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:HmsReverse')->findOneBy(array('hospitalConfig' => $hospital, 'hmsInvoice' => $admission->getId()));
        return $this->render('HospitalBundle:Reverse:admissionShow.html.twig', array(
            'entity' => $entity
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

    public function cabinSelectAction(Invoice $invoice)
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $cabin = ($invoice->getCabin()) ? $invoice->getCabin()->getId() : 0;
        $cabins = $this->getDoctrine()->getRepository(Invoice::class)->getExistCabin($config,$cabin);
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getCurrentCabins($config,2,$cabins);
        $items = array();
        $items[] = array('value' => '','text'=> '-Change Patient Cabin-');
        foreach ($entities as $entity):
            $items[] = array('value' => $entity['id'],'text'=> $entity['name']);
        endforeach;
        $items[]=array('value' => '0','text'=> 'Empty Cabin');
        return new JsonResponse($items);

    }

    public function inlineCabinUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Invoice::class)->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        if($data['name'] == 'Cabin'){
            $setValue = $em->getRepository(Particular::class)->find($data['value']);
            if($setValue){
                $entity->setCabin($setValue);
            }else {
                $entity->setCabin(NULL);
            }
        }
        $em->persist($entity);
        $em->flush();
        exit;

    }


    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_ADMISSION,ROLE_DOMAIN_HOSPITAL_VISIT");
     */
    public function finalBillAction(Invoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            return $this->render('HospitalBundle:InvoiceAdmission:finalbill.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('hms_invoice_admission'));
        }

    }
}

