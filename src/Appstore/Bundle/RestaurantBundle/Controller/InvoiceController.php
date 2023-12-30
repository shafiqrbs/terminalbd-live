<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantAndroidProcess;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Appstore\Bundle\RestaurantBundle\Form\InvoiceType;
use Appstore\Bundle\RestaurantBundle\Form\RestaurantParticularType;
use Appstore\Bundle\RestaurantBundle\Service\PosItemManager;
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
 * InvoiceController controller.
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
     * Lists all Particular entities.
     * @Secure(roles="ROLE_DOMAIN,ROLE_DOMAIN_RESTAURANT_MANAGER,ROLE_RESTAURANT,ROLE_DOMAIN_RESTAURANT")
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getRestaurantConfig();
        $entities = $em->getRepository('RestaurantBundle:Invoice')->invoiceLists( $user,$data);
        $pagination = $this->paginate($entities);

        return $this->render('RestaurantBundle:Invoice:index.html.twig', array(
            'config' => $config,
            'entities' => $pagination,
            'salesTransactionOverview' => '',
            'previousSalesTransactionOverview' => '',
            'assignDoctors' => '',
            'searchForm' => $data,
        ));

    }

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Invoice();
        $option = $this->getUser()->getGlobalOption();
        $config = $option->getRestaurantConfig();
        $entity->setRestaurantConfig($config);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setPaymentStatus('Pending');
        $entity->setCreatedBy($this->getUser());
        $entity->setSalesBy($this->getUser());
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($option);
        $entity->setCustomer($customer);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('restaurant_invoice_edit', array('id' => $entity->getId())));

    }

    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_DOMAIN_RESTAURANT_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_RESTAURANT")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig' => $config , 'id' => $id));

        $categories = $em->getRepository('RestaurantBundle:Category')->findBy(array('restaurantConfig' => $config , 'status' => 1));
        $tables = $em->getRepository('RestaurantBundle:Particular')->findBy(array('restaurantConfig' => $config , 'service' => 1));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $invoiceParticularForm = $this->createInvoiceParticularForm(New InvoiceParticular(),$entity);
        if ($entity->getProcess() == "Done") {
            return $this->redirect($this->generateUrl('restaurant_invoice_show', array('id' => $entity->getId())));
        }
        return $this->render('RestaurantBundle:Invoice:editPos.html.twig', array(
            'entity'        => $entity,
            'categories'    => $categories,
            'tables'        => $tables,
            'user'          => $this->getUser(),
            'form'          => $editForm->createView(),
            'itemForm'      => $invoiceParticularForm->createView(),
        ));

    }

    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(Invoice $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->invoiceParticularLists($entity);
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
        $due = !empty($entity->getDue()) ? $entity->getDue() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;

        $data = array(
            'subTotal'               => $subTotal,
            'netTotal'               => $netTotal,
            'payment'                => $payment ,
            'due'                    => $due,
            'vat'                    => $vat,
            'discount'               => $discount,
            'invoiceParticulars'     => $invoiceParticulars ,
            'msg'                    => $msg ,
            'success'                => 'success'
        );

        return $data;

    }

    public function addParticularAction(Request $request, Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $process = $request->request->get('process');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'process'=>$process);
        $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));


    }

    public function addProductAction(Request $request, Invoice $invoice, $product)
    {
        $em = $this->getDoctrine()->getManager();
        // $particularId = $request->request->get('particularId');
        $invoiceItems = array('particularId' => $product , 'quantity' => 1,'process'=>'create');
        $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
    }

    public function updateProductAction(Request $request, Invoice $invoice, $product)
    {
        $em = $this->getDoctrine()->getManager();
        $quantity = $_REQUEST['quantity'];
        $invoiceItems = array('particularId' => $product , 'quantity' => $quantity,'process'=>'update');
        $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));

    }

    public function invoiceParticularDeleteAction( $invoice, InvoiceParticular $particular){


        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $entity =  $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->find($invoice);
        $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($entity);
        $msg = 'Product deleted successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));
    }

    public function updateAction(Request $request, Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $invoiceParticularForm = $this->createInvoiceParticularForm(New InvoiceParticular(),$entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if($editForm->isValid()) {

            if (isset($data['customerMobile']) and !empty($data['customerMobile'])) {
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($option, $mobile, $data);
                $entity->setCustomer($customer);
            } elseif (isset($data['mobile']) and !empty($data['mobile'])) {
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $option, 'mobile' => $mobile));
                $entity->setCustomer($customer);
            }else{
                $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($option);
                $entity->setCustomer($customer);
            }
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            $due = $entity->getTotal() - $entity->getPayment();
            $entity->setDue($due);
            $em->flush();
            if(!empty($entity->getInvoiceParticulars()) and in_array($entity->getProcess(),array('Delivered','Done'))) {
                $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->insertAccessories($entity);
                if($entity->getRestaurantConfig()->isStockHistory() == 1 ) {
                    $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantStockHistory')->processInsertSalesItem($entity);
                }
                $em->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($entity);
            }
            return $this->redirect($this->generateUrl('restaurant_invoice'));
        }
        return $this->render('RestaurantBundle:Invoice:editPos.html.twig', array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'itemForm'      => $invoiceParticularForm->createView(),
        ));

    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discountCalculation = (float)$request->request->get('discount');
        $invoice = $request->request->get('invoice');
        $discountType = $request->request->get('discountType');
        $entity = $em->getRepository('RestaurantBundle:Invoice')->find($invoice);
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
        $returnEntity =  $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($entity);
        $result = $this->returnResultData($returnEntity,$msg);
        return new Response(json_encode($result));

    }

    public function invoiceDiscountCouponAction(Request $request)
    {

        /* @var $config RestaurantConfig */

        $em = $this->getDoctrine()->getManager();
        $invoice = $request->request->get('invoice');
        $discountCoupon = $request->request->get('discountCoupon');
        $entity = $em->getRepository('RestaurantBundle:Invoice')->find($invoice);
        $subTotal = $entity->getSubTotal();
        /* @var $config RestaurantConfig */
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        // $coupon = $this->getDoctrine()->getRepository('RestaurantBundle:Coupon')->getValidCouponCode($this->getUser()->getGlobalOption(),$discountCoupon);
        $total = 0;
        $discount = 0;
        if($config->getDiscountType() == 'flat' and !empty($discountCoupon)){
            $total = ($subTotal  - $config->getDiscountPercentage());
        }elseif($config->getDiscountType() == 'percentage' and !empty($discountCoupon)){
            $discount = ($subTotal * $config->getDiscountPercentage())/100;
            $total = ($subTotal  - $discount);
        }
        if( $subTotal > $discount and $discount > 0 ){
            $entity->setDiscountCoupon($discountCoupon);
            $entity->setDiscount($discount);
            $entity->setTotal($total);
            $entity->setDue($entity->getTotal() - $entity->getPayment());
            $em->flush();
            $msg = 'Discount added successfully';
        }else{
            $msg = 'Discount is not use properly';
        }
        $returnEntity =  $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($entity);
        $result = $this->returnResultData($returnEntity,$msg);
        return new Response(json_encode($result));

    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sales = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $sales = $em->getRepository('RestaurantBundle:Invoice')->find($sales);
        $salesTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
        $salesSubTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
        $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
        return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'purchaseItem' => '', 'salesItem' => '','salesVat' => $vat, 'msg' => '' , 'success' => 'success')));

    }

    public function showAction(Invoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getRestaurantConfig()->getId();
        if ($inventory == $entity->getRestaurantConfig()->getId()) {
            return $this->render('RestaurantBundle:Invoice:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('restaurant_invoice'));
        }

    }

    private function createEditForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new InvoiceType($globalOption), $entity, array(
            'action' => $this->generateUrl('restaurant_invoice_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createInvoiceParticularForm(InvoiceParticular $entity , Invoice $invoice)
    {
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $particular = $this->getDoctrine()->getRepository('RestaurantBundle:Particular');
        $form = $this->createForm(new RestaurantParticularType($config,$particular), $entity, array(
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'particularForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $Invoice = $em->createQuery("DELETE RestaurantBundle:Invoice e WHERE e.id = {$id}");
        $Invoice->execute();
        return new Response(json_encode(array('success' => 'success')));

    }

    public function deleteEmptyInvoiceAction()
    {
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantConfig')->invoiceDelete($config);
        return $this->redirect($this->generateUrl('restaurant_invoice'));
    }

    public function checkTokenBookingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tokenNo = $request->request->get('tokenNo');
        $invoice = $request->request->get('invoice');
        $status = $em->getRepository('RestaurantBundle:Invoice')->checkTokenBooking($invoice,$tokenNo);
        echo $status;

    }

    public function reverseAction($invoice){

        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig' => $config, 'invoice' => $invoice));
        $em->getRepository('RestaurantBundle:Invoice')->salesTransactionReverse($entity);
        $em->getRepository('RestaurantBundle:InvoiceParticular')->reverseInvoiceParticularUpdate($entity);
        $em = $this->getDoctrine()->getManager();
        $entity->setRevised(true);
        $entity->setProcess('Revised');
        $entity->setRevised(true);
        $entity->setPaymentStatus('Due');
        $entity->setDiscount(null);
        $entity->setDue($entity->getTotal() - $entity->getPayment());
        $entity->setPaymentInWord(null);
        $em->flush();
        $template = $this->get('twig')->render('RestaurantBundle:Invoice:reverse-data.html.twig',array(
            'entity' => $entity,
        ));
        if($entity->getRestaurantConfig()->isStockHistory() == 1 ) {
            $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantStockHistory')->processReverseSalesItem($entity);
        }
        $em->getRepository('RestaurantBundle:Reverse')->insertInvoice($entity,$template);
        return $this->redirect($this->generateUrl('restaurant_invoice_edit',array('id'=>$entity->getId())));
    }

    public function reverseShowAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Reverse')->findOneBy(array('restaurantConfig'=>$config,'invoice' => $invoice));
        return $this->render('RestaurantBundle:Invoice:reverse.html.twig', array(
            'entity' => $entity,
        ));
    }

    public function PosPrintAction(Request $request,$invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig'=>$config,'invoice'=>$invoice));
        $response = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->posPrint($entity);
        return new Response($response);
    }



     public function PosDeliveryPrintAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig'=>$config,'id'=>$id));
        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->findBy(array('invoice' => $entity->getId()));
        $response = $this->renderView(
            'RestaurantBundle:Invoice:posPrint.html.twig', array(
                'entity'         => $entity,
                'printMode'         => 'print',
                'invoiceParticulars'         => $invoiceParticulars
            )
        );
        return new Response($response);
    }



    public function approvedOrderAction(Invoice $entity)
    {
        $globalOption = $entity->getRestaurantConfig()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $payment = !empty($data['payment']) ? $data['payment'] :0;
        if($entity->getPayment() >= $entity->getTotal()){
            $entity->setPayment($entity->getTotal());
            $entity->setPaymentStatus('Paid');
            $entity->setDue(null);
        }else{
            $entity->setPayment($payment);
            if($entity->getTotal() > $payment) {
                $entity->setDue($entity->getTotal() - $payment);
            }else{
                $entity->setDue(null);
            }
            $entity->setPaymentStatus('Due');
        }
        $entity->setApprovedBy($this->getUser());
        $entity->setProcess('Done');
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        $entity->setPaymentInWord($amountInWords);
        $em->flush();
        if ($entity->getTotal() > 0) {
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->insertAccessories($entity);
            $em->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($entity);
        }
        return $this->redirect($this->generateUrl('restaurant_invoice'));

    }

    public function PaymentPrintAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig' => $config,'id' => $id));
        $response = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->posPrint($entity);
        return new Response($response);

    }

    public function KitchenPrintAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig'=>$config,'invoice'=>$invoice));
        $response = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->posPrint($entity);
        return new Response($response);
    }

    public function saveAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig'=>$config,'invoice'=>$invoice));
        if(!empty($entity)){
            $entity->setProcess('Kitchen');
            $em->flush();
        }
        exit;

    }

    public function cashPayment(Invoice $entity){

        $em =  $em = $this->getDoctrine()->getManager();
        $entity->setPayment($entity->getTotal());
        $entity->setPaymentStatus('Paid');
        $entity->setApprovedBy($this->getUser());
        $entity->setProcess('Done');
        $entity->setDue(0);
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        $entity->setPaymentInWord($amountInWords);
        $em->flush();
        if ($entity->getTotal() > 0) {
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->insertAccessories($entity);
            $em->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($entity);
        }

    }

    public function productsAction(Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $pagination         = $em->getRepository('RestaurantBundle:Particular')->getServiceLists($invoice ,array('product','stockable'));
        return new Response($pagination);

    }

    public function todaySalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $salesOverview      = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->findWithSalesOverview($this->getUser());
        $created            = date('y-m-d');
        $sales              = $em->getRepository('RestaurantBundle:Invoice')->invoiceLists( $this->getUser() , array('created' => $created));
        $salesLists         = $this->paginate($sales);
        $template = $this->get('twig')->render('RestaurantBundle:Invoice:product.html.twig',array(
            'salesLists' => $salesLists,
        ));
        $data = array('overview'=> $salesOverview ,'products' =>$template );
        return new Response($template);

    }


    public function androidSalesAction()
    {
        $conf = $this->getUser()->getGlobalOption()->getRestaurantConfig()->getId();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantAndroidProcess')->getAndroidSalesList($conf,"sales");
        $pagination = $this->paginate($entities);
        $sales = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->findAndroidDeviceSales($pagination);
        return $this->render('RestaurantBundle:Invoice:salesAndroid.html.twig', array(
            'entities'  => $pagination,
            'sales'     => $sales,
        ));
    }

    public function androidSalesProcessAction($device)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->androidDeviceSalesProcess($device);
        exit;
    }


    public function insertGroupApiSalesImportAction(RestaurantAndroidProcess $android)
    {
        $msg = "invalid";
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();

        $removeSales = $em->createQuery("DELETE RestaurantBundle:Invoice e WHERE e.androidProcess= {$android->getId()}");
        if(!empty($removeSales)){
            $removeSales->execute();
        }
        $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->insertApiSales($config->getGlobalOption(),$android);

        /* @var $sales Invoice */

        $salses = $this->getDoctrine()->getRepository("RestaurantBundle:Invoice")->findBy(array('androidProcess' => $android));
        foreach ($salses as $sales){
            if($sales->getProcess() == "Device"){
                $sales->setProcess('Done');
                $sales->setInvoice($sales->getDeviceSalesId());
                $sales->setUpdated($sales->getCreated());
                $sales->setApprovedBy($this->getUser());
                $em->flush();
                $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->insertAccessories($sales);
                if($sales->getRestaurantConfig()->isStockHistory() == 1 ) {
                    $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantStockHistory')->processInsertSalesItem($sales);
                }
                $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($sales);
                $msg = "valid";
            }
        }
        if($msg == "valid"){
            $android->setStatus(true);
            $em->persist($android);
            $em->flush();
            $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateApiSalesPurchasePrice($android->getId());
        }
        if($msg == "valid"){
            return new Response('success');
        }else{
            return new Response('failed');
        }

    }


}

