<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Form\AssociationInvoiceType;
use Appstore\Bundle\DomainUserBundle\Event\AssociationSmsEvent;
use Appstore\Bundle\DomainUserBundle\Form\MemberEditProfileType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer controller.
 *
 */
class AssociationController extends Controller
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
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN,ROLE_MEMBER_ASSOCIATION")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $data['type'] = "member";
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Customer')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $batches = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->studentBatchChoiceList();
        $bloods = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->bloodsChoiceList();
        return $this->render('DomainUserBundle:Association:index.html.twig', array(
            'entities' => $pagination,
            'batches' => $batches,
            'bloods' => $bloods,
            'searchForm' => $data,
        ));
    }


    /**
     * Creates a new Customer entity.
     *
     */
    public function createAction(Request $request)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = new Customer();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
        $entity = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'mobile'=>$mobile));
        if ($form->isValid() and empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($globalOption);
	        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
	        $entity->setMobile($mobile);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('domain_customer'));
        }

        return $this->render('DomainUserBundle:Association:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Customer entity.
     *
     * @param Customer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Customer $entity)
    {
        $em = $this->getDoctrine()->getRepository('DomainUserBundle:Customer');
        $form = $this->createForm(new MemberEditProfileType($em), $entity, array(
            'action' => $this->generateUrl('domain_association_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN,ROLE_MEMBER_ASSOCIATION")
     */
    public function newAction()
    {
        $entity = new Customer();
        $form   = $this->createCreateForm($entity);

        return $this->render('DomainUserBundle:Association:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Customer entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $config,'id'=> $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ElectionMember entity.');
        }
        $html = $this->renderView('DomainUserBundle:Association:profile.html.twig',
            array('entity' => $entity)
        );
        return New Response($html);
    }

     /**
     * Finds and displays a Customer entity.
     *
     */
    public function processAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $config, 'id' => $id ));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Association Member entity.');
        }
        if ($entity->getProcess() == 'Pending'){
            $entity->setProcess('Checked');
            $entity->setCheckedBy($this->getUser());
        }elseif ($entity->getProcess() == 'Checked'){
            $entity->setProcess('Approved');
            $entity->setApprovedBy($this->getUser());
            /* @var $global GlobalOption */
            $global = $this->getUser()->getGlobalOption();
            if($global->getSmsSenderTotal() and $global->getSmsSenderTotal()->getRemaining() > 0 and $global->getNotificationConfig()->getSmsActive() == 1) {
                $dispatcher = $this->container->get('event_dispatcher');
                $msg = "Dear Member, Your registration successfully done!";
                $dispatcher->dispatch('appstore.customer.post.member_sms', new AssociationSmsEvent($entity,$msg));
            }

        }
        $em->persist($entity);
        $em->flush();
        return New Response("success");
    }

    /**
     * Finds and displays a Customer entity.
     *
     */
    public function groupSmsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Customer')->findBy(array('globalOption' => $config));
        /* @var $global GlobalOption */
        $global = $this->getUser()->getGlobalOption();
        if($global->getSmsSenderTotal() and $global->getSmsSenderTotal()->getRemaining() > 0 and $global->getNotificationConfig()->getSmsActive() == 1) {
            foreach ($entities as $entity){
                $msg = "Congratulation! your registration has done.your member ID is {$entity->getCustomerId()}. Please click www.bhaws.org for give your monthly payment info. Thanks BHAWS";
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('appstore.customer.post.member_sms', new AssociationSmsEvent($entity, $msg));
            }
        }
        return New Response("success");
    }

    /**
     * Finds and displays a Customer entity.
     *
     */

    public function memberShowAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }
        return $this->render('DomainUserBundle:Customer:memberShow.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN,ROLE_MEMBER_ASSOCIATION")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('DomainUserBundle:Association:editProfile.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Customer entity.
    *
    * @param Customer $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Customer $entity)
    {
        $em = $this->getDoctrine()->getRepository('DomainUserBundle:Customer');
        $form = $this->createForm(new MemberEditProfileType($em), $entity, array(
            'action' => $this->generateUrl('domain_association_profile_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Customer entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
	        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
	        $entity->setMobile($mobile);
            $em->flush();
            return $this->redirect($this->generateUrl('domain_association'));
        }

        return $this->render('DomainUserBundle:Association:editProfile.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    public function batchSelectAction()
    {
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->studentBatchChoiceList();
        $items = array();
        $items[] = array('value' => '','text'=> '-Select Batch-');
        foreach ($entities as $entity):
            $items[] = array('value' => $entity,'text'=> $entity);
        endforeach;
        $items[]=array('value' => '','text'=> 'Empty Batch');
        return new JsonResponse($items);
    }

    public function bloodSelectAction()
    {
        $items[] = array('value' => '','text'=> '-Blood-');
        $items[]=array('value' => 'A+','text'=> 'A+');
        $items[]=array('value' => 'A-','text'=> 'A-');
        $items[]=array('value' => 'B-','text'=> 'B-');
        $items[]=array('value' => 'B+','text'=> 'B+');
        $items[]=array('value' => 'O+','text'=> 'O+');
        $items[]=array('value' => 'O-','text'=> 'O-');
        $items[]=array('value' => 'AB+','text'=> 'AB+');
        $items[]=array('value' => 'AB-','text'=> 'AB-');
        $items[]=array('value' => '','text'=> 'Empty');
        return new JsonResponse($items);
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $setName = 'set'.$data['name'];
        $setValue = $data['value'];
        $entity->$setName($setValue);
        $em->persist($entity);
        $em->flush();
        exit;

    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN,ROLE_MEMBER_ASSOCIATION_ADMIN")
     */

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$globalOption,'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }
        $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($entity->getUser());
        if($user){
            $user->setEnabled(false);
            $user->setIsDelete(true);
            $user->setGlobalOption(NULL);
        }
        $entity->setGlobalOption(NULL);
        $entity->setStatus(false);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->deleteAssociationInvoice($entity);
        return new Response('Success');
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoComplete($go,$item);
        }
        return new JsonResponse($item);
    }

    public function searchCustomerNameAction($customer)
    {
        return new JsonResponse(array(
            'id'=> $customer,
            'text' => $customer
        ));
    }

    public function autoMobileSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteName($go,$item);
        }
        return new JsonResponse($item);
    }

    public function searchCustomerMobileAction($customer)
    {
        return new JsonResponse(array(
            'id'=> $customer,
            'text' => $customer
        ));
    }

    public function autoCodeSearchAction(Request $request)
    {

        $q = $_REQUEST['term'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteCode($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('id' => $entity['customer'],'value' => $entity['text']);
        endforeach;
        return new JsonResponse($items);

    }

    public function searchCodeAction($customer)
    {
        return new JsonResponse(array(
            'id'=> $customer,
            'text' => $customer
        ));
    }


    public function autoLocationSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $item = $this->getDoctrine()->getRepository('SettingLocationBundle:Location')->searchAutoComplete($item);
        }
        return new JsonResponse($item);

    }

    public function searchLocationNameAction($location)
    {
        return new JsonResponse(array(
            'id'=> $location,
            'text' => $location
        ));
    }

    public function searchAutoCompleteNameAction()
    {
        $q = $_REQUEST['q'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteName($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('id' => $entity['id'],'value' => $entity['id']);
        endforeach;
        return new JsonResponse($entities);

    }

    public function searchAutoCompleteMobileAction()
    {
        $q = $_REQUEST['term'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoComplete($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('id' => $entity['customer'],'value' => $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN,ROLE_MEMBER_ASSOCIATION")
     */

    public function invoiceNewAction($customer)
    {

        $em = $this->getDoctrine()->getManager();
        $option =  $this->getUser()->getGlobalOption();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array("globalOption" => $option,"customerId" => $customer));

        $lastInvoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->getLastInvoiceParticular($customer);

        $entity = new BusinessInvoice();
        $editForm = $this->createInvoiceCreateForm($entity,$customer);
        $outstanding = 0;

        return $this->render("DomainUserBundle:Association/Invoice:new.html.twig", array(

            'globalOption'      => $this->getUser()->getGlobalOption(),
            'lastInvoice'       => $lastInvoice,
            'entity'            => $entity,
            'customer'          => $customer,
            'outstanding'       => $outstanding,
            'form'              => $editForm->createView(),

        ));

    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param BusinessInvoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createInvoiceCreateForm(BusinessInvoice $entity, Customer $customer)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AssociationInvoiceType($globalOption), $entity, array(
            'action' => $this->generateUrl('domain_association_invoice_create', array('customer' => $customer->getCustomerId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
                'enctype' => 'multipart/form-data',

            )
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN,ROLE_MEMBER_ASSOCIATION")
     */

    public function invoiceCreateAction(Request $request, $customer)
    {
        $data = $request->request->all();
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();

        $option =  $this->getUser()->getGlobalOption();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array("globalOption" => $option,"customerId" => $customer));
        $lastInvoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->getLastInvoiceParticular($customer);

        $entity = new BusinessInvoice();
        $form = $this->createInvoiceCreateForm($entity,$customer);
        $form->handleRequest($request);
        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        if (($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setBusinessConfig($config);
            $entity->setCustomer($customer);
            $entity->setMobile($customer->getMobile());
            $entity->setReceived($data['paymentTotal']);
            $entity->setDue(0);
            $entity->setEndDate($lastInvoice->getEndDate());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertStudentMonthlyParticular($entity,$lastInvoice, $data);
            $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($entity);
            if($entity->getProcess() == "Done"){
                $accountSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessAccountInvoice($entity);
                $em->getRepository('AccountingBundle:Transaction')->serviceTransaction($accountSales);
                /* @var $global GlobalOption */
                $global = $this->getUser()->getGlobalOption();
                if($global->getSmsSenderTotal() and $global->getSmsSenderTotal()->getRemaining() > 0 and $global->getNotificationConfig()->getSmsActive() == 1) {
                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch('setting_tool.post.association_invoice', new \Setting\Bundle\ToolBundle\Event\AssociationInvoiceSmsEvent($entity));
                }
            }
            return $this->redirect($this->generateUrl('domain_association_invoice'));
        }
        $this->get('session')->getFlashBag()->add(
            'warning', "Payment information does not valid"
        );
        return $this->render("DomainUserBundle:Association/Invoice:new.html.twig", array(
            'globalOption' => $user->getGlobalOption(),
            'customer' => $customer,
            'lastInvoice'       => $lastInvoice,
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN,ROLE_CRM_ASSOCIATION")
     */
    public function invoiceAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->invoiceLists( $config->getId(),$data);
        $pagination = $this->paginate($entities);
        return $this->render("DomainUserBundle:Association/Invoice:index.html.twig", array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN,ROLE_MEMBER_ASSOCIATION")
     */

    public function invoiceShowAction($id)
    {
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $this->getDoctrine()->getRepository("BusinessBundle:BusinessInvoice")->findOneBy(array('businessConfig' => $config,'id' => $id));
        return $this->render("DomainUserBundle:Association/Invoice:show.html.twig", array(
            'entity' => $entity,
        ));

    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN,ROLE_MEMBER_ASSOCIATION")
     */
     public function invoiceEditAction($id)
    {
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $this->getDoctrine()->getRepository("BusinessBundle:BusinessInvoice")->findOneBy(array('businessConfig' => $config,'id' => $id));
        $form = $this->editInvoiceCreateForm($entity);
        return $this->render("DomainUserBundle:Association/Invoice:edit.html.twig", array(
            'entity' => $entity,
            'globalOption' => $entity->getCustomer()->getGlobalOption(),
            'form' => $form->createView(),
        ));

    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param BusinessInvoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function editInvoiceCreateForm(BusinessInvoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AssociationInvoiceType($globalOption), $entity, array(
            'action' => $this->generateUrl('domain_association_invoice_update', array('id'=>$entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
                'enctype' => 'multipart/form-data',

            )
        ));
        return $form;
    }

    public function invoiceUpdateAction(Request $request,$id)
    {
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $this->getDoctrine()->getRepository("BusinessBundle:BusinessInvoice")->findOneBy(array('businessConfig' => $config,'id' => $id));
        $form = $this->editInvoiceCreateForm($entity);
        $form->handleRequest($request);
        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        if (($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setBusinessConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Data has been inserted successfully"
            );
            if($entity->getProcess() == "Done"){
            $accountSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessAccountInvoice($entity);
            $em->getRepository('AccountingBundle:Transaction')->serviceTransaction($accountSales);
            }
            $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($entity);
            return $this->redirect($this->generateUrl('domain_association_invoice'));
        }
        $this->get('session')->getFlashBag()->add(
            'warning', "Payment information does not valid"
        );
        return $this->render("DomainUserBundle:Association/Invoice:edit.html.twig", array(
            'globalOption' => $entity->getCustomer()->getGlobalOption(),
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function invoicePrintAction($id)
    {
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $this->getDoctrine()->getRepository("BusinessBundle:BusinessInvoice")->findOneBy(array('businessConfig' => $config,'id' => $id));

        $em = $this->getDoctrine()->getManager();

        /* @var $config BusinessConfig */

        $print = (isset($_REQUEST['print']) and !empty($_REQUEST['print'])) ?  $_REQUEST['print'] : 'print';

        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if ($config->getId() == $entity->getBusinessConfig()->getId()) {

            if($config->isCustomInvoicePrint() == 1){
                $template = 'invoice-'.$config->getGlobalOption()->getId();
            }else{
                $template = !empty($config->getInvoiceType()) ? $config->getInvoiceType():'print';
            }
            $amountInWords = '';
            if($entity->getProcess() == "Quotation"){
                $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            }
            $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($this->getUser()->getGlobalOption(),$entity->getCustomer());
            $balance = empty($result) ? 0 : $result;
            return  $this->render("BusinessBundle:Print:{$template}.html.twig",
                array(
                    'config' => $config,
                    'entity' => $entity,
                    'balance' => $balance,
                    'amountInWords' => $amountInWords,
                    'print' => $print,
                )
            );
        }
    }



}
