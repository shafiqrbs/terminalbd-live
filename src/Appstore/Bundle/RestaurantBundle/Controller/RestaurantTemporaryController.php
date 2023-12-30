<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;
use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTemporary;
use Appstore\Bundle\RestaurantBundle\Form\RestaurantTemporaryParticularType;
use Appstore\Bundle\RestaurantBundle\Form\TemporaryType;
use Appstore\Bundle\RestaurantBundle\Service\PosItemManager;
use Core\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

/**
 * RestaurantTemporary controller.
 *
 */
class RestaurantTemporaryController extends Controller
{

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getRestaurantConfig();
        $entity = new Invoice();
        $form = $this->createTemporaryForm($entity);
        $itemForm = $this->createInvoiceParticularForm(New RestaurantTemporary());
        $tempTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $subTotal = !empty($tempTotal['subTotal']) ? $tempTotal['subTotal'] :0;
        $purchasePrice = !empty($tempTotal['purchasePrice']) ? $tempTotal['purchasePrice'] :0;
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$subTotal);
        $categories = $em->getRepository('RestaurantBundle:Category')->findBy(array('restaurantConfig' => $config , 'status' => 1));
        $tables = $em->getRepository('RestaurantBundle:Particular')->findBy(array('restaurantConfig' => $config , 'service' => 1));
        $initialTotal = ($subTotal + $vat);
        $html = $this->renderView('RestaurantBundle:Invoice:gridPos.html.twig', array(
            'config'     => $config,
            'temporarySubTotal'     => $subTotal,
            'initialVat'            => $vat,
            'initialTotal'          => $initialTotal,
            'initialDiscount'       => 0,
            'user'                  => $user,
            'categories'            => $categories,
            'tables'                => $tables,
            'entity'                => $entity,
            'form'                  => $form->createView(),
            'itemForm'              => $itemForm->createView(),
        ));
        return New Response($html);
    }

    private function createTemporaryForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new TemporaryType($globalOption), $entity, array(
            'action' => $this->generateUrl('restaurant_temporary_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createInvoiceParticularForm(RestaurantTemporary $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $form = $this->createForm(new RestaurantTemporaryParticularType($config), $entity, array(
            'action' => $this->generateUrl('restaurant_temporary_particular_add'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'particularForm',
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
        $config = $option->getRestaurantConfig();
        $tempTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $subTotal = !empty($tempTotal['subTotal']) ? $tempTotal['subTotal'] :0;
        $purchasePrice = !empty($tempTotal['purchasePrice']) ? $tempTotal['purchasePrice'] :0;
        $masterData = $request->request->all();
        $data = $masterData['restaurant_invoice'];
        $btn = $request->request->get('buttonType');
        $tableNos = $request->request->get('tableNos');
        $form = $this->createTemporaryForm($entity);
        $form->handleRequest($request);
        $entity->setRestaurantConfig($config);
        if($tableNos){
            $entity->setTableNos($tableNos);
        }
        if(empty($entity->getSalesBy())){
            $entity->setSalesBy($this->getUser());
        }
        if (isset($masterData['customerMobile']) and !empty($masterData['customerMobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($masterData['customerMobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($option, $mobile, $masterData);
            $entity->setCustomer($customer);
        } elseif (isset($masterData['mobile']) and !empty($masterData['mobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($masterData['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $option, 'mobile' => $mobile));
            $entity->setCustomer($customer);
        }else{
            $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($option);
            $entity->setCustomer($customer);
        }
        $entity->setPaymentStatus('Pending');
        $entity->setSubTotal($subTotal);
        $entity->setPurchasePrice($purchasePrice);
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$subTotal);
        $entity->setVat($vat);
        $total = round(($subTotal - $entity->getDiscount()) + $entity->getVat());
        $entity->setTotal($total);
        if ($entity->getTotal() > 0) {
            $entity->setProcess('Done');
        }
        $entity->setCreatedBy($this->getUser());
        $deliveryDateTime = $request->request->get('deliveryDateTime');
        $datetime = empty($deliveryDateTime) ? '' : $deliveryDateTime ;
        $entity->setDeliveryDateTime($datetime);
        if(($entity->getTotal() > 0 and $entity->getPayment() >= $entity->getTotal() and $entity->isHold() != 1)){
            $entity->setPayment($entity->getTotal());
            $entity->setPaymentStatus("Paid");
            $entity->setDue(0);
            if($data['payment'] > $entity->getTotal()){
                $amount = $data['payment'] - $entity->getTotal();
                $entity->setReturnAmount($amount);
            }

        }else if(($entity->getTotal() > 0 and !empty($entity->getPayment()) and $entity->isHold() != 1)){
            $payment = floatval($data['payment']);
            $entity->setPayment($payment);
            $entity->setPaymentStatus("Due");
            $amount = $entity->getTotal() -  $entity->getPayment();
            if($amount > 0){
                $entity->setDue($amount);
            }else{
                $entity->setReturnAmount(abs($amount));
            }
        }elseif($entity->getRestaurantConfig()->isAutoPayment() == 1 and empty($entity->getPayment()) and $entity->isHold() != 1){
            $entity->setPayment($entity->getTotal());
            $entity->setPaymentStatus("Paid");
            $entity->setDue(0);
        }
        if($entity->isHold() == 1){
            $entity->setProcess('Hold');
        }
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords(round($entity->getTotal()));
        $entity->setPaymentInWord($amountInWords);
        if($entity->getSubTotal()){
            $em->persist($entity);
            $em->flush();
        }
        $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->initialInvoiceItems($user,$entity);
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->removeInitialParticular($this->getUser());
        if($entity->isHold() != 1){
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->insertAccessories($entity);
            $em->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($entity);
        }
        if($entity->getRestaurantConfig()->isStockHistory() == 1 ) {
            $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantStockHistory')->processInsertSalesItem($entity);
        }
        if($btn != 'saveBtn' and $entity->getRestaurantConfig()->isPosPrint() == 1 and $entity->isHold() != 1 ){
            $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->findBy(array('invoice' => $entity->getId()));
            $pos = $this->posPrint($entity,$invoiceParticulars);
            return new Response($pos);
        }elseif($btn != 'saveBtn' and $entity->getRestaurantConfig()->isDeliveryPrint() == 1 and $entity->isHold() != 1 ) {
            $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->findBy(array('invoice' => $entity->getId()));
            $htmlProcess = $this->renderView(
                'RestaurantBundle:Invoice:posPrint.html.twig', array(
                    'entity'         => $entity,
                    'printMode'         => 'print',
                    'invoiceParticulars'         => $invoiceParticulars
                )
            );
            return new Response($htmlProcess);
        }
        return new Response("success");

    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $user = $this->getUser();
        $discount = (float)$request->request->get('discount');
        $discountType = $request->request->get('discountType');
        $tempTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $subTotal = !empty($tempTotal['subTotal']) ? $tempTotal['subTotal'] :0;
        $purchasePrice = !empty($tempTotal['purchasePrice']) ? $tempTotal['purchasePrice'] :0;
        if($discountType == 'flat'){
            $initialGrandTotal = ($subTotal  - $discount);
        }else{
            $discount = ($subTotal * $discount)/100;
            $initialGrandTotal = ($subTotal  - $discount);
        }
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$subTotal);
        $data = array(
            'subTotal' => $subTotal,
            'initialGrandTotal' => round($initialGrandTotal + $vat),
            'initialDiscount' => $discount,
            'initialVat' => $vat,
            'success' => 'success'
        );
        return new Response(json_encode($data));
    }

    public function invoiceDiscountCouponAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $config RestaurantConfig */
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $discount = $request->request->get('discount');
        $tempTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $subTotal = !empty($tempTotal['subTotal']) ? $tempTotal['subTotal'] :0;
        $purchasePrice = !empty($tempTotal['purchasePrice']) ? $tempTotal['purchasePrice'] :0;
        if($config->getDiscountType() == 'flat' and !empty($discount)){
            $initialGrandTotal = ($subTotal  - $config->getDiscountPercentage());
        }elseif($config->getDiscountType() == 'percentage' and !empty($discount)){
            $discount = ($subTotal * $config->getDiscountPercentage())/100;
            $initialGrandTotal = ($subTotal  - $discount);
        }
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$subTotal);
        $data = array(
            'subTotal' => $subTotal,
            'initialGrandTotal' => round($initialGrandTotal + $vat),
            'initialDiscount' => $discount,
            'initialVat' => $vat,
            'success' => 'success'
        );
        return new Response(json_encode($data));

    }

    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(User $user,$msg=''){

        $config = $user->getGlobalOption()->getRestaurantConfig();
        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSalesGridItems($user);
        $tempTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $subTotal = !empty($tempTotal['subTotal']) ? $tempTotal['subTotal'] :0;
        $purchasePrice = !empty($tempTotal['purchasePrice']) ? $tempTotal['purchasePrice'] :0;
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$subTotal);
        $data = array(
           'subTotal'           => $subTotal,
           'initialGrandTotal'  => round($subTotal + $vat),
           'invoiceParticulars' => $invoiceParticulars ,
           'initialVat'         => $vat ,
           'msg'                => $msg ,
           'success'            => 'success'
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
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->insertInvoiceItems($user, $invoiceItems);
        $result = $this->returnResultData($user);
        return new Response(json_encode($result));

    }


    public function addProductAction($product)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        // $particularId = $request->request->get('particularId');
        $entity = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->find($product);
        $invoiceItems = array('particularId' => $product , 'quantity' => 1,'price' => $entity->getPrice(),'process'=>'create');
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->insertInvoiceItems($user, $invoiceItems);
        $result = $this->returnResultData($user);
        return new Response(json_encode($result));
    }

    public function updateProductAction(Request $request , $product)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $quantity = $_REQUEST['quantity'];
        $entity = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->find($product);
        $invoiceItems = array('particularId' => $product , 'quantity' => $quantity,'price' => $entity->getPrice(),'process' => 'update');
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->insertInvoiceItems($user, $invoiceItems);
        $result = $this->returnResultData($user);
        return new Response(json_encode($result));
    }


    public function invoiceParticularDeleteAction(RestaurantTemporary $particular){


        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $result = $this->returnResultData($user);
        return new Response(json_encode($result));

    }

    private function posPrint(Invoice $entity,$invoiceParticulars)
    {
        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $config = $entity->getRestaurantConfig();

        $address        = $config->getAddress();
        $vatRegNo       = $config->getVatRegNo();
        $companyName    = $option->getName();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $dueBdt             = $entity->getDue();
        $payment            = $entity->getPayment();
        $returnBdt          = $entity->getReturnAmount();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getSalesBy();

        $slipNo ='';
        $tableNo ='';
        if($entity->getSlipNo()){
            $slipNo = "{$entity->getSlipNo()} / ";
        }
        if($entity->getTableNos()){
            $tableNo = implode(",",$entity->getTableNos());
        }
        $table = "Table no. {$slipNo}{$tableNo}";

        $transaction    = new PosItemManager('Pay Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('SubTotal: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $grandTotal     = new PosItemManager('Payable: ','Tk.',number_format($total));
        $payment        = new PosItemManager('Received: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($dueBdt));
        $returnTk       = new PosItemManager('Return: ','Tk.',number_format($returnBdt));


        /** ===================Invoice Sales Item Information========================= */


        /* Date is kept the same for testing */
        $date = date('d-m-Y h:i:s A');

        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        $printer -> setFont(Printer::FONT_B);
        $printer -> text($address."\n");
        $printer -> feed();
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        if(!empty($vatRegNo)){
            $printer -> text("BIN No - ".$vatRegNo." Mushak - 6.3\n\n");
        }
        if($entity->getRestaurantConfig()->isPrintToken() == 1){
            $token = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->getLastCode($entity);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("Token No-{$token}\n\n");
            $printer -> selectPrintMode();
            $printer -> feed();
        }
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer->setFont(Printer::FONT_B);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setEmphasis(true);
        $printer -> text("Invoice no. {$entity->getInvoice()}                  {$table}\n");
        $printer -> setEmphasis(false);
        $printer -> text("Date: {$date}          {$transaction}\n");
        $printer -> text(new PosItemManager('Item Name', 'Qty', 'Amount'));
        $printer -> text("---------------------------------------------------------------\n");
        $i=1;
        /* @var $row InvoiceParticular */
        foreach ( $invoiceParticulars as $row){
            $productName = "{$i}. {$row->getParticular()->getName()}";
            $printer -> text(new PosItemManager($productName,$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> text("---------------------------------------------------------------\n");
        $printer -> text($subTotal);
        $printer -> setEmphasis(false);
        if($config->getVatEnable() and $config->getVatMode() == "excluding"){
            $printer->text($vat);
            $printer->setEmphasis(false);
        }
        if($discount){
            $printer->text($discount);
            $printer -> setEmphasis(false);
        }
        $printer -> text("---------------------------------------------------------------\n");
        $printer -> text($grandTotal);
        $printer -> text($payment);
        $printer -> text("---------------------------------------------------------------\n");
        if($dueBdt > 0){
            $printer -> text($due);
        }
        if($returnBdt > 0){
            $printer -> text($returnTk);
        }
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        if($config->getVatMode() == "including"){
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> text("{$config->getVatPercentage()}% VAT Including\n");
        }
        if($config->getInvoiceNote()){
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> text($config->getInvoiceNote()."\n");
        }
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Served By: ".$salesBy."\n");
        $printer -> text("Thanks for being here\n");
        if($website){
            $printer -> text("** Visit www.".$website."**\n");
        }
        $printer -> text("Powered by - www.terminalbd.com - 01815-308090 \n");

        if($config->isKitchenPrint() == 1 ){
            $printer->cut();
            $printer->setFont(Printer::FONT_A);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("KITCHEN PRINT");
            $printer -> text("\n");
            if($entity->getRestaurantConfig()->isPrintToken() == 1){
                $token = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->getLastCode($entity);
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> text("Token No-{$token}\n\n");
                $printer -> selectPrintMode();
                $printer -> feed();
            }
            $printer -> text("Invoice no. {$entity->getInvoice()}\n");
            $printer -> selectPrintMode();
            $printer -> setEmphasis(true);
            $printer -> text("{$table}\n");
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer->setFont(Printer::FONT_B);
            $printer -> setEmphasis(true);
            $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
            $printer -> text("------------------------------------------------------------\n");
            $i=1;
            /* @var $row InvoiceParticular */
            foreach ( $invoiceParticulars as $row){
                $productName = "{$i}. {$row->getParticular()->getName()}";
                $printer -> text(new PosItemManager($productName,$row->getQuantity(),number_format($row->getSubTotal())));
                $i++;
            }
            $printer -> text("------------------------------------------------------------\n");
            $printer -> setEmphasis(true);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("Date: {$date}\n");
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> text("{$entity->getRemark()}\n");
            $printer -> setEmphasis(true);
        }

        if($config->isDeliveryPrint() == 1 ){
            $printer->cut();
            $printer->setFont(Printer::FONT_A);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("DELIVERY PRINT");
            $printer -> text("\n");
            if($entity->getRestaurantConfig()->isPrintToken() == 1){
                $token = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->getLastCode($entity);
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> text("Token No-{$token}\n\n");
                $printer -> selectPrintMode();
                $printer -> feed();
            }
            $printer -> text("Invoice no. {$entity->getInvoice()}\n");
            $printer -> selectPrintMode();
            $printer -> setEmphasis(true);
            $printer -> text("{$table}\n");
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer->setFont(Printer::FONT_B);
            $printer -> setEmphasis(true);
            $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
            $printer -> text("------------------------------------------------------------\n");
            $i=1;
            /* @var $row InvoiceParticular */
            foreach ( $invoiceParticulars as $row){
                $productName = "{$i}. {$row->getParticular()->getName()}";
                $printer -> text(new PosItemManager($productName,$row->getQuantity(),number_format($row->getSubTotal())));
                $i++;
            }
            $printer -> text("------------------------------------------------------------\n");
        }
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return $response;
    }

}

