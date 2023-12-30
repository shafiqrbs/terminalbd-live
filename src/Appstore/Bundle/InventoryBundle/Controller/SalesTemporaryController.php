<?php

namespace Appstore\Bundle\MedicineBundle\Controller;



use Appstore\Bundle\MedicineBundle\Service\PosItemManager;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary;
use Appstore\Bundle\MedicineBundle\Form\SalesTemporaryItemType;
use Appstore\Bundle\MedicineBundle\Form\SalesTemporaryType;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Core\UserBundle\Entity\User;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

/**
 * Invoice controller.
 *
 */
class SalesTemporaryController extends Controller
{

    public function newAction()
    {
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getMedicineConfig();
        $entity = new MedicineSales();
        $salesItemForm = $this->createMedicineSalesItemForm(new MedicineSalesItem());
        $editForm = $this->createCreateForm($entity);
        $result = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
        $html = $this->renderView('MedicineBundle:Sales:temporary.html.twig', array(
            'entity'        => $entity,
            'salesItem'     => $salesItemForm->createView(),
            'form'          => $editForm->createView(),
            'user'          => $user,
            'config'        => $config,
            'result'        => $result,
        ));
        return New Response($html);
    }

    /**
     * Creates a form to edit a MedicineSales entity.wq
     *
     * @param MedicineSales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineSales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new SalesTemporaryType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('medicine_sales_temporary_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'salesTemporaryForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createMedicineSalesItemForm(MedicineSalesItem $salesItem )
    {

        $form = $this->createForm(new SalesTemporaryItemType(), $salesItem, array(
            'action' => $this->generateUrl('medicine_sales_temporary_item_add'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'salesTemporaryItemForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        if( 0 == $data['salesSubTotal']){
	        $data = array('sales' => '',
	                      'process' => 'save',
	                      'success' => 'invalid');
	        return new Response(json_encode($data));
        }
        $entity = New MedicineSales();
        $user = $this->getUser();

        /* @var $global GlobalOption */

        $global = $user->getGlobalOption();
        $config = $global->getMedicineConfig();

        $editForm = $this->createCreateForm($entity);
        $editForm->handleRequest($request);
        $entity->setMedicineConfig($config);
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($user->getGlobalOption());
        $entity->setCustomer($customer);
        $globalOption = $this->getUser()->getGlobalOption();
        if (!empty($data['customerMobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($globalOption, $mobile, $data);
            $entity->setCustomer($customer);
        } elseif (!empty($data['mobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile));
            $entity->setCustomer($customer);
        }
        $tempTotal = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
        $purchaseTotal = floor($tempTotal['purchaseSubTotal']);
        $entity->setPurchasePrice(round($purchaseTotal));
        $entity->setSubTotal(round($data['salesSubTotal']));
        $entity->setNetTotal(round($data['salesNetTotal']));
        if($entity->getTransactionMethod()->getSlug() == 'mobile' and !empty($entity->getAccountMobileBank()) and !empty($entity->getAccountMobileBank()->getServiceCharge())){
            $serviceCharge = $entity->getAccountMobileBank()->getServiceCharge();
            $totalServiceCharge = (($entity->getNetTotal() * $serviceCharge)/100);
            $discount = ($entity -> getDiscount() + $totalServiceCharge);
            $entity->setDiscount($discount);
            $entity->setNetTotal($data['salesSubTotal'] - $discount);
            $entity->setReceived($data['salesSubTotal'] - $discount);
        }elseif($entity->getTransactionMethod()->getSlug() == 'bank' and !empty($entity->getAccountBank()) and !empty($entity->getAccountBank()->getServiceCharge())){
            $serviceCharge = $entity->getAccountBank()->getServiceCharge();
            $totalServiceCharge = (($entity->getNetTotal() * $serviceCharge)/100);
            $discount = ($entity -> getDiscount() + $totalServiceCharge);
            $entity->setDiscount($discount);
            $entity->setNetTotal($data['salesSubTotal'] - $discount);
            $entity->setReceived($data['salesSubTotal'] - $discount);
        }

        if ($entity->getNetTotal() <= $entity->getReceived()) {
            $entity->setReceived($entity->getNetTotal());
            $entity->setPaymentStatus('Paid');
            $entity->setDue(0);
        } else {
            $entity->setPaymentStatus('Due');
            $entity->setDue($entity->getNetTotal() - $entity->getReceived());
        }
        if (isset($data['process']) and ($data['process'] == 'Hold')) {
            $entity->setProcess('Hold');
        } else {
            $entity->setApprovedBy($this->getUser());
            $entity->setProcess('Done');
        }

        $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
        if($accountConfig == 1){
            $datetime = new \DateTime("yesterday 23:30:30");
            $entity->setCreated($datetime);
            $entity->setUpdated($datetime);
        }
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->temporarySalesInsert($user, $entity);
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->removeSalesTemporary($this->getUser());
        if ($entity->getProcess() == 'Done'){
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($entity);
        }
        $btn = $request->request->get('buttonType');
        if($btn == "posBtn" and $entity->getProcess() == 'Done'){
            $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->findBy(array('medicineSales' => $entity->getId()));
            if(!empty($invoiceParticulars)){
                $pos = $this->posPrint($entity,$invoiceParticulars);
                return new Response($pos);
            }
        }
        return new Response('success');

    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $user = $this->getUser();
        $discount = (float)$request->request->get('discount');
        $discountType = $request->request->get('discountType');
        $result = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
	    $subTotal = $result['subTotal'];
        if($discountType == 'flat' and $subTotal > $discount){
            $initialDiscount = round($discount);
            $initialGrandTotal =round ($subTotal  - $initialDiscount);
        }elseif($discountType == 'percentage' and $subTotal > $discount){
            $initialDiscount = round(($subTotal * $discount)/100);
            $initialGrandTotal = round($subTotal  - $initialDiscount);
        }else{
	        $initialDiscount = 0;
	        $initialGrandTotal = $subTotal;
        }
        $data = array(
            'subTotal' => round($subTotal),
            'profit' => round($initialGrandTotal - $result['purchaseSubTotal']),
            'initialGrandTotal' => round($initialGrandTotal),
            'initialDiscount' => $initialDiscount,
            'success' => 'success'
        );
        return new Response(json_encode($data));

    }

    public function returnResultData(User $user,$msg=''){

        $salesItems = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSalesItems($user);
        $total = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
	    $subTotal = floor($total['subTotal']);
	    $purchaseSubTotal = floor($total['purchaseSubTotal']);
        $data = array(
           'subTotal' => $subTotal,
           'purchaseSubTotal' => $purchaseSubTotal,
           'initialGrandTotal' => $subTotal,
           'discount' => $subTotal,
           'profit' => ($subTotal - $purchaseSubTotal),
           'salesItems' => $salesItems ,
           'msg' => $msg ,
           'success' => 'success'
       );
       return $data;

    }

    public function addParticularAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all()['salesTemporaryItem'];
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->insertInvoiceItems($user, $data);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));


    }

    public function invoiceItemUpdateAction(Request $request)
    {
        $user = $this->getUser();
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->updateInvoiceItems($user, $data);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));

    }

    public function invoiceParticularDeleteAction(MedicineSalesTemporary $particular){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find Sales Item entity.');
        }
        $em->remove($particular);
        $em->flush();
        $msg = 'Particular deleted successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));

    }

    private function posPrint(MedicineSales $entity,$invoiceParticulars = '')
    {
        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();

        $vatRegNo       = $config->getVatRegNo();
        $companyName    = $option->getName();
        $mobile         = "Mobile -".$option->getMobile();
        $address        = $config->getAddress();
        $website        = $option->getDomain();
        $customer       = '';

        /** ===================Customer Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getNetTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $due                = $entity->getDue();
        $payment            = $entity->getReceived();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getSalesBy()->getProfile()->getName();
        if($entity->getCustomer()->getName() != "Default"){
        $customer           = "Customer: {$entity->getCustomer()->getName()},Mobile: {$entity->getCustomer()->getMobile()}\n";
        }

        /** ===================Invoice Sales Item Information========================= */


        /* Date is kept the same for testing */
        $date = date('d-M-y h:i:s A');

        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        $printer -> text($address."\n");
        $printer -> text($mobile."\n");
        $printer -> feed();
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        /*if(!empty($vatRegNo)){
            $printer -> text("Vat Reg No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
        }*/
        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
      //  $vat            = new PosItemManager('Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $grandTotal     = new PosItemManager('Net Payable: ','Tk.',number_format($total));
        $payment        = new PosItemManager('Paid: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($due));

        /* Title of receipt */
        $printer -> feed();
        $printer -> setEmphasis(true);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales Memo No- {$entity->getInvoice()}\n");
        $printer -> setEmphasis(false);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        if(!empty($customer)){
            $printer -> text($customer);
        }
        $printer -> feed();
        $printer -> setEmphasis(true);
        $printer->setFont(Printer::FONT_B);
        $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
        $printer -> text("---------------------------------------------------------------\n");
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setEmphasis(false);
        //$printer -> feed();
        $i = 1;
        if(!empty($invoiceParticulars)){
            /* @var $row MedicineSalesItem */
            foreach ($invoiceParticulars as $row){
                $qnt = sprintf("%s", str_pad($row->getQuantity(),2, '0', STR_PAD_LEFT));
                if($row->getMedicineStock()->getMedicineBrand()){
                    $printer -> text(new PosItemManager($i.'. '.$row->getMedicineStock()->getMedicineBrand()->getName(),$qnt,number_format($row->getSubTotal())));
                }else{
                    $printer -> text(new PosItemManager($i.'. '.$row->getMedicineStock()->getName(),$qnt,number_format($row->getSubTotal())));
                }
                $i++;
            }
        }
        $printer -> text("---------------------------------------------------------------\n");
    //    $printer -> feed();
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setEmphasis(true);
        $printer -> text ( "\n" );
        //$printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($subTotal);
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
       /* if($vat){
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text($vat);
            $printer->setEmphasis(false);
        }*/
        $printer -> text("---------------------------------------------------------------\n");
        $printer->text($discount);
        $printer -> setEmphasis(true);
        $printer -> text ( "\n" );

        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> text($grandTotal);
        $printer -> setEmphasis(true);
      //  $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> text($payment);
        $printer -> setEmphasis(true);
      //  $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($due);
      //  $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer->text("\n");
        //$printer -> feed();
        //$printer->text($transaction);
        //$printer->selectPrintMode();
        /* Barcode Print */
        $printer -> feed();
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales By: ".$salesBy."\n");
        $printer -> text("Thanks for being here\n");
        /*if($website){
            $printer -> text("Please visit www.".$website."\n");
        }*/
        $printer -> text($date . "\n");
        $printer -> text("*Medicines once sold are not taken back*\n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return $response;
    }



}

