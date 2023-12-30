<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\InventoryBundle\Form\SalesItemType;
use Appstore\Bundle\InventoryBundle\Form\SalesOnlineType;
use Appstore\Bundle\InventoryBundle\Service\PosItemManager;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Hackzilla\BarcodeBundle\Utility\Barcode;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Mike42\Escpos\Printer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sales controller.
 *
 */
class SalesOnlineController extends Controller
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
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $data = $_REQUEST;
        $entities = $em->getRepository('InventoryBundle:Sales')->salesLists( $this->getUser() , $mode='general-sales', $data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('InventoryBundle:SalesOnline:index.html.twig', array(
            'entities' => $pagination,
            'config' => $inventoryConfig,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));
    }


    public function customerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Customer')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $config = $globalOption->getInventoryConfig();
        return $this->render('InventoryBundle:SalesOnline:index.html.twig', array(
            'config' => $config,
            'entities' => $pagination,
            'inventory' => $globalOption->getInventoryConfig(),
            'searchForm' => $data,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new Sales();
        $globalOption = $this->getUser()->getGlobalOption();
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($globalOption);
        $entity->setCustomer($customer);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setSalesMode('general-sales');
        $entity->setPaymentStatus('Pending');
        $entity->setInventoryConfig($globalOption->getInventoryConfig());
        $entity->setSalesBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_salesonline_edit', array('code' => $entity->getInvoice())));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function editAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $em->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'invoice' => $code));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        $todaySales = $em->getRepository('InventoryBundle:Sales')->todaySales($this->getUser(),$mode = 'general-sales');
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($this->getUser(),$mode = 'general-sales');
        if(!in_array($entity->getProcess(),array('In-progress','Created'))) {
            return $this->redirect($this->generateUrl('inventory_salesonline_show', array('id' => $entity->getId())));
        }
        $editForm = $this->createEditForm($entity);
        if($inventory->getSalesMode() == 'stock'){
            $theme = 'stock-item';
            $createItemForm = $this->createItemForm(New SalesItem(),$entity);
            return $this->render('InventoryBundle:SalesOnline:'.$theme.'.html.twig', array(
                'entity' => $entity,
                'todaySales' => $todaySales,
                'todaySalesOverview' => $todaySalesOverview,
                'itemForm' => $createItemForm->createView(),
                'form' => $editForm->createView(),
            ));
        }else{
            $editForm = $this->createEditForm($entity);
            $theme = 'purchase-item';
            return $this->render('InventoryBundle:SalesOnline:'.$theme.'.html.twig', array(
                'entity' => $entity,
                'todaySales' => $todaySales,
                'todaySalesOverview' => $todaySalesOverview,
                'form' => $editForm->createView(),
            ));
        }
    }

    private function createItemForm(SalesItem $item , Sales $entity)
    {
        $form = $this->createForm(new SalesItemType($entity->getInventoryConfig()), $item, array(
            'action' => $this->generateUrl('inventory_salesonline_item_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'id' => 'stockItem',
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Creates a form to edit a Sales entity.wq
     *
     * @param Sales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Sales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new SalesOnlineType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('inventory_salesonline_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'id' => 'salesForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:SalesItem')->salesItems($inventory, $data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Sales:salesItem.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function updateAction(Request $request, Sales $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {

            $globalOption = $this->getUser()->getGlobalOption();
            if (!empty($data['customerMobile'])) {
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($globalOption,$mobile,$data);
                $entity->setCustomer($customer);
            } elseif(!empty($data['mobile'])) {
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile ));
                $entity->setCustomer($customer);
            }
            $entity->setDue($entity->getTotal() - $entity->getPayment());
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());
            $entity->setPaymentInWord($amountInWords);
            if ($entity->getTotal() <= $entity->getPayment()) {
                $entity->setPayment($entity->getTotal());
                $entity->setDue(0);
                $entity->setPaymentStatus('Paid');
            }else{
                $entity->setPaymentStatus('Due');
            }
            $entity->setApprovedBy($this->getUser());
            if($entity->getProcess() =="Done"){
                $datetime = new \DateTime("now");
                $entity->setCreated($datetime);
            }
            if(empty($entity->getPayment()) AND $entity->getProcess() =="Done"){
                $entity->setTransactionMethod(NULL);
            }
            $purchaseAmount = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->getItemPurchasePrice($entity);
            $entity->setPurchasePrice($purchaseAmount);
            $profit = ( $entity->getTotal()-($entity->getVat() + $purchaseAmount));
            $entity->setProfit($profit);
            $em->flush();
            if (in_array($entity->getProcess(),array('Delivered','Done'))){
                $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
                $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
                $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
            }
            return $this->redirect($this->generateUrl('inventory_salesonline_show', array('id' => $entity->getId())));
        }


    }

    /**
     * Finds and displays a Sales entity.
     *
     */
    public function showAction(Sales $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();

        if ($inventory->getId() == $entity->getInventoryConfig()->getId()) {
            if($inventory->getSalesMode() == 'stock') {
                return $this->render('InventoryBundle:SalesOnline:stock-show.html.twig', array(
                    'entity' => $entity,
                    'inventoryConfig' => $inventory,
                ));
            }else{
                $theme = 'purchase-item';
                return $this->render('InventoryBundle:SalesOnline:purchase-item-show.html.twig', array(
                    'entity' => $entity,
                    'inventoryConfig' => $inventory,
                ));
            }
        } else {
            return $this->redirect($this->generateUrl('inventory_salesonline'));
        }

    }

    /**
     * Finds and displays a Sales entity.
     *
     */
    public function showPreviewAction(Sales $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();

        if(!empty($this->getUser()->getProfile()->getBranches())){
            $itemBranchStock = $this->getDoctrine()->getRepository('InventoryBundle:Delivery')->returnBranchSalesItem($this->getUser(),$entity);
        }else{
            $data = array('stockReceiveItem' => '' , 'stockSalesItem' => '' , 'stockSalesReturnItem' => '', 'stockReturnItem' => ''  );
            $itemBranchStock = $data ;
        }

        if ($inventory == $entity->getInventoryConfig()->getId()) {
            return $this->render('InventoryBundle:SalesOnline:show-preview.html.twig', array(
                'entity'                => $entity,
                'itemBranchStocks'      => $itemBranchStock,
            ));
        }
    }

    public function getBarcode($invoice)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($invoice);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(34);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,' . $code . '" />';
        return $data;
    }

    public function invoicePrintAction($invoice)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory,'invoice'=>$invoice));

        $barcode = $this->getBarcode($entity->getInvoice());
        $totalAmount = ( $entity->getTotal() + $entity->getDeliveryCharge());
        $inWard = $this->get('settong.toolManageRepo')->intToWords($totalAmount);
        if($inventory->isCustomPrint() == 1){
            $print = $this->getUser()->getGlobalOption()->getSubDomain();
        }else{
            $print = 'invoice';
        }

        return $this->render('InventoryBundle:SalesPrint:'.$print.'.html.twig', array(
            'entity'      => $entity,
            'inventory'   => $inventory,
            'barcode'     => $barcode,
            'inWard'      => $inWard,
        ));
    }

    public function onlinePosPrintAction(Request $request)
    {

        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $data = $request->request->all();
        $salesId = $data['salesId'];
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'id' => $salesId));
        $this->updateOnlineSalesByPosPrint($entity,$data);
        $option = $entity->getInventoryConfig()->getGlobalOption();



        /** ===================Company Information=================================== */
        if(!empty($entity->getBranches())){

            /* @var Branches $branch **/

            $branch = $entity->getBranches();
            $branchName     = $branch->getName();
            $mobile         = $branch->getMobile();
            $address1       = $branch->getAddress();
            $thana          = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getName():'';
            $district       = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }else{

            $address1       = $option->getContactPage()->getAddress1();
            $mobile         = $option->getMobile();
            $thana          = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getName():'';
            $district       = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }

        $vatRegNo       = $inventory->getVatRegNo();
        $companyName    = $option->getName();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        /* @var Customer $customer **/
        $customer = $entity->getCustomer();

        if( $entity->getSalesMode() == 'online' and !empty($customer) ){

            $name = 'Name: '. $customer->getName();
            $customerMobile = 'Mobile no: '. $customer->getMobile();
            $customerAddress = 'Address: '. $customer->getAddress();
            $thana          = !empty($customer->getLocation()) ? $customer->getLocation()->getName():'';
            $district       = !empty($customer->getLocation()) ? ' ,'. $customer->getLocation()->getParent()->getName():'';
            $customerLocation = $thana.$district;

        }

        /** ===================Transaction  Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $deliveryCharge     = $entity->getDeliveryCharge();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getCreatedBy();

        /* Information for the receipt */

        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Add Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $deliveryCharge = new PosItemManager('Delivery Charge: ','Tk.',number_format($deliveryCharge));

        if( $entity->getSalesMode() == 'online' ) {
            $grandTotal = new PosItemManager('Net Payable: ', 'Tk.', number_format($total + $entity->getDeliveryCharge()));
        }else{
            $grandTotal = new PosItemManager('Net Payable: ', 'Tk.', number_format($total));
        }

        /* Date is kept the same for testing */
        $date = date('l jS \of F Y h:i:s A');

        /* Customer Information */

        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n\n");
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> selectPrintMode();
        if(!empty($entity->getBranches())) {
            $printer->text($branchName . "\n");
        }else{
            $printer -> text($address."\n");
        }
        $printer->text($mobile . "\n");
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);

        /* Title of receipt */
        if(!empty($vatRegNo)){
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setEmphasis(true);
            $printer -> selectPrintMode();
            $printer->text ( "\n" );
            $printer -> text("Vat Reg No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
            $printer->text ( "\n" );
        }

        if( $entity->getSalesMode() == 'online' and !empty($customer) ){

            /* Customer Information */
            $billTo       = new PosItemManager('Bill To');

            $printer    ->setUnderline(Printer::UNDERLINE_NONE);
            $printer    ->setJustification(Printer::JUSTIFY_LEFT);
            $printer    ->setEmphasis(true);
            $printer    ->setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer    ->text($billTo);
            $printer    ->text("\n");
            $printer    ->setEmphasis(false);
            $printer    ->selectPrintMode();
            $printer    ->setJustification(Printer::JUSTIFY_LEFT);
            $printer    ->text($name . "\n");
            $printer    ->text($customerMobile . "\n");
            $printer    ->text($customerAddress . "\n");
            $printer    ->text($customerLocation . "\n");
            $printer    ->text ( "\n" );
            $printer    ->setEmphasis(false);

        }

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setEmphasis(true);
        $printer -> text("SALES INVOICE\n\n");
        $printer -> setEmphasis(false);

        $printer -> selectPrintMode();
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text(new PosItemManager('Product', 'Qnt', 'Amount'));
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);;
        $printer -> setEmphasis(false);
        $printer -> feed();
        $i=1;
        foreach ( $entity->getSalesItems() as $row){

            $printer -> setUnderline(Printer::UNDERLINE_NONE);
            $printer -> text( new PosItemManager($i.'. '.$row->getItem()->getName(),"",""));
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer -> text(new PosItemManager($row->getPurchaseItem()->getBarcode(),$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setEmphasis(true);
        $printer -> text ( "\n" );
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($subTotal);
        $printer -> setEmphasis(false);
        if($vat){
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text($vat);
            $printer->setEmphasis(false);
        }
        if($discount){
            $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer->text($discount);
            $printer -> setEmphasis(false);
            $printer -> text ( "\n" );
        }

        if($entity->getSalesMode() == 'online' and !empty($customer) and !empty($deliveryCharge)){
            $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer->text($deliveryCharge);
            $printer -> setEmphasis(false);
            $printer -> text ( "\n" );
        }

        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($grandTotal);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);

        $printer->text("\n");
        $printer->setEmphasis(false);
        $printer->text($transaction);
        $printer->selectPrintMode();


        /* Barcode Print */
        $printer->selectPrintMode ( Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH );
        $printer->text ( "\n" );
        $printer->selectPrintMode ();
        $printer->setBarcodeHeight (60);
        $hri = array (Printer::BARCODE_TEXT_BELOW => "");
        $printer -> feed();
        foreach ( $hri as $position => $caption){
            $printer->selectPrintMode ();
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer->text ($caption);
            $printer->setBarcodeTextPosition ( $position );
            $printer->barcode ($invoice , Printer::BARCODE_JAN13 );
            $printer->feed ();
        }
        /* Footer */

        $printer -> feed();
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales By: ".$salesBy."\n");
        $printer -> text("Thank you for shopping\n");
        if($website){
            $printer -> text("Please visit www.".$website."\n");
        }
        $printer -> text($date . "\n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return new Response($response);

    }

    public function onlinePosPrintIndividualAction(Sales $entity)
    {

        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $inventory = $entity->getInventoryConfig();
        $option = $entity->getInventoryConfig()->getGlobalOption();

        /** ===================Company Information=================================== */
        if(!empty($entity->getBranches())){

            /* @var Branches $branch **/

            $branch = $entity->getBranches();
            $branchName     = $branch->getName();
            $mobile         = $branch->getMobile();
            $address1       = $branch->getAddress();
            $thana          = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getName():'';
            $district       = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }else{

            $address1       = $option->getContactPage()->getAddress1();
            $mobile         = $option->getMobile();
            $thana          = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getName():'';
            $district       = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }

        $vatRegNo       = $inventory->getVatRegNo();
        $companyName    = $option->getName();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        /* @var Customer $customer **/
        $customer = $entity->getCustomer();

        if( $entity->getSalesMode() == 'online' and !empty($customer) ){

            $name = 'Name: '. $customer->getName();
            $customerMobile = 'Mobile no: '. $customer->getMobile();
            $customerAddress = 'Address: '. $customer->getAddress();
            $thana          = !empty($customer->getLocation()) ? $customer->getLocation()->getName():'';
            $district       = !empty($customer->getLocation()) ? ' ,'. $customer->getLocation()->getParent()->getName():'';
            $customerLocation = $thana.$district;

        }

        /** ===================Transaction  Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $deliveryCharge     = $entity->getDeliveryCharge();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getCreatedBy();

        /* Information for the receipt */

        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Add Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $deliveryCharge = new PosItemManager('Delivery Charge: ','Tk.',number_format($deliveryCharge));

        if( $entity->getSalesMode() == 'online' ) {
            $grandTotal = new PosItemManager('Net Payable: ', 'Tk.', number_format($total + $entity->getDeliveryCharge()));
        }else{
            $grandTotal = new PosItemManager('Net Payable: ', 'Tk.', number_format($total));
        }

        /* Date is kept the same for testing */
        $date = date('l jS \of F Y h:i:s A');

        /* Customer Information */

        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n\n");
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> selectPrintMode();
        if(!empty($entity->getBranches())) {
            $printer->text($branchName . "\n");
        }else{
            $printer -> text($address."\n");
        }
        $printer->text($mobile . "\n");
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);

        /* Title of receipt */
        if(!empty($vatRegNo)){
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setEmphasis(true);
            $printer -> selectPrintMode();
            $printer->text ( "\n" );
            $printer -> text("Vat Reg No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
            $printer->text ( "\n" );
        }

        if( $entity->getSalesMode() == 'online' and !empty($customer) ){

            /* Customer Information */
            $billTo       = new PosItemManager('Bill To');

            $printer    ->setUnderline(Printer::UNDERLINE_NONE);
            $printer    ->setJustification(Printer::JUSTIFY_LEFT);
            $printer    ->setEmphasis(true);
            $printer    ->setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer    ->text($billTo);
            $printer    ->text("\n");
            $printer    ->setEmphasis(false);
            $printer    ->selectPrintMode();
            $printer    ->setJustification(Printer::JUSTIFY_LEFT);
            $printer    ->text($name . "\n");
            $printer    ->text($customerMobile . "\n");
            $printer    ->text($customerAddress . "\n");
            $printer    ->text($customerLocation . "\n");
            $printer    ->text ( "\n" );
            $printer    ->setEmphasis(false);

        }

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setEmphasis(true);
        $printer -> text("SALES INVOICE\n\n");
        $printer -> setEmphasis(false);

        $printer -> selectPrintMode();
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text(new PosItemManager('Product', 'Qnt', 'Amount'));
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);;
        $printer -> setEmphasis(false);
        $printer -> feed();
        $i=1;
        foreach ( $entity->getSalesItems() as $row){

            $printer -> setUnderline(Printer::UNDERLINE_NONE);
            $printer -> text( new PosItemManager($i.'. '.$row->getItem()->getName(),"",""));
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer -> text(new PosItemManager($row->getPurchaseItem()->getBarcode(),$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setEmphasis(true);
        $printer -> text ( "\n" );
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($subTotal);
        $printer -> setEmphasis(false);
        if($vat){
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text($vat);
            $printer->setEmphasis(false);
        }
        if($discount){
            $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer->text($discount);
            $printer -> setEmphasis(false);
            $printer -> text ( "\n" );
        }

        if($entity->getSalesMode() == 'online' and !empty($customer) and !empty($deliveryCharge)){
            $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer->text($deliveryCharge);
            $printer -> setEmphasis(false);
            $printer -> text ( "\n" );
        }

        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($grandTotal);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);

        $printer->text("\n");
        $printer->setEmphasis(false);
        $printer->text($transaction);
        $printer->selectPrintMode();


        /* Barcode Print */
        $printer->selectPrintMode ( Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH );
        $printer->text ( "\n" );
        $printer->selectPrintMode ();
        $printer->setBarcodeHeight (60);
        $hri = array (Printer::BARCODE_TEXT_BELOW => "");
        $printer -> feed();
        foreach ( $hri as $position => $caption){
            $printer->selectPrintMode ();
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer->text ($caption);
            $printer->setBarcodeTextPosition ( $position );
            $printer->barcode ($invoice , Printer::BARCODE_JAN13 );
            $printer->feed ();
        }
        /* Footer */

        $printer -> feed();
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales By: ".$salesBy."\n");
        $printer -> text("Thank you for shopping\n");
        if($website){
            $printer -> text("Please visit www.".$website."\n");
        }
        $printer -> text($date . "\n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return new Response($response);

    }

    public function updateOnlineSalesByPosPrint(Sales $entity,$data){


        $em = $this->getDoctrine()->getManager();

        $globalOption = $this->getUser()->getGlobalOption();
        if (!empty($data['sales_general']['customer']['mobile'])) {

            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['sales_general']['customer']['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomer($globalOption,$mobile,$data);
            $entity->setCustomer($customer);

        } elseif(!empty($data['mobile'])) {

            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile ));
            $entity->setCustomer($customer);

        } else {

            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'name' => 'Default'));
            if(empty($customer)){
                $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($globalOption);

            }
            $entity->setCustomer($customer);
        }

        if ($entity->getInventoryConfig()->getVatEnable() == 1 && $entity->getInventoryConfig()->getVatPercentage() > 0) {
            $vat = $em->getRepository('InventoryBundle:Sales')->getCulculationVat($entity,$data['paymentTotal']);
            $entity->setVat($vat);
        }
        $entity->setDeliveryCharge($data['deliveryCharge']);
        $entity->setDue($data['dueAmount']);
        $entity->setDiscount($data['discount']);
        $entity->setTotal($data['paymentTotal']);
        $entity->setPayment($data['paymentTotal'] - $data['dueAmount']);
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());
        $entity->setPaymentInWord($amountInWords);

        if ($data['paymentTotal'] <= $data['paymentAmount']) {
            $entity->setPayment($entity->getTotal());
            $entity->setDue(0);
            $entity->setPaymentStatus('Paid');
        } else if ($data['paymentTotal'] > $data['paymentAmount']) {
            $entity->setPaymentStatus('Due');
        }
        if (empty($data['sales_general']['salesBy'])) {
            $entity->setSalesBy($this->getUser());
        }
        if ( $data['process'] != 'in-progress') {
            $entity->setApprovedBy($this->getUser());
        }
        $em->flush();

        /*
        if (in_array('OnlineSales', $entity->getInventoryConfig()->getDeliveryProcess())) {

            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.posorder_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }*/

        if ($data['process'] != 'in-progress' ) {

            $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
            $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
            //   $em->getRepository('InventoryBundle:GoodsItem')->updateEcommerceItem($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
            return $this->redirect($this->generateUrl('inventory_salesonline_new'));
        }
    }

    public function reverseAction($invoice)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory,'invoice' => $invoice));
        if($entity){
            $em = $this->getDoctrine()->getManager();
            $em->getRepository('InventoryBundle:StockItem')->saleaItemStockReverse($entity);
            $em->getRepository('InventoryBundle:Item')->getSalesItemReverse($entity);
            $em->getRepository('AccountingBundle:AccountSales')->accountSalesReverse($entity);
            $em = $this->getDoctrine()->getManager();
            $entity->setRevised(true);
            $entity->setProcess('In-progress');
            $entity->setRevised(true);
            $entity->setTotal($entity->getSubTotal());
            $entity->setPaymentStatus('Due');
            $entity->setDiscount(null);
            $entity->setDue($entity->getSubTotal());
            $entity->setPaymentInWord(null);
            $entity->setPayment(null);
            $entity->setPaymentStatus('Pending');
            $em->flush();
            $template = $this->get('twig')->render('InventoryBundle:Reverse:salesReverse.html.twig', array(
                'entity' => $entity,
                'inventoryConfig' => $inventory,
            ));
            $em->getRepository('InventoryBundle:Reverse')->insertSales($entity, $template);
            return $this->redirect($this->generateUrl('inventory_salesonline_edit', array('code' => $entity->getInvoice())));
        }
        return $this->redirect($this->generateUrl('inventory_salesonline'));

    }

    public function invoiceGroupReverseAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = ['startDate' => '2019-07-04','endateDate' => '2019-07-04','process'=>"Done"];
        $entities = $em->getRepository('InventoryBundle:Sales')->salesLists( $this->getUser() , $mode='general-sales', $data);
        $pagination = $entities->getResult();

        /* @var $entity Sales */

        foreach ($pagination as $entity):
            $em->getRepository('InventoryBundle:StockItem')->saleaItemStockReverse($entity);
            $em->getRepository('InventoryBundle:Item')->getSalesItemReverse($entity);
            $em->getRepository('InventoryBundle:GoodsItem')->ecommerceItemReverse($entity);
            $em->getRepository('AccountingBundle:AccountSales')->accountSalesReverse($entity);
            $em = $this->getDoctrine()->getManager();
            $entity->setRevised(true);
            $entity->setProcess("Revised");
            $em->flush();
        endforeach;
        exit;

    }

    public function invoiceGroupApprovedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data=$_REQUEST;
        //  $data = ['startDate' => '2021-11-12','endateDate' => '2021-11-12'];
        $entities = $em->getRepository('InventoryBundle:Sales')->salesLists( $this->getUser() , $mode='pos', $data);
        $pagination = $entities->getResult();
        /* @var $entity Sales */
        foreach ($pagination as $entity):
            $entity->setPayment($entity->getTotal());
            $method = $this->getDoctrine()->getRepository("SettingToolBundle:TransactionMethod")->find(1);
            $entity->setTransactionMethod($method);
            $entity->setDue(0);
            $em->flush();
            $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
        endforeach;
        exit;
    }


    public function createItemAction($id,Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $pItem = isset($data['purchaseItem']) ? $data['purchaseItem']:'';
        $price = isset($data['salesPrice']) ? $data['salesPrice']:'';
        $item = $data['salesitem']['item'];
        $quantity = isset($data['quantity']) ? $data['quantity'] : 1;


        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $sales = $em->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory,'id'=>$id));

        /* @var $itemStock Item */
        $itemStock = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findOneBy(array('inventoryConfig' => $config,'id' => $item));
        $purchaseItem = '';
        if(empty($pItem)){
            $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->returnPurchaseItemDetails($inventory,trim($pItem));
        }

        if($purchaseItem){

            /* @var $purchaseItem PurchaseItem */

            $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkPurchaseItemSalesQuantity($purchaseItem);
            $itemStock = $purchaseItem->getQuantity();
            if(!empty($purchaseItem) and $itemStock > 0 and  $itemStock > $checkQuantity) {
                $data = array('quantity'=> $quantity ,'salesPrice'=> $price ,'purchasePrice' => $itemStock->getPurchasePrice());
                $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertOnlineSalesPurchaseItem($sales, $purchaseItem,$data);
                $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
                $msg = 'success';
            } else {
                $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
                $msg = 'invalid';
            }
            $data = $this->returnResultData($sales,$msg);
            return new Response(json_encode($data));

        }elseif($itemStock) {

            $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkSalesItemQuantity($itemStock);
            if($itemStock->getRemainingQnt() > 0 and  $itemStock->getRemainingQnt() > $checkQuantity) {
                $data = array('quantity'=> $quantity ,'salesPrice'=> $price ,'purchasePrice' => $itemStock->getPurchasePrice());
                $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertSalesItems($sales, $itemStock,$data);
                $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
                $msg = 'success';
            } else {
                $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
                $msg = 'invalid';
            }
            $data = $this->returnResultData($sales,$msg);
            return new Response(json_encode($data));
        }
        return new Response(json_encode(array('status'=>'in-valid')));
    }

    public function returnResultData($sales,$msg=''){

        //$salesItems = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->getSalesItems($sales);
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $config,'id' => $sales));
        /*
         * $salesItems = $this->renderView('InventoryBundle:Sales:item.html.twig', array(
            'entity' => $entity
        ));
        */
        $salesItems = $this->renderView('InventoryBundle:Sales:sales-pos-item.html.twig', array(
            'entity' => $entity
        ));
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $due = $entity->getDue();
        $vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
            'msg' => $msg,
            'salesSubTotal' => $subTotal,
            'salesTotal' => $netTotal,
            'payment' => $payment ,
            'due' => $due,
            'discount' => $discount,
            'vat' => $vat,
            'salesItems' => $salesItems ,
            'success' => 'success'
        );
        return $data;

    }



}
