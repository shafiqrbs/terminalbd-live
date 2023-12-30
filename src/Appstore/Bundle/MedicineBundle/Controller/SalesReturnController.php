<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturn;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturnInvoice;
use Appstore\Bundle\MedicineBundle\Form\SalesTemporaryItemType;
use Appstore\Bundle\MedicineBundle\Form\SalesTemporaryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SalesReturn controller.
 *
 */
class SalesReturnController extends Controller
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
     * Lists all SalesReturn entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
        $data = $_REQUEST;
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesReturn')->invoiceLists($config,$data);
        $pagination = $this->paginate($entities);
        return $this->render('MedicineBundle:SalesReturn:index.html.twig', array(
            'entities' => $pagination,
        ));
    }


     /**
     * Lists all SalesReturn entities.
     *
     */
    public function salesReturnInvoiceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesReturnInvoice')->invoiceLists($config,$data);
        $pagination = $this->paginate($entities);
        return $this->render('MedicineBundle:SalesReturn:return-invoice.html.twig', array(
            'entities' => $pagination,
        ));
    }


    public function customerItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $pagination = '';
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->salesItemLists($this->getUser(),$data);
        if($entities){
            $pagination = $entities->getResult();
        }
        $customerMobile = (isset($_REQUEST['customer']) and ( $_REQUEST['customer'])) ? $_REQUEST['customer'] : '';
        $customer = "";
        $balance = "";
        $returnItems = "";
        if($customerMobile){
            $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(array('globalOption'=> $this->getUser()->getGlobalOption(),'mobile'=>$customerMobile));
            if($customer){
                $outstanding = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($this->getUser()->getGlobalOption(),$customer->getId());
                $balance = empty($outstanding) ? 0 : $outstanding;
                $returnItems = $this->getDoctrine()->getRepository(MedicineSalesReturn::class)->getCustomerExistReturnItem($customer->getId());
            }

        }
        return $this->render('MedicineBundle:SalesReturn:customer-item.html.twig', array(
            'entities' => $pagination,
            'balance' => $balance,
            'returnItems' => $returnItems,
            'customer' => $customer,
            'discountPercentLists' => '',
            'searchForm' => $data,
        ));
    }

    public function newAction(MedicineSalesItem $item){

        $balance = 0;
	    $user = $this->getUser();
	    $entity = $item->getMedicineSales();
        $result = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
        $discountPercentLists = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->discountPercentList();
        if($entity->getCustomer()){
            $outstanding = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($this->getUser()->getGlobalOption(),$entity->getCustomer());
            $balance = empty($outstanding) ? 0 : $outstanding;
        }
        return $this->render('MedicineBundle:SalesReturn:new.html.twig', array(
            'entity' => $item->getMedicineSales(),
            'user'   => $user,
            'discountPercentLists'   => $discountPercentLists,
            'balance'   => $balance,
            'result'   => $result,
        ));
    }

    public function groupItemNewAction(Request $request){

        $balance = 0;
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $data = $request->request->all();
        $items = $data['item'];
        $globalOption = $this->getUser()->getGlobalOption();
        $returnInvoice = $this->getDoctrine()->getRepository(MedicineSalesReturnInvoice::class)->insertMedicineSalesReturn($globalOption,$data);
        if($returnInvoice){
            foreach ($items as $item){
                if($data['quantity'][$item] > 0){
                    $qnt = $data['quantity'][$item];
                    /* @var $salesItem MedicineSalesItem */
                    $salesItem = $this->getDoctrine()->getRepository(MedicineSalesItem::class)->find($item);
                    $entity = new MedicineSalesReturn();
                    $entity->setMedicineConfig($globalOption->getMedicineConfig());
                    $entity->setMedicineSalesReturnInvoice($returnInvoice);
                    $entity->setQuantity($qnt);
                    $entity->setMedicineStock($salesItem->getMedicineStock());
                    $entity->setMedicineSalesItem($salesItem);
                    if($salesItem->getMedicinePurchaseItem()){
                        $entity->setMedicinePurchaseItem($salesItem->getMedicinePurchaseItem());
                    }
                    $price = empty($data['price'][$item]) ? $salesItem->getSalesPrice() : $data['price'][$item];
                    $entity->setSalesPrice($price);
                    $entity->setSubTotal($entity->getSalesPrice() * $entity->getQuantity());
                    $em->persist($entity);
                    $em->flush();
                    if($salesItem->getMedicinePurchaseItem()) {
                        $this->getDoctrine()->getRepository( 'MedicineBundle:MedicinePurchaseItem' )->updateRemovePurchaseItemQuantity( $salesItem->getMedicinePurchaseItem(), 'sales-return' );
                    }
                    $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($salesItem->getMedicineStock(), 'sales-return');
                }
            }
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been return successfully"
            );
        }
        return $this->redirect($this->generateUrl('medicine_sales_customer_return_invoice'));
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



	/**
     * Creates a new SalesReturn entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $data = $request->request->all();
        foreach ($data['quantity'] as $key => $qnt) {
        	if($qnt > 0 ){
        	    $adjustment = isset($data['isAdjustment'][$data['salesItem'][$key]]) ? 1 : 0;
                $entity = new MedicineSalesReturn();
		        $salesItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->find($data['salesItem'][$key]);
		        $entity->setMedicineConfig($config);
		        $entity->setQuantity($qnt);
		        $entity->setAdjustment($adjustment);
		        $entity->setMedicineStock($salesItem->getMedicineStock());
		        if($salesItem->getMedicinePurchaseItem()){
			        $entity->setMedicinePurchaseItem($salesItem->getMedicinePurchaseItem());
		        }
		        $entity->setMedicineSalesItem($salesItem);
		        $price = empty($data['price'][$key]) ? $salesItem->getSalesPrice() : $data['price'][$key];
		        $entity->setSalesPrice($price);
		        $entity->setSubTotal($entity->getSalesPrice() * $entity->getQuantity());
		        $em->persist($entity);
		        $em->flush();
		        if(!empty($salesItem->getMedicinePurchaseItem())) {
			        $this->getDoctrine()->getRepository( 'MedicineBundle:MedicinePurchaseItem' )->updateRemovePurchaseItemQuantity( $salesItem->getMedicinePurchaseItem(), 'sales-return' );
		        }
		        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($salesItem->getMedicineStock(), 'sales-return');

	        }
        }
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been inserted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_sales_return'));

    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineSalesReturn')->find($id);
	    $item = $entity->getMedicinePurchaseItem();
        $stock = $entity->getMedicineStock();
	    $this->get('session')->set('item', $item);
	    $this->get('session')->set('stock', $stock);
	    $em->remove($entity);
        $em->flush();
	    $item = $this->get('session')->get('item');
	    $stock = $this->get('session')->get('stock');
	    if(!empty($item)) {
		    $this->getDoctrine()->getRepository( 'MedicineBundle:MedicinePurchaseItem' )->updateRemovePurchaseItemQuantity( $item, 'sales-return' );
	    }
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'sales-return');
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_sales_return'));
    }

    public function approveAction(MedicineSalesReturn $entity)
    {

    	$em = $this->getDoctrine()->getManager();
        if (!empty($entity) and $entity->getProcess() !='approved') {
            $em = $this->getDoctrine()->getManager();
            if($entity->isAdjustment() == 1){
                $em->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountSalesReturn($entity);
            }else{
                $journal = $em->getRepository('AccountingBundle:AccountJournal')->insertMedicineAccountSalesReturn($entity);
                $entity->setJournal($journal);
            }
            $entity->setProcess('approved');
            $em->flush();
	        return new Response('success');
        } else {
            return new Response('failed');
        }

    }

    public function showInvoiceAction($id)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $em = $this->getDoctrine()->getManager();
        $return = $em->getRepository(MedicineSalesReturnInvoice::class)->findOneBy(array('medicineConfig'=>$config,'id'=>$id));
        return $this->render('MedicineBundle:SalesReturn:show.html.twig', array(
            'entity' => $return,
        ));
    }

    public function printInvoiceAction($id)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(MedicineSalesReturnInvoice::class)->findOneBy(array('medicineConfig'=>$config,'id'=>$id));
        $outstanding = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($this->getUser()->getGlobalOption(),$entity->getCustomer());
        $balance = empty($outstanding) ? 0 : $outstanding;
        return $this->render('MedicineBundle:SalesReturn:print.html.twig', array(
            'entity' => $entity,
            'balance' => $balance,
        ));
    }

    public function deleteInvoiceAction($id)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $em = $this->getDoctrine()->getManager();
        $return = $em->getRepository(MedicineSalesReturnInvoice::class)->findOneBy(array('medicineConfig'=>$config,'id'=>$id));
        foreach ($return->getMedicineSalesReturns() as $entity){
            $item = $entity->getMedicinePurchaseItem();
            $stock = $entity->getMedicineStock();
            $this->get('session')->set('item', $item);
            $this->get('session')->set('stock', $stock);
            $em->remove($entity);
            $em->flush();
            $item = $this->get('session')->get('item');
            $stock = $this->get('session')->get('stock');
            if(!empty($item)) {
                $this->getDoctrine()->getRepository( 'MedicineBundle:MedicinePurchaseItem' )->updateRemovePurchaseItemQuantity( $item, 'sales-return' );
            }
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'sales-return');
        }
        if($return->getProcess() == 'approved'){
            if($return->getAdjustment() > 0){
                $em->getRepository('AccountingBundle:AccountSales')->accountMedicineSalesReturnDelete($return);
            }
            if($return->getPayment() > 0){
                $em->getRepository('AccountingBundle:AccountJournal')->accountMedicineSalesReturnDelete($return);
            }
        }
        $em->remove($return);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_sales_customer_return_invoice'));
    }

    public function approveInvoiceAction(MedicineSalesReturnInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($entity) and $entity->getProcess() !='approved') {
            if($entity->getAdjustment() > 0){
                $em->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountSalesReturnInvoice($entity);
            }
            if($entity->getPayment() > 0){
                $em->getRepository('AccountingBundle:AccountJournal')->insertMedicineAccountSalesReturnInvoice($entity);
            }
            $entity->setProcess('approved');
            if($entity->getMedicineSalesReturns()) {
                foreach ($entity->getMedicineSalesReturns() as $item) {
                    $item->setProcess('approved');
                }
            }
            $em->flush();
            return new Response('success');
        } else {
            return new Response('failed');
        }
    }

    public function updateTotalAmountMismatchAction(){

        $this->getDoctrine()->getRepository(MedicineSalesReturnInvoice::class)->updateTotalAmountMismatch();
    }


}
