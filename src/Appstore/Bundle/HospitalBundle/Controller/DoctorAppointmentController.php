<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HmsDoctorVisitMode;
use Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceTemporaryParticular;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\DoctorAppointmentType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Core\UserBundle\Entity\User;
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
class DoctorAppointmentController extends Controller
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
     * @Secure(roles="ROLE_HOSPITAL,ROLE_DOMAIN");
     */

    public function appointmentInvoiceAction()
    {
        ini_set('max_execution_time', 0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user , $mode = 'visit' , $data);
        $records = $entities->getResult();
        foreach ($records as $invoice){
            if($invoice->getProcess() == "Done"){
                $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->insertVisitTransaction($invoice);
            }
        }
        $pagination = $this->paginate($entities);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5,6));
        return $this->render('HospitalBundle:Prescription:index.html.twig', array(
            'entities'                          => $pagination,
            'assignDoctors'                     => $assignDoctors,
            'searchForm'                        => $data,
        ));

    }

    public function newAction()
    {
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entity = new Invoice();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createInvoiceCustomerForm($entity);
        $html = $this->renderView('HospitalBundle:Prescription:appointment.html.twig', array(
            'initialDiscount'   => 0,
            'user'   => $user,
            'entity'   => $entity,
            'hospital'   => $hospital,
            'form'   => $form->createView(),
        ));
        return New Response($html);
    }


    private function createInvoiceCustomerForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $form = $this->createForm(new DoctorAppointmentType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('hms_doctor_visit_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal',
                'id' => 'appointmentPatientForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = New Invoice();
        $user = $this->getUser();
        $option = $user->getGlobalOption();
        $hospital = $option->getHospitalConfig();
        $editForm = $this->createInvoiceCustomerForm($entity);
        $editForm->handleRequest($request);
        $appointment = $request->request->all();
        $data = $appointment['appointment_invoice'];
        $isConfirm = isset($data['isConfirm']) ? $data['isConfirm'] :'';
        $entity->setHospitalConfig($hospital);
        $assignDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->find($data['assignDoctor']);
        $entity->setAssignDoctor($assignDoctor);
       // $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
       // $entity->setTransactionMethod($transactionMethod);
        $entity->setInvoiceMode('visit');
        $entity->setPrintFor('visit');
        $entity->setCreatedBy($this->getUser());
        if (!empty($data['customer']['name'])) {
            $patientId = $request->request->get('patientId');
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customer']['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findHmsExistingCustomerDiagnostic($this->getUser()->getGlobalOption(), $mobile,$patientId,$data);
            $entity->setCustomer($customer);
            $entity->setMobile($mobile);
        }
        $entity->setSubTotal($assignDoctor->getPrice());
        $entity->setTotal($entity->getPayment());
        if($entity->getPayment() > 0 and $isConfirm == 1 ){
            $entity->setApprovedBy($this->getUser());
            $entity->setProcess('Done');
            $entity->setPaymentStatus("Paid");
            $entity->setDue(0);
        }else{
            $entity->setDue($assignDoctor->getPrice());
            $entity->setProcess('In-progress');
        }
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        $entity->setPaymentInWord($amountInWords);
        $em->persist($entity);
        $em->flush();
        if($entity->getPayment() > 0 and $isConfirm == 1 ){
            $this->invoiceApproveAction($entity);
        }
        $prescriptionLink = $this->generateUrl('hms_prescription_doctor_print',array('id' => $entity->getId()));
        $printLink = $this->generateUrl('hms_prescription_print',array('id'=> $entity->getId()));
        $array = array(
            'save'=> isset($appointment['save']) ? $appointment['save'] :'',
            'print'=> isset($appointment['print']) ? $appointment['print'] :'',
            'isConfirm'=> $isConfirm,
            'prescriptionLink' =>"<a target='_blank' class='btn blue' href='{$prescriptionLink}'>Print Prescription</a>",
            'printLink' => $printLink,
            'invoice' => $entity->getId(),
        );
        return new Response(json_encode($array));

    }

    public function doctorVisitAmountAction(Particular $particular)
    {
        $visit = "";
        $visitType = isset($_REQUEST['visitType']) ? $_REQUEST['visitType'] :'';
        if($visitType){
            $visit = $this->getDoctrine()->getRepository(HmsDoctorVisitMode::class)->findOneBy(array('doctor'=>$particular,'service'=>$visitType));
        }
        $amount = empty($visit) ? $particular->getPrice() : $visit->getAmount();
        return new Response($amount);
    }

    public function invoiceApproveAction(Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $invoice->setApprovedBy($this->getUser());
        $invoice->setProcess('Done');
        $em->persist($invoice);
        $em->flush();
        if($invoice->getPayment() > 0) {
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->insertVisitTransaction($invoice);
        }
        if($invoice->getAssignDoctor()->isSendToAccount() == 1){
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertHospitalVisitAccount($invoice);
        }
        return new Response('Success');
    }

}

