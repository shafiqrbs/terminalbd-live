<?php

namespace Appstore\Bundle\MedicineBundle\Controller;



use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\MedicineStockItemSalesType;
use Appstore\Bundle\MedicineBundle\Form\SalesTemporaryItemType;
use Appstore\Bundle\MedicineBundle\Form\SalesTemporaryType;
use Appstore\Bundle\MedicineBundle\Service\PosItemManager;
use Core\UserBundle\Entity\User;
use Mike42\Escpos\Printer;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class MedicineSalesTemporaryController extends Controller
{

    public function newAction()
    {

       // return $this->redirect($this->generateUrl('medicine_homepage'));
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getMedicineConfig();
        $entity = new MedicineSales();
        $salesItemForm = $this->createMedicineSalesItemForm(new MedicineSalesItem());
        $editForm = $this->createCreateForm($entity);
        $result = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
        $stockItemForm = $this->createStockItemForm(new MedicineStock());
        $discountPercentList = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->discountPercentList();
        $brands = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getBrands($this->getUser()->getGlobalOption()->getMedicineConfig()->getId());
        return $this->render('MedicineBundle:Sales:pos.html.twig', array(
            'entity'        => $entity,
            'salesItem'     => $salesItemForm->createView(),
            'stockItemForm' => $stockItemForm->createView(),
            'form'          => $editForm->createView(),
            'discountPercentLists'          => $discountPercentList,
            'brands'          => $brands,
            'user'          => $user,
            'config'        => $config,
            'result'        => $result,
        ));
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
        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem');
        $form = $this->createForm(new SalesTemporaryItemType($globalOption,$em), $salesItem, array(
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

    private function createStockItemForm(MedicineStock $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $form = $this->createForm(new MedicineStockItemSalesType($config), $entity, array(
            'action' => $this->generateUrl('medicine_stock_item_sales_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'custom-form',
                'id' => 'medicineStock',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function stockItemCreateAction(Request $request)
    {
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getMedicineConfig();

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = new MedicineStock();
        $form = $this->createStockItemForm($entity);
        $form->handleRequest($request);
        $medicineName = trim($data['medicineStock']['name']);
        if(empty($data['medicineId'])) {
            $checkStockMedicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->checkDuplicateStockNonMedicine($config,$medicineName);
        }else{
            $medicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->find($data['medicineId']);
            $checkStockMedicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->checkDuplicateStockMedicine($config, $medicine);
        }
        if (empty($checkStockMedicine) and $medicineName){
            $entity->setMedicineConfig($config);
            if(!empty($data['medicineId'])){
                if($data['medicineCompany']){
                    $entity->setBrandName($data['medicineCompany']);
                }else{
                    $entity->setBrandName($medicine->getMedicineCompany()->getName());
                }
                $entity->setMedicineBrand($medicine);
                $name = $medicine->getName().' '.$medicine->getStrength().' '.$medicine->getMedicineForm();
                $entity->setName($name);
                $slug = str_replace(" ",'',$medicine->getName().$medicine->getStrength());
                $entity->setSlug(strtolower($slug));
                $entity->setMode('medicine');
            }else{
                $slug = str_replace(" ",'',$entity->getName());
                $entity->setSlug(strtolower($slug));
                if($data['medicineCompany']){
                    $entity->setBrandName($data['medicineCompany']);
                }
            }
            if(empty($entity->getUnit())){
                $unit = $this->getDoctrine()->getRepository('SettingToolBundle:ProductUnit')->find(4);
                $entity->setUnit($unit);
            }
            $avg = ($entity->getSalesPrice() - (($entity->getSalesPrice() * 12.5)/100));
            $entity->setPurchasePrice($avg);
            $entity->setAveragePurchasePrice($avg);
            $entity->setAverageSalesPrice($entity->getSalesPrice());
            $entity->setRemainingQuantity($entity->getOpeningQuantity() - $entity->getSalesQuantity());
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->insertDirectInvoiceItems($user, $entity,$data);
            $result = $this->returnResultData($user,'success');
            return new Response(json_encode($result));
        }elseif($checkStockMedicine){
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->insertDirectInvoiceItems($user,$checkStockMedicine,$data);
            $result = $this->returnResultData($user,'success');
            return new Response(json_encode($result));
        }else{
            return new Response(json_encode(array('success'=>'invalid')));
        }

    }

    public function createAction(Request $request)
    {
        set_time_limit(0);

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
        $invoice = $entity->getInvoice();
        $tempTotal = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
        $exist = $this->getDoctrine()->getRepository(MedicineSales::class)->findOneBy(array('medicineConfig' => $config, 'invoice' => $invoice));
        $btn = $request->request->get('buttonType');
        if ($editForm->isValid() and empty($exist) and round($data['salesSubTotal']) > 0 and round($tempTotal['subTotal']) > 0 ) {
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
            $purchaseTotal = floor($tempTotal['purchaseSubTotal']);
            $entity->setPurchasePrice(round($purchaseTotal));
            $entity->setSubTotal(round($data['salesSubTotal']));
            $entity->setNetTotal(round($data['salesNetTotal']));
            if ($entity->getTransactionMethod()->getSlug() == 'mobile' and !empty($entity->getAccountMobileBank()) and !empty($entity->getAccountMobileBank()->getServiceCharge())) {
                $serviceCharge = $entity->getAccountMobileBank()->getServiceCharge();
                $totalServiceCharge = (($entity->getNetTotal() * $serviceCharge) / 100);
                $discount = ($entity->getDiscount() + $totalServiceCharge);
                $entity->setDiscount($discount);
                $entity->setNetTotal($data['salesSubTotal'] - $discount);
                $entity->setReceived($data['salesSubTotal'] - $discount);
            } elseif ($entity->getTransactionMethod()->getSlug() == 'bank' and !empty($entity->getAccountBank()) and !empty($entity->getAccountBank()->getServiceCharge())) {
                $serviceCharge = $entity->getAccountBank()->getServiceCharge();
                $totalServiceCharge = (($entity->getNetTotal() * $serviceCharge) / 100);
                $discount = ($entity->getDiscount() + $totalServiceCharge);
                $entity->setDiscount($discount);
                $entity->setNetTotal($data['salesSubTotal'] - $discount);
                $entity->setReceived($data['salesSubTotal'] - $discount);
            }

            if ($entity->getMedicineConfig()->isAutoPayment() == 1 and empty($entity->getReceived())) {
                $entity->setReceived($entity->getNetTotal());
                $entity->setPaymentStatus('Paid');
                $entity->setDue(0);
            } elseif ($entity->getNetTotal() <= $entity->getReceived()) {
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
                $entity->setProcess('Done');
            }
            if (isset($data['printWithoutDiscount']) and ($data['printWithoutDiscount'] == 1)) {
                $entity->setPrintWithoutDiscount(1);
            }
            $entity->setApprovedBy($this->getUser());
            if (empty($entity->getSalesBy())) {
                $entity->setSalesBy($this->getUser());
            }
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if ($accountConfig == 1) {
                $datetime = new \DateTime("yesterday 23:30:30");
                $entity->setCreated($datetime);
                $entity->setUpdated($datetime);
            }
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->temporarySalesInsert($user, $entity);
          //  $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->temporarySalesInsertManual($user, $entity);
           // $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->removeSalesTemporary($this->getUser());
            if ($entity->getProcess() == 'Done') {
                $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($entity);
            }
            if ($btn == "regularBtn" and $entity->getProcess() == 'Done') {
                return new Response($entity->getId());
            }else if ($btn == "receiveBtn" and $entity->getProcess() == 'Done') {
                return new Response('success');
            }
        }elseif($exist and $btn == "regularBtn" and $exist->getProcess() == 'Done'){
            return new Response($entity->getId());
        }elseif($exist and $exist->getProcess() == 'Done'){
            return new Response('success');
        }
        return new Response('failed');

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

       // $salesItems = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSalesItems($user);
        $salesTemporary = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->findBy(array('user'=>$user),array('id'=>'ASC'));
        $total = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
	    $subTotal = round($total['subTotal']);
	    $purchaseSubTotal = round($total['purchaseSubTotal']);
        $discountPercentLists = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->discountPercentList();
        $salesItems = $this->renderView('MedicineBundle:Sales:ajaxPosItem.html.twig', array(
            'config' => $this->getUser()->getGlobalOption()->getMedicineConfig(),
            'salesTemporary'        => $salesTemporary,
            'result'                => $total,
            'discountPercentLists'  => $discountPercentLists
        ));
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
        $form = $request->request->all();
        $data = $form['salesTemporaryItem'];
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->insertInvoiceItems($user, $data);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));


    }

    public function addBarcodeAction(Request $request)
    {
        $user = $this->getUser();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $barcode =trim($_REQUEST['barcode']);
        $stock = $this->getDoctrine()->getRepository("MedicineBundle:MedicineStock")->findOneBy(array('medicineConfig'=>$config,'barcode'=>$barcode));
        if($stock and $config->isRemainingQuantity() == 1 and $stock->getRemainingQuantity() >  0){
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->insertBarcodeInvoiceItems($user, $stock);
        }
        if($stock and $config->isRemainingQuantity() != 1){
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->insertBarcodeInvoiceItems($user, $stock);
        }
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));
    }

    public function addAjaxLiveAction(Request $request,$id)
    {
        $user = $this->getUser();
        $data = $request->request->all();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $stock = $this->getDoctrine()->getRepository("MedicineBundle:MedicineStock")->findOneBy(array('medicineConfig'=>$config,'id'=>$id));
        if($stock){
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->insertAjaxLiveItem($user, $stock,$data);
        }
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));
    }

    public function liveSearchSalesAction(Request $request)
    {

        $item = !empty($_REQUEST['query']) ? trim($_REQUEST['query']): '';
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $items = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->liveSearchAutoComplete($item,$inventory);
            $discountPercentLists = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->discountPercentList();
            $return = $this->renderView('MedicineBundle:Sales:live-search-data.html.twig', array(
                'items'  => $items,
                'discountPercentLists'  => $discountPercentLists
            ));

        }else{
                $return = 'No results containing all your search terms were found.';
        }
        return new JsonResponse($return);
    }


    public function addGenericStockAction($id)
    {
        $user = $this->getUser();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $stock = $this->getDoctrine()->getRepository("MedicineBundle:MedicineStock")->findOneBy(array('medicineConfig'=>$config,'id'=>$id));
        if($stock){
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->insertBarcodeInvoiceItems($user, $stock);
        }
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));
    }

    public function addGenericStockItemAction(Request $request, MedicineStock $stock)
    {
        $data = $request->request->all();
        $user = $this->getUser();
        if($stock){
            $this->getDoctrine()->getRepository(MedicineSalesTemporary::class)->insertGenericInvoiceItems($user,$stock,$data);
        }
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
        /* @var $config MedicineConfig */
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();

        $vatRegNo       = $config->getVatRegNo();
        $companyName    = $option->getName();
       // $mobile         = "Mobile -".$option->getMobile();
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
        $printMessage       = $config->getPrintFooterText();
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
       // $printer -> text($mobile."\n");
        $printer -> feed();
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        /*if(!empty($vatRegNo)){
            $printer -> text("Vat Reg No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
        }*/
        $discountPercent = $config->isPrintDiscountPercent();
        $prevDue = $config->isPrintPreviousDue();
        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
      //  $vat            = new PosItemManager('Vat: ','Tk.',number_format($vat));
        if($discountPercent == 1 and $entity->getDiscountType() =="Percentage"){
            $percent = $entity->getDiscountCalculation();
            $discount       = new PosItemManager('Discount: ('.$percent.'%)','Tk.',number_format($discount));
        }else{
            $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        }
        $grandTotal     = new PosItemManager('Net Payable: ','Tk.',number_format($total));
        $previousDue = $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->customerSingleOutstanding($option,$entity->getCustomer());
        if($prevDue == 1 and $entity->getCustomer()->getName() != "Default"){
            $previous = ($previousDue - $entity->getDue());
            $previousBalance       = new PosItemManager('Previous Due: ','Tk.',number_format($previous));
        }
        $payment        = new PosItemManager('Paid: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($previousDue));

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
                    $printer -> text(new PosItemManager($i.'. '.$row->getMedicineStock()->getName(),$qnt,number_format($row->getSubTotal())));
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
        $printer -> text("---------------------------------------------------------------\n");
        $printer->text($discount);
        $printer -> setEmphasis(false);
        $printer -> text ( "\n" );

        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> text($grandTotal);
         $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> text($previousBalance);
        $printer -> setEmphasis(false);
      //  $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> text($payment);
        $printer -> setEmphasis(true);
        if($previousDue > 0 and $entity->getCustomer()->getName() != "Default") {
            $printer->text($due);
        }
      //  $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer->text("\n");
        $printer -> feed();
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales By: ".$salesBy."\n");
        $printer -> text($date . "\n");
        if($printMessage){
            $printer->text("{$printMessage}\n");
        }else{
            $printer->text("*Medicines once sold are not taken back*\n");
        }
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return $response;
    }

}

