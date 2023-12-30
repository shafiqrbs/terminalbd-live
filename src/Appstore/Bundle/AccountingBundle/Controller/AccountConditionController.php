<?php

namespace Appstore\Bundle\AccountingBundle\Controller;
use Appstore\Bundle\AccountingBundle\Entity\AccountCondition;
use Appstore\Bundle\AccountingBundle\Entity\AccountConditionLedger;
use Appstore\Bundle\AccountingBundle\Form\AccountConditionLedgerType;
use Appstore\Bundle\AccountingBundle\Form\AccountConditionType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\Response;


/**
 * Customer controller.
 *
 */
class AccountConditionController extends Controller
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
     * @Secure(roles="ROLE_ACCOUNTING,ROLE_DOMAIN,ROLE_DOMAIN_ACCOUNTING_CONDITION")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:AccountCondition')->findBy(array('globalOption'=>$globalOption),array('name'=>'ASC'));
        return $this->render('AccountingBundle:AccountCondition:index.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_ACCOUNTING,ROLE_DOMAIN")
     */

    public function ledgerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $pagination = $em->getRepository('AccountingBundle:AccountConditionLedger')->getLedgerLists($globalOption->getId(),$data);
        $entities = $this->paginate($pagination);
        return $this->render('AccountingBundle:AccountCondition:ledger.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }

    /**
     * Creates a new Customer entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountCondition();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $option = $this->getUser()->getGlobalOption();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
	        $entity->setGlobalOption($option);
	        $entity->setMobile($mobile);
	        $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->insertConditionAccount($entity);
            return $this->redirect($this->generateUrl('account_condition'));
        }
        return $this->render('AccountingBundle:AccountCondition:new.html.twig', array(
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
    private function createCreateForm(AccountCondition $entity)
    {
        $form = $this->createForm(new AccountConditionType(), $entity, array(
            'action' => $this->generateUrl('account_condition_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */
    public function newAction()
    {
        $entity = new AccountCondition();
        $form   = $this->createCreateForm($entity);
        return $this->render('AccountingBundle:AccountCondition:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $entity = $this->getDoctrine()->getRepository("AccountingBundle:AccountCondition")->findOneBy(array('globalOption'=>$option,'id'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountCondition:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Customer entity.
    *
    * @param AccountCondition $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountCondition $entity)
    {
        $form = $this->createForm(new AccountConditionType(), $entity, array(
            'action' => $this->generateUrl('account_condition_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
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
        $option = $this->getUser()->getGlobalOption();
        $entity = $this->getDoctrine()->getRepository("AccountingBundle:AccountCondition")->findOneBy(array('globalOption'=>$option,'id'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
	        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
	        $entity->setMobile($mobile);
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->insertConditionAccount($entity);
            return $this->redirect($this->generateUrl('account_condition'));
        }
        return $this->render('AccountingBundle:AccountCondition:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $entity = $this->getDoctrine()->getRepository("AccountingBundle:AccountCondition")->findOneBy(array('globalOption'=>$option,'id'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }
        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }
        return $this->redirect($this->generateUrl('account_condition'));
    }


    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */
    public function ledgerNewAction()
    {
        $entity = new AccountConditionLedger();
        $form   = $this->createLedgerForm($entity);
        return $this->render('AccountingBundle:AccountCondition:new-ledger.html.twig', array(
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
    private function createLedgerForm(AccountConditionLedger $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountConditionLedgerType($option), $entity, array(
            'action' => $this->generateUrl('account_condition_ledger_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Creates a new AccountConditionLedger entity.
     *
     */
    public function createLedgerAction(Request $request)
    {
        $entity = new AccountConditionLedger();
        $form = $this->createLedgerForm($entity);
        $form->handleRequest($request);
        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        if($form->isValid() && empty($method)){
            $entity->setGlobalOption($option);
            if( in_array($entity->getMode(),array("Debit"))){
                $entity->setDebit(abs($entity->getAmount()));
                $entity->setAmount(0);
                $entity->setTransactionMethod(null);
            }else{
                $entity->setCredit(abs($entity->getAmount()));
                $entity->setAmount(abs($entity->getAmount()));
            }
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountConfig == 1){
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
            return $this->redirect($this->generateUrl('account_condition_ledger'));
        }elseif(($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {
            $entity->setGlobalOption($option);
            if( in_array($entity->getMode(),array("Debit"))){
                $entity->setDebit(abs($entity->getAmount()));
                $entity->setAmount(0);
                $entity->setTransactionMethod(null);
            }else{
                $entity->setCredit(abs($entity->getAmount()));
                $entity->setAmount('-'.$entity->getAmount());
            }
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountConfig == 1){
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
            return $this->redirect($this->generateUrl('account_condition_ledger'));
        }

        $this->get('session')->getFlashBag()->add(
            'notice',"May be you are missing to select bank or mobile account"
        );
        return $this->render('AccountingBundle:AccountCondition:new-ledger.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
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
        $entity = $em->getRepository('AccountingBundle:AccountConditionLedger')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountConditionLedger entity.');
        }
        $amount = abs(floatval($data['value']));
        if($data['name'] == 'Debit'){
            $entity->setDebit(abs($amount));
            $entity->setAmount(abs($amount));
        }else{
            $entity->setCredit(abs($amount));
            $entity->setAmount('-'.$amount);
        }
        $em->flush();
        exit;
    }

    /**
     * Displays a form to edit an existing Expenditure entity.
     *
     */
    public function deleteLedgerAction($id)
    {

        $option = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountConditionLedger')->findOneBy(array('globalOption'=>$option,'id'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountConditionLedger entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_SALES")
     */

    public function approveAction(AccountConditionLedger $entity)
    {
        if (!empty($entity) and $entity->getProcess() != 'approved') {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            if(empty($entity->getTransactionMethod()) and in_array($entity->getMode(),array( 'Due','Advance'))){
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
            $em->getRepository('AccountingBundle:AccountConditionLedger')->updateConditionBalance($entity);
            if($entity->getAmount() > 0 and in_array($entity->getMode(),array( 'Due','Advance')) ){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertAccountConditionCash($entity);
            }
            return new Response('success');

        } else {

            return new Response('failed');
        }

    }




}
