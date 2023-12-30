<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\HospitalBundle\Entity\AdmissionPatientParticular;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceAdmittedParticularType;
use Appstore\Bundle\HospitalBundle\Form\InvoicePaymentType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceType;
use Appstore\Bundle\HospitalBundle\Form\NewPatientAdmissionType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class AdmissionPatientParticularController extends Controller
{

    public function invoiceAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'invoice' => $invoice));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($hospital,array(1,2,3,4,8,7));
        return $this->render('HospitalBundle:InvoiceAdmission:admissionPatientParticular.html.twig', array(
            'entity' => $entity,
            'particularService' => $services,
        ));
    }

    public function admittedInvoiceAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'invoice' => $invoice));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $code = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->getLastCode($entity);
        $it = new InvoiceTransaction();
        $it->setCode($code + 1);
        $transactionCode = sprintf("%s", str_pad($it->getCode(),2, '0', STR_PAD_LEFT));
        $it->setTransactionCode($transactionCode);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $it->setTransactionMethod($transactionMethod);
        $it->setProcess('Pending');
        $it->setHmsInvoice($entity);
        $it->setCreatedBy($this->getUser());
        $em->persist($it);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_invoice_admission_daily_invoice_create', array('invoice' => $entity->getInvoice(),'id' => $it->getId())));

    }


    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createInvoicePaymentForm(InvoiceTransaction $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new InvoicePaymentType($globalOption), $entity, array(
            'action' => $this->generateUrl('hms_invoice_admission_daily_invoice_transaction_submit', array('invoice' => $entity->getHmsInvoice()->getInvoice(),'id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'newInvoicePayment',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function dailyAdmittedInvoiceAction($invoice, InvoiceTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        if($transaction->getProcess() == "Done") {
            $this->getDoctrine()->getRepository(AccountSales::class)->insertHospitalInvoiceDelete($transaction);
            foreach ($transaction->getAdmissionPatientParticulars() as $row){
                $this->getDoctrine()->getRepository(InvoiceParticular::class)->reverseInvoiceParticularMasterUpdate($row);
            }

            $count =  count($transaction->getAdmissionPatientParticulars());
            if($count > 0){
                $transaction->setProcess('Created');
            }else{
                $transaction->setProcess('In-progress');
            }
            $em->flush();
            $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($transaction->getHmsInvoice());
            $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($transaction->getHmsInvoice());
            $this->getDoctrine()->getRepository('HospitalBundle:Particular')->admittedPatientAccessories($transaction);
        }
        if($transaction->getProcess() == 'In-progress'){
            return $this->redirect($this->generateUrl('hms_invoice_admission_confirm', array('id' => $transaction->getHmsInvoice()->getId())));
        }
        $particular = new AdmissionPatientParticular();
        $particularForm = $this->createCreateForm($particular);
        $editForm = $this->createInvoicePaymentForm($transaction);
        return $this->render('HospitalBundle:InvoiceAdmission:confirm.html.twig', array(
            'entity' => $entity,
            'invoiceTransaction' => $transaction,
            'form' => $particularForm->createView(),
            'paymentForm' => $editForm->createView(),
        ));
    }

    public function submitTransactionAction(Request $request , InvoiceTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $payment = (float)$data['invoicePayment']['payment'];
        $transactionForm = $this->createInvoicePaymentForm($transaction);
        $transactionForm->handleRequest($request);
        if ($transactionForm->isValid() and (!empty($transaction)) and count($transaction->getAdmissionPatientParticulars()) > 0) {
            $em = $this->getDoctrine()->getManager();
            $transaction->setProcess("In-progress");
            $em->persist($transaction);
            $em->flush();
            return $this->redirect($this->generateUrl('hms_invoice_admission_confirm', array('id' => $transaction->getHmsInvoice()->getId())));
        }
        $particular = new AdmissionPatientParticular();
        $particularForm = $this->createCreateForm($particular);
        $editForm = $this->createInvoicePaymentForm($transaction);
        return $this->render('HospitalBundle:InvoiceAdmission:confirm.html.twig', array(
            'entity' => $transaction->getHmsInvoice(),
            'invoiceTransaction' => $transaction,
            'form' => $particularForm->createView(),
            'paymentForm' => $editForm->createView(),
        ));

    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AdmissionPatientParticular $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $form = $this->createForm(new InvoiceAdmittedParticularType($config), $entity, array(
            'action' => $this->generateUrl('hms_invoice_admitted_create', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    public function addParticularAction(Request $request, InvoiceTransaction $transaction)
    {

        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('HospitalBundle:AdmissionPatientParticular')->insertInvoiceItems($transaction, $invoiceItems);
        $this->getDoctrine()->getRepository('HospitalBundle:AdmissionPatientParticular')->updateInvoiceTransactionTotalPrice($transaction);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($transaction,$msg);
        return new Response(json_encode($result));
    }

    public function returnResultData(InvoiceTransaction $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('HospitalBundle:AdmissionPatientParticular')->getSalesItems($entity);
        $data = array(
            'total' => $entity->getTotal() ,
            'invoiceParticulars' => $invoiceParticulars ,
            'msg' => $msg ,
            'success' => 'success'
        );
        return $data;

    }


    public function invoiceParticularDeleteAction(InvoiceTransaction $transaction, AdmissionPatientParticular $patientParticular)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$patientParticular) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $em->remove($patientParticular);
        $em->flush();
        $this->getDoctrine()->getRepository('HospitalBundle:AdmissionPatientParticular')->updateInvoiceTransactionTotalPrice($transaction);
        $total = $transaction->getTotal();
        return new Response(json_encode(array('success' => 'success','total' => $total)));
    }



    public function invoiceTransactionDeleteAction($invoice ,InvoiceTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $invoice = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));
        if (!$invoice) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        if($transaction->getRevised() == 1 and !empty($transaction->getAdmissionPatientParticulars())){
            /* @var $transaction InvoiceTransaction */
            foreach ($transaction->getAdmissionPatientParticulars() as $patientParticular) {
                $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->reverseInvoiceParticularMasterUpdate($patientParticular);
            }
        }
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($invoice);
        $em->remove($transaction);
        $em->flush();
        exit;
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
        if(empty($transaction->getRevised())){
            foreach ($transaction->getAdmissionPatientParticulars() as $patientParticular ){
                $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->insertInvoiceParticularMasterUpdate($patientParticular);
            }
        }
        if($transaction->getPayment() > 0){
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->admissionInvoiceTransactionUpdate($transaction);
        }
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateAdmissionInvoiceTotalPrice($transaction);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($transaction->getHmsInvoice());
        $this->getDoctrine()->getRepository('HospitalBundle:Particular')->admittedPatientAccessories($transaction);
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


    public function singleTransactionInvoicePrintAction($invoice, InvoiceTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));
        $barcode = $this->getBarcode($entity->getInvoice());
        $patientId = $this->getBarcode($entity->getCustomer()->getCustomerId());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());

        $invoiceDetails = ['Pathology' => ['items' => [], 'total'=> 0, 'hasQuantity' => false ]];

        foreach ($transaction->getAdmissionPatientParticulars() as $item) {
            /** @var InvoiceParticular $item */
            $serviceName = $item->getParticular()->getService()->getName();
            $hasQuantity = $item->getParticular()->getService()->getHasQuantity();

            if(!isset($invoiceDetails[$serviceName])) {
                $invoiceDetails[$serviceName]['items'] = [];
                $invoiceDetails[$serviceName]['total'] = 0;
                $invoiceDetails[$serviceName]['hasQuantity'] = ($hasQuantity == 1);
            }
            $invoiceDetails[$serviceName]['items'][] = $item;
            $invoiceDetails[$serviceName]['total'] += $item->getSubTotal();
        }

        if(count($invoiceDetails['Pathology']['items']) == 0) {
            unset($invoiceDetails['Pathology']);
        }
        $inWordTransaction = $this->get('settong.toolManageRepo')->intToWords($transaction->getPayment());
        return $this->render('HospitalBundle:Print:payment.html.twig', array(
            'entity'                => $entity,
            'invoiceDetails'        => $invoiceDetails,
            'invoiceBarcode'        => $barcode,
            'patientBarcode'        => $patientId,
            'inWords'               => $inWords,
            'transaction'           => $transaction,
            'inWordTransaction'     => $inWordTransaction,
        ));
    }

    public function transactionInvoicePrintAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));

        $barcode = $this->getBarcode($entity->getInvoice());
        $patientId = $this->getBarcode($entity->getCustomer()->getCustomerId());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());
        return $this->render('HospitalBundle:Print:payments.html.twig', array(
            'entity'                => $entity,
            'invoiceBarcode'        => $barcode,
            'patientBarcode'        => $patientId,
            'inWords'               => $inWords,
        ));
    }

    public function invoiceParticularPrintAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));

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
        return $this->render('HospitalBundle:Print:invoiceParticular.html.twig', array(
            'entity'                => $entity,
            'invoiceDetails'        => $invoiceDetails,
            'invoiceBarcode'        => $barcode,
            'patientBarcode'        => $patientId,
            'inWords'               => $inWords,
        ));
    }




}

