<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Form\AccountHotelPurchaseType;
use Appstore\Bundle\AccountingBundle\Form\AccountSalesBusinessType;
use Appstore\Bundle\AccountingBundle\Form\AccountSalesInvoiceType;
use Appstore\Bundle\AccountingBundle\Form\AccountSalesMedicineType;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountHotelController controller.
 *
 */
class AccountHotelController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all AccountSales entities.
     *
     */
    public function salesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('AccountingBundle:AccountSales')->findWithSearch($user,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent =array(20,29));
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('AccountingBundle:AccountHotel:sales.html.twig', array(
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }

    /**
     * Lists all AccountSales entities.
     *
     */
    public function purchaseAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($globalOption,$data);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent =array(5));
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('AccountingBundle:AccountHotel:purchase.html.twig', array(
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }

    /**
     * Lists all AccountSales entities.
     *
     */
    public function customerOutstandingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:AccountHotel:customerOutstanding.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    /**
     * Lists all AccountSales entities.
     *
     */
    public function vendorOutstandingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->vendorBusinessOutstanding($globalOption,'business',$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:AccountHotel:purchaseOutstanding.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function purchaseNewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountPurchase();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:AccountHotel:purchaseNew.html.twig', array(
            'entity'    => $entity,
            'banks'     => $banks,
            'form'      => $form->createView(),
        ));
    }

    public function purchaseCreateAction(Request $request)
    {
        $entity = new AccountPurchase();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $entity->setProcessHead('business');
            $entity->setProcessType('Payment');
            if($entity->getPayment() < 0){
                $entity->setPurchaseAmount(abs($entity->getPayment()));
                $entity->setPayment(0);
                $entity->setTransactionMethod(null);
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_purchase_business'));
        }

        return $this->render('AccountingBundle:AccountHotel:purchaseNew.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Creates a form to create a AccountPurchase entity.
     *
     * @param AccountPurchase $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountPurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountHotelPurchaseType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_purchase_business_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    /**
     * Displays a form to create a new AccountSales entity.
     *
     */
    public function salesNewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountSales();
        $form   = $this->salesCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:AccountHotel:new.html.twig', array(
            'entity' => $entity,
            'banks' => $banks,
            'form'   => $form->createView(),
        ));
    }

    public function salesCreateAction(Request $request)
    {
        $entity = new AccountSales();
        $form = $this->salesCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption( $this->getUser()->getGlobalOption());
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
	        if($entity->getProcessHead() == 'Outstanding'){
		        $entity->setTotalAmount(abs($entity->getAmount()));
		        $entity->setAmount(0);
		        $entity->setTransactionMethod(null);
	        }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_sales_business'));
        }

        return $this->render('AccountingBundle:AccountHotel:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Creates a form to create a AccountSales entity.
     *
     * @param AccountSales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function salesCreateForm(AccountSales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountSalesBusinessType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_sales_business_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing AccountSales entity.
     *
     */
    public function duePaymentAction(MedicineSales $sales)
    {
        $em = $this->getDoctrine()->getManager();
        $global=$this->getUser()->getGlobalOption();
        $entity = $em->getRepository('AccountingBundle:AccountSales')->findOneBy(array('globalOption'=>$global,'businessSales'=>$sales->getId()));
        if (!$entity) {
            return $this->redirect($this->generateUrl('account_sales_business_new'));
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountSales();
        $entity->setGlobalOption( $this->getUser()->getGlobalOption());
        $entity->setMedicineSales($sales);
        $entity->setCustomer($sales->getCustomer());
        $entity->setTransactionMethod($this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->find(1));
        $entity->setProcessHead('Due');
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountHotel:invoice.html.twig', array(
            'sales'      => $sales,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new AccountSales entity.
     *
     */
    public function duePaymentUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountSales')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountSales entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('account_sales_business'));
        }
        return $this->render('AccountingBundle:AccountHotel:invoice.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a AccountSales entity.
     *
     * @param AccountSales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AccountSales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountSalesInvoiceType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_sales_business_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function salesApproveAction(AccountSales $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
	        $em->getRepository('AccountingBundle:AccountSales')->updateCustomerBalance($entity);
	        if($entity->getAmount() > 0 ){
		        $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertSalesCash($entity);
		        $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountSalesTransaction($entity);
	        }else{
		        $this->getDoctrine()->getRepository('AccountingBundle:Transaction')-> insertCustomerOutstandingTransaction($entity);


	        }
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;

    }

    public function customerLedgerAction()
    {
	    $em = $this->getDoctrine()->getManager();
    	$data = $_REQUEST;
	    $user = $this->getUser();
	    $customer ='';
	    $overview = '';
	    if(isset($data['mobile']) and !empty($data['mobile'])){
		    $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('mobile'=>$data['mobile']));
		    $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data);

	    }
	    $entities = $em->getRepository('AccountingBundle:AccountSales')->customerLedger($user,$data);
	    return $this->render('AccountingBundle:AccountHotel:salesLedger.html.twig', array(
		    'entities' => $entities->getResult(),
		    'overview' => $overview,
		    'customer' => $customer,
		    'searchForm' => $data,
	    ));
    }


}
