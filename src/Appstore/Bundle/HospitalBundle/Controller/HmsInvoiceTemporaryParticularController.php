<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceTemporaryParticular;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceType;
use Core\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class HmsInvoiceTemporaryParticularController extends Controller
{

    public function newAction()
    {
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entity = new Invoice();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createInvoiceCustomerForm($entity);
        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($hospital,array(1,8,7));
        $referredDoctors    = $em->getRepository('HospitalBundle:Particular')->getServiceWithParticular($hospital,array(5,6));;
        $subTotal = $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->getSubTotalAmount($user);
        $html = $this->renderView('HospitalBundle:Invoice:diagnostic.html.twig', array(
            'temporarySubTotal'   => $subTotal,
            'hospital'   => $hospital,
            'initialDiscount'   => 0,
            'user'   => $user,
            'entity'   => $entity,
            'particularService' => $services,
            'referredDoctors' => $referredDoctors,
            'form'   => $form->createView(),
        ));
        return New Response($html);
    }


    private function createInvoiceCustomerForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new InvoiceType($globalOption,$category ,$location), $entity, array(
            'action' => $this->generateUrl('hms_invoice_temporary_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal',
                'id' => 'invoicePatientForm',
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
        $discountType = $request->request->get('discountType');
        $patientId = $request->request->get('customerId');
        $admissionId = $request->request->get('admissionId');
        $record = $request->request->all();
        $data = $record['appstore_bundle_hospitalbundle_invoice'];
        $referredId = $data['referredId'];
        $consultantId = $data['consultant'];
        $entity->setHospitalConfig($hospital);
        $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(1);
        $entity->setService($service);
        $referredDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital,'name'=>'Self','service' => 6));
        $entity->setReferredDoctor($referredDoctor);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        if($admissionId){
            $admission = $this->getDoctrine()->getRepository(Invoice::class)->findOneBy(array('hospitalConfig' => $hospital,'invoice'=>$admissionId));
            $entity->setParent($admission);
            $entity->setMarketingExecutive($admission->getMarketingExecutive());
            $entity->setReferredDoctor($admission->getReferredDoctor());
        }
        $entity->setTransactionMethod($transactionMethod);
        $entity->setPaymentStatus('Pending');
        $entity->setInvoiceMode('diagnostic');
        $entity->setPrintFor('diagnostic');
        $entity->setCreatedBy($this->getUser());
        if (!empty($data['customer']['name'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customer']['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findHmsExistingCustomerDiagnostic($this->getUser()->getGlobalOption(), $mobile,$patientId,$data);
            $entity->setCustomer($customer);
            $entity->setMobile($mobile);
        }
        if(!empty($data['assignDoctor']['name']) && !empty($data['assignDoctor']['mobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['assignDoctor']['mobile']);
            $doctor = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findHmsExistingDoctor($hospital , $mobile,$data);
            $entity->setAssignDoctor($doctor);
        }elseif(!empty($consultantId)){
            $consultant = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->find($consultantId);
            $entity->setAssignDoctor($consultant);
        }
        if(!empty($data['referredDoctor']['name']) && !empty($data['referredDoctor']['mobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['referredDoctor']['mobile']);
            $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findHmsExistingReferred($hospital , $mobile,$data);
            $entity->setReferredDoctor($referred);
        }elseif(!empty($referredId)){
            $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->find($referredId);
            $entity->setReferredDoctor($referred);
        }
        $deliveryDateTime = $request->request->get('deliveryDateTime');
        $datetime = (new \DateTime("now"))->format('d-m-Y 7:30 A');
        $datetime = empty($deliveryDateTime) ? $datetime : $deliveryDateTime ;
        if($deliveryDateTime){
            $entity->setDeliveryDateTime($datetime);
        }
        $entity->setDiscountType($discountType);
        if($entity->getTotal() > 0 and $entity->getPayment() >= $entity->getTotal() ){
	        $entity->setPayment($entity->getTotal());
	        $entity->setPaymentStatus("Paid");
	        $entity->setDue(0);
        }
        if(isset($data['isHold']) and $data['isHold'] == 1){
            $entity->setProcess('Hold');
        }else{
            $entity->setProcess('In-progress');
        }
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        $entity->setPaymentInWord($amountInWords);
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->insertMasterParticular($user,$entity);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($entity);
        $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->insertTransaction($entity);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
        $this->getDoctrine()->getRepository('HospitalBundle:Particular')->insertAccessories($entity);
        if($hospital->getInitialDiagnosticShow() != 1){
            $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->removeInitialParticular($user);
        }
        $data = array(
            'entity' => $entity->getId(),
            'process' => $entity->getProcess(),
            'success' => 'success'
        );
        return new Response(json_encode($data));



    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $user = $this->getUser();
        $discount = (float)$request->request->get('discount');
        $discountType = $request->request->get('discountType');
        $subTotal = $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->getSubTotalAmount($user);
        if($discountType == 'flat'){
            $initialGrandTotal = ($subTotal  - $discount);
        }else{
            $discount = ($subTotal * $discount)/100;
            $initialGrandTotal = ($subTotal  - $discount);
        }
        $data = array(
            'subTotal' => $subTotal,
            'initialGrandTotal' => $initialGrandTotal,
            'initialDiscount' => $discount,
            'success' => 'success'
        );
        return new Response(json_encode($data));


    }

    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(User $user,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->getSalesItems($user);
        $subTotal = $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->getSubTotalAmount($user);
        $data = array(
           'subTotal' => $subTotal,
           'initialGrandTotal' => $subTotal,
           'invoiceParticulars' => $invoiceParticulars ,
           'msg' => $msg ,
           'success' => 'success'
       );
       return $data;

    }

    public function addParticularAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->insertInvoiceItems($user, $invoiceItems);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));


    }

     public function searchAddParticularAction(Particular $particular)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $particularId =$particular->getId();
        $quantity =1;
        $price = $particular->getPrice() ;
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->insertInvoiceItems($user, $invoiceItems);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));


    }

    public function invoiceParticularDeleteAction(HmsInvoiceTemporaryParticular $particular){


        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $msg = 'Particular deleted successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));

    }

}

