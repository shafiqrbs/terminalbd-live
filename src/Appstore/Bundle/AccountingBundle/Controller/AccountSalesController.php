<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\AccountingBundle\Form\AccountSalesInvoiceType;
use Appstore\Bundle\AccountingBundle\Form\AccountSalesType;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountSales controller.
 *
 */
class AccountSalesController extends Controller
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
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_SALES, ROLE_DOMAIN")
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('AccountingBundle:AccountSales')->findWithSearch($user,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->receiveModeOverview($user,$data);
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $groups = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getProcessModes($user->getGlobalOption()->getId());
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getCreatedUsers($user->getGlobalOption()->getId());
        return $this->render('AccountingBundle:AccountSales:index.html.twig', array(
            'entities' => $pagination,
            'transactionMethods' => $transactionMethods,
            'users' => $users,
            'groups' => $groups,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_SALES_REPORT, ROLE_DOMAIN")
     */
    public function customerOutstandingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:AccountSales:customerOutstanding.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_SALES_REPORT, ROLE_DOMAIN")
     */

    public function customerSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSummary($globalOption,$data);
        return $this->render('AccountingBundle:AccountSales/Report:customerReport.html.twig', array(
            'entities' => $entities,
            'option' => $globalOption,
            'searchForm' => $data,
        ));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_SALES_REPORT, ROLE_DOMAIN")
     */

    public function userSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->userSummary($globalOption,$data);
        if(isset($data['submit']) and $data['submit'] == 'print' and isset($data['employee']) and $data['employee']){
            return $this->render('AccountingBundle:AccountSales/Report:userSummary.html.twig', array(
                'entities' => $entities,
                'option' => $globalOption,
                'searchForm' => $data,
            ));
        }else{
            return $this->render('AccountingBundle:AccountSales/Report:userSummary.html.twig', array(
                'entities' => $entities,
                'option' => $globalOption,
                'searchForm' => $data,
            ));
        }
    }


    /**
     * Lists all AccountSalesReturn entities.
     *
     */
    public function salesReturnAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = $em->getRepository('AccountingBundle:AccountSalesReturn')->findWithSearch($this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSalesReturn')->salesReturnOverview($this->getUser(),$data);
        return $this->render('AccountingBundle:AccountSales:salesReturn.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }

    /**
     * Creates a new AccountSales entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountSales();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $data = $request->request->all();
        if($form->isValid() && empty($method)){
            $entity->setGlobalOption($option);
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            $customer = $form["customer"]->getData();
            if (empty($customer) and !empty($data['customerMobile'])){
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($option, $mobile, $data);
                $entity->setCustomer($customer);

            }elseif (!empty($customer)){
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($customer);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$option, 'mobile' => $mobile));
                $entity->setCustomer($customer);
            }

            if( in_array($entity->getProcessHead(),array("Debit"))){
                $entity->setTotalAmount(abs($entity->getAmount()));
                $entity->setAmount(0);
                $entity->setTransactionMethod(null);
            }else{
                $entity->setAmount(abs($entity->getAmount()));
            }
            if( in_array($entity->getProcessHead(),array('Credit','Discount'))){
                $entity->setTransactionMethod(null);
            }
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if(isset($data['created']) and !empty($data['created'])){
                $created = $data['created'];
                $date = new \DateTime($created);
                $entity->setCreated($date);
                $entity->setUpdated($date);
            }elseif($accountConfig == 1){
                $datetime = new \DateTime("yesterday 23:30:30");
                $entity->setCreated($datetime);
                $entity->setUpdated($datetime);
            }else{
                $datetime = new \DateTime("now");
                $entity->setUpdated($datetime);
            }

            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            if($this->getUser()->getCheckExistRole('ROLE_DOMAIN_ACCOUNTING_OPERATOR')){
                return $this->redirect($this->generateUrl('account_sales_new'));
            }else{
                return $this->redirect($this->generateUrl('account_sales'));
            }
        }elseif(($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {
            $entity->setGlobalOption($option);
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            $customer = $form["customer"]->getData();
            if (empty($customer) and !empty($data['customerMobile'])){
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($option, $mobile, $data);
                $entity->setCustomer($customer);

            }elseif (!empty($customer)){
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($customer);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$option, 'mobile' => $mobile));
                $entity->setCustomer($customer);
            }
            if( in_array($entity->getProcessHead(),array("Debit"))){
                $entity->setTotalAmount(abs($entity->getAmount()));
                $entity->setAmount(0);
                $entity->setTransactionMethod(null);
            }else{
                $entity->setAmount(abs($entity->getAmount()));
            }
            if( in_array($entity->getProcessHead(),array('Credit','Discount'))){
                $entity->setTransactionMethod(null);
            }
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if(isset($data['created']) and !empty($data['created'])){
                $created = $data['created'];
                $date = new \DateTime($created);
                $entity->setCreated($date);
                $entity->setUpdated($date);
            }elseif($accountConfig == 1){
                $datetime = new \DateTime("yesterday 23:30:30");
                $entity->setCreated($datetime);
                $entity->setUpdated($datetime);
            }else{
                $datetime = new \DateTime("now");
                $entity->setUpdated($datetime);
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            if($this->getUser()->getCheckExistRole('ROLE_DOMAIN_ACCOUNTING_OPERATOR')){
                return $this->redirect($this->generateUrl('account_sales_new'));
            }else{
                return $this->redirect($this->generateUrl('account_sales'));
            }
        }

        $this->get('session')->getFlashBag()->add(
            'notice',"May be you are missing to select bank or mobile account"
        );
        return $this->render('AccountingBundle:AccountSales:new.html.twig', array(
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
    private function createCreateForm(AccountSales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountSalesType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_sales_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_SALES,ROLE_DOMAIN_ACCOUNTING_OPERATOR,ROLE_DOMAIN")
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountSales();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:AccountSales:new.html.twig', array(
            'entity' => $entity,
            'banks' => $banks,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new AccountSales entity.
     *
     */
    public function duePaymentAction(Sales $sales)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountSales();
        $entity->setGlobalOption( $this->getUser()->getGlobalOption());
        $entity->setSales($sales);
        $entity->setCustomer($sales->getCustomer());
        $entity->setTransactionMethod($this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->find(1));
        $entity->setProcessHead('Due');
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        $form   = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountSales:invoice.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountSales entity.
     *
     */
    public function printAction(AccountSales $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity and $entity->getGlobalOption()->getId() != $this->getUser()->getGlobalOption()->getId() ) {
            throw $this->createNotFoundException('Unable to find AccountSales entity.');
        }
        $template = ($entity->getGlobalOption()->getAccountingConfig()->isCustomPrint() == 1) ? $entity->getGlobalOption()->getSubDomain():'print';
        $amountInWord = $this->get('settong.toolManageRepo')->intToWords($entity->getAmount());
        $outstanding = $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->customerSingleOutstanding($entity->getGlobalOption(),$entity->getCustomer());
        return $this->render("AccountingBundle:AccountSales:{$template}.html.twig", array(
            'entity'           => $entity,
            'config'           => $entity->getGlobalOption()->getAccountingConfig(),
            'outstanding'     => $outstanding,
            'amountInWord'     => $amountInWord,
        ));
    }

    /**
     * Displays a form to edit an existing AccountSales entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountSales')->find($id);

        if (!$entity and $entity->getGlobalOption()->getId() != $this->getUser()->getGlobalOption()->getId() ) {
            throw $this->createNotFoundException('Unable to find AccountSales entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountSales:invoice.html.twig', array(
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
            'action' => $this->generateUrl('account_sales_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing AccountSales entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountSales')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountSales entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {

            if ($entity->getSales()->getDue() < $entity->getAmount() ){
                $this->get('session')->getFlashBag()->add(
                    'notice',"Payment amount receive must be same or less as due amount"
                );
                return $this->redirect($this->generateUrl('account_sales_edit',array('id' => $entity->getId())));
            }
            $em->flush();
            return $this->redirect($this->generateUrl('account_sales'));
        }

        return $this->render('AccountingBundle:AccountSales:invoice.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing Expenditure entity.
     *
     */
    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountSales')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        if($data['name'] == 'TotalAmount'){
            $entity->setTotalAmount(abs($data['value']));
            $entity->setAmount(0);
        }else{
            $entity->setAmount($data['value']);
        }
        $em->flush();
        exit;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_SALES")
     */

    public function approveAction(AccountSales $entity)
    {
	    if (!empty($entity) and $entity->getProcess() != 'approved') {
		    $em = $this->getDoctrine()->getManager();
		    $entity->setProcess('approved');
		    $entity->setApprovedBy($this->getUser());
		    if(empty($entity->getTransactionMethod()) and in_array($entity->getProcessHead(),array( 'Due','Advance'))){
			    $method = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->find(1);
			    $entity->setTransactionMethod($method);
		    }
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountConfig == 1){
                $datetime = new \DateTime("yesterday 23:30:30");
                $entity->setCreated($datetime);
                $entity->setUpdated($datetime);
            }
		    $em->flush();
		    if(!empty($entity->getSales()) and $entity->getProcessHead() == 'Due'){
		    	$this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesPaymentReceive($entity);
		    }
		    $em->getRepository('AccountingBundle:AccountSales')->updateCustomerBalance($entity);
		    if($entity->getAmount() > 0 and in_array($entity->getProcessHead(),array( 'Due','Advance')) ){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertSalesCash($entity);
		    }

            $customer = $entity->getCustomer();
            $name = $customer->getName();
            $orgName = $entity->getGlobalOption()->getName();
            $hotline = $entity->getGlobalOption()->getHotline();
            $mobile = "+88".$customer->getMobile();
           // $mobile = "+8801828148148";

            $outstanding = number_format($entity->getAmount(),2);

            $msg = "Sir, Your payment received BDT {$outstanding} TK, Thanks for being with us.";
            $msg = nl2br("=={$orgName}==  Dear {$msg}");
            if($entity->isSmsAlert() == 1 and $entity->getGlobalOption()->getAccountingConfig()->isSalesReceiveSms() == 1 and $entity->getGlobalOption()->getSmsSenderTotal() and $entity->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $entity->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1) {
                $this->send($msg,$mobile);
                $this->getDoctrine()->getRepository('SettingToolBundle:SmsSender')->insertCustomerOutstandingSms($customer,$msg,'success');
                $this->get('session')->getFlashBag()->add(
                    'success'," $name SMS has benn sent successfully"
                );
            }
		    return new Response('success');

        } else {

            return new Response('failed');
        }

    }

    /**
     * Deletes a Expenditure entity.
     *
     */
    public function deleteAction(AccountSales $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNT_REVERSE,ROLE_DOMAIN")
     */

    public function salesReverseAction(AccountSales $entity){

        $em = $this->getDoctrine()->getManager();
        $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->accountReverse($entity);
        $entity->setProcess(null);
        $entity->setApprovedBy(null);
        $entity->setTotalAmount(0);
        $entity->setAmount(0);
        $entity->setBalance(0);
        $em->flush();
        return $this->redirect($this->generateUrl('account_sales'));

    }


    public function salesPrint(AccountSales $sales)
    {
    	$em = $this->getDoctrine()->getManager();
	    $template = $sales->getGlobalOption()->getSlug();
	    $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($sales->getGlobalOption(), $data = array('mobile'=> $sales->getCustomer()->getMobile()));
	    $balance = empty($result) ? 0 :$result[0]['customerBalance'];
	    $amountInWords = $this->get('settong.toolManageRepo')->intToWords($sales->getAmount());
	    return  $this->render("BusinessBundle:Print:{$template}.html.twig",
		    array(
			    'amountInWords' => $amountInWords,
			    'entity' => $sales,
			    'balance' => $balance,
			    'print' => 'print',
		    )
	    );
    }

    public function getCustomerLedgerAction()
    {

	    $customerId = $_REQUEST['customer'];
	    $globalOption = $this->getUser()->getGlobalOption();
	    $balance = 0;
	    $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'id'=> $customerId));
	    if(!empty($customer)){
		    $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($globalOption,$customer);
		    $balance = empty($result) ? 0 : $result;
	    }
        $taka = number_format($balance).' Taka';
	    return new Response($taka);

    }

    public function getCustomerSalesLedgerAction()
    {

        $customerId = $_REQUEST['customer'];
        $globalOption = $this->getUser()->getGlobalOption();
        $balance = 0;
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'mobile'=> $customerId));
        if(!empty($customer)){
            $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($globalOption,$customer);
            $balance = empty($result) ? 0 : $result;
        }
        $taka = number_format($balance).' Taka';
        return new Response($taka);

    }

    function send($msg, $phone, $sender = ""){

        $curl = curl_init();
        $data =array(
            'apikey' => '198a7497a859e5fe',
            'secretkey' => 'cb6adaba',
            'callerID' => '8809612770474',
            'toUser' => $phone,
            'messageContent' => $msg,
        );
        echo $phone;
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://smpp.ajuratech.com:7790/sendtext",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        var_dump($response);
        print_r(curl_error($curl));
        curl_close($curl);
    }

}
