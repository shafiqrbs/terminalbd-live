<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Form\AccountMedicinePurchaseType;
use Appstore\Bundle\AccountingBundle\Form\AccountSalesInvoiceType;
use Appstore\Bundle\AccountingBundle\Form\AccountSalesMedicineType;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountSalesMedicine controller.
 *
 */
class AccountMedicineController extends Controller
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
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_SALES,ROLE_DOMAIN")
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
        return $this->render('AccountingBundle:AccountMedicine:sales.html.twig', array(
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_PURCHASE,ROLE_DOMAIN")
	 */

	public function customerOutstandingAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data =$_REQUEST;
		$globalOption = $this->getUser()->getGlobalOption();
		$entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption,$data);
		$pagination = $this->paginate($entities);
		return $this->render('AccountingBundle:AccountMedicine:customerOutstanding.html.twig', array(
			'entities' => $pagination,
			'searchForm' => $data,
		));
	}



	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_PURCHASE,ROLE_DOMAIN")
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
        return $this->render('AccountingBundle:AccountMedicine:purchase.html.twig', array(
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_PURCHASE,ROLE_DOMAIN")
	 */

    public function vendorOutstandingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->vendorMedicineOutstanding($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:AccountMedicine:purchaseOutstanding.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_PURCHASE,ROLE_DOMAIN")
	 */


	public function purchaseNewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountPurchase();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:AccountMedicine:purchaseNew.html.twig', array(
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
            $entity->setProcessHead('medicine');
            $entity->setProcessType('Payment');
            if($entity->getPayment() < 0){
                $entity->setPurchaseAmount(abs($entity->getPayment()));
                $entity->setPayment(0);
                $entity->setProcessType('Opening');
                $entity->setTransactionMethod(null);
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_purchase_medicine'));
        }

        return $this->render('AccountingBundle:AccountMedicine:purchaseNew.html.twig', array(
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
        $form = $this->createForm(new AccountMedicinePurchaseType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_purchase_medicine_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_SALES,ROLE_DOMAIN")
	 */

	public function salesNewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountSales();
        $form   = $this->salesCreateForm($entity);
        return $this->render('AccountingBundle:AccountMedicine:new.html.twig', array(
            'entity' => $entity,
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
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_sales_medicine'));
        }

        return $this->render('AccountingBundle:AccountMedicine:new.html.twig', array(
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
        $form = $this->createForm(new AccountSalesMedicineType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_sales_medicine_create'),
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
        $entity = $em->getRepository('AccountingBundle:AccountSales')->findOneBy(array('globalOption'=>$global,'medicineSales'=>$sales->getId()));
        if (!$entity) {
            return $this->redirect($this->generateUrl('account_sales_medicine_new'));
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
        return $this->render('AccountingBundle:AccountMedicine:invoice.html.twig', array(
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
            return $this->redirect($this->generateUrl('account_sales_medicine'));
        }
        return $this->render('AccountingBundle:AccountMedicine:invoice.html.twig', array(
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
            'action' => $this->generateUrl('account_sales_medicine_update', array('id' => $entity->getId())),
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
        if (!empty($entity) and $entity->getProcess() != 'approved' ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $em->getRepository('AccountingBundle:AccountSales')->updateCustomerBalance($entity);
            if($entity->getMedicineSales()){
                $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->updateSalesPaymentReceive($entity);
            }
            // $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountSalesTransaction($entity);
            return new Response('success');
        } else {
            return new Response('failed');
        }


    }

    public function salesMedicineReverseAction(AccountSales $entity){

	    $em = $this->getDoctrine()->getManager();
    	$this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->accountReverse($entity);
	    $entity->setProcess(null);
	    $entity->setApprovedBy(null);
	    $entity->setBalance(0);
	    $em->flush();
	    return $this->redirect($this->generateUrl('account_sales_medicine'));
    }

	public function purchaseMedicineReverseAction(AccountPurchase $entity){

		$em = $this->getDoctrine()->getManager();
		$this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountReverse($entity);
		$entity->setProcess(null);
		$entity->setApprovedBy(null);
		$entity->setBalance(0);
		$em->flush();
		return $this->redirect($this->generateUrl('account_purchase_medicine'));

	}


}
