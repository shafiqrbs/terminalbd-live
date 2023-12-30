<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\PettyCash;
use Appstore\Bundle\AccountingBundle\Form\PettyCashType;
use Symfony\Component\HttpFoundation\Response;

/**
 * PettyCash controller.
 *
 */
class PettyCashController extends Controller
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
     * Lists all PettyCash entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:PettyCash')->findBy(array('globalOption' => $globalOption,'parent'=>NULL));
        $pagination = $this->paginate($entities);
        //$overview = $this->getDoctrine()->getRepository('AccountingBundle:PettyCash')->salesOverview($inventory,$data);
        $accountHead = $em->getRepository('AccountingBundle:AccountHead')->findBy(array('status'=>1),array('name'=>'asc'));
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1));
        $accountBanks = $em->getRepository('AccountingBundle:AccountBank')->findBy(array('globalOption'=>$globalOption,'status'=>1));
        $accountMobileBank = $em->getRepository('AccountingBundle:AccountMobileBank')->findBy(array('globalOption'=>$globalOption,'status'=>1));
        return $this->render('AccountingBundle:PettyCash:index.html.twig', array(
            'entities' => $pagination,
            'transactionMethods' => $transactionMethods,
            'accountBanks' => $accountBanks,
            'accountMobileBank' => $accountMobileBank,
            'overview' => '',
        ));
    }
    /**
     * Creates a new PettyCash entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new PettyCash();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $lastBalance = $em->getRepository('AccountingBundle:PettyCash')->lastInsertCash($entity);
            $em = $this->getDoctrine()->getManager();
            $entity->setBalance($lastBalance + $entity->getAmount());
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('account_pettycash'));
        }
        return $this->render('AccountingBundle:PettyCash:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PettyCash entity.
     *
     * @param PettyCash $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PettyCash $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PettyCashType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_pettycash_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new PettyCash entity.
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new PettyCash();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:PettyCash:new.html.twig', array(
            'entity' => $entity,
            'banks' => $banks,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PettyCash entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:PettyCash')->find($id);
         if (!$entity) {
            throw $this->createNotFoundException('Unable to find PettyCash entity.');
        }

        return $this->render('AccountingBundle:PettyCash:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing PettyCash entity.
     *
     */
    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $pettyCash = $em->getRepository('AccountingBundle:PettyCash')->find($data['pk']);
        if (!$pettyCash) {
            throw $this->createNotFoundException('Unable to find PettyCash entity.');
        }
        $pettyCash->setAmount($data['value']);
        $em->flush();
        exit;
    }

    /**
     * Deletes a PettyCash entity.
     *
     */
    public function deleteAction(PettyCash $pettyCash)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$pettyCash) {
            throw $this->createNotFoundException('Unable to find PettyCash entity.');
        }
        $em->remove($pettyCash);
        $em->flush();
        return new Response('success');
        exit;
    }

    public function paymentAction(Request $request)
    {
        $data = $request->request->all();
        $entity = new PettyCash();
        $em = $this->getDoctrine()->getManager();
        $entity->setGlobalOption($this->getUser()->getGlobalOption());
        $parent = $em->getRepository('AccountingBundle:PettyCash')->find($data['parent']);
        $returnAmount = ($parent->getReturnAmount() + $data['amount']);
        $parent->setReturnAmount($returnAmount);
        $entity->setParent($parent);
        $entity->setToUser($parent->getCreatedBy());
        $entity->setAmount($data['amount']);
        $entity->setRemark($data['remark']);
        if($data['transactionMethod'] == 2){
            $entity->setAccountBank($em->getRepository('AccountingBundle:AccountBank')->find($data['accountBank']));
            $entity->setTransactionMethod($em->getRepository('SettingToolBundle:TransactionMethod')->find($data['transactionMethod']));
        }elseif($data['transactionMethod'] == 3 ){
            $entity->setAccountBkash($em->getRepository('AccountingBundle:AccountBkash')->find($data['accountBkash']));
            $entity->setTransactionMethod($em->getRepository('SettingToolBundle:TransactionMethod')->find($data['transactionMethod']));
        }else{
            $entity->setTransactionMethod($em->getRepository('SettingToolBundle:TransactionMethod')->find(1));
        }
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->returnPettyCashTransaction($entity);
        return new Response('success');
        exit;

    }

    public function approveAction(PettyCash $pettyCash)
    {
        if (!empty($pettyCash)) {
            $em = $this->getDoctrine()->getManager();
            $pettyCash->setProcess('approved');
            $pettyCash->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertPettyCash($pettyCash);
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertPettyCashTransaction($pettyCash);

            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

}
