<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountOnlineOrder;
use Appstore\Bundle\AccountingBundle\Form\AccountOnlineOrderType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountOnlineOrder controller.
 *
 */
class AccountOnlineOrderController extends Controller
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
     * Lists all AccountOnlineOrder entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:AccountOnlineOrder')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountOnlineOrder')->onlineOrderOverview($globalOption,$data);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent =array(20,29));
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('AccountingBundle:AccountOnlineOrder:index.html.twig', array(
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }

    /**
     * Lists all AccountOnlineOrderReturn entities.
     *
     */
    public function salesReturnAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:AccountOnlineOrderReturn')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountOnlineOrderReturn')->salesOverview($globalOption,$data);
        return $this->render('AccountingBundle:AccountOnlineOrder:salesReturn.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }
    /**
     * Creates a new AccountOnlineOrder entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountOnlineOrder();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $lastBalance = $em->getRepository('AccountingBundle:AccountOnlineOrder')->lastInsertOnlineOrder($this->getUser()->getGlobalOption(),$entity);
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption( $this->getUser()->getGlobalOption());
            $entity->setProcessHead('Account Online');
            $entity->setBalance($lastBalance - $entity->getAmount());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_onlineorder'));
        }

        return $this->render('AccountingBundle:AccountOnlineOrder:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountOnlineOrder entity.
     *
     * @param AccountOnlineOrder $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountOnlineOrder $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountOnlineOrderType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_onlineorder_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new AccountOnlineOrder entity.
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountOnlineOrder();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:AccountOnlineOrder:new.html.twig', array(
            'entity' => $entity,
            'banks' => $banks,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountOnlineOrder entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountOnlineOrder')->find($id);
       if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountOnlineOrder entity.');
        }

        return $this->render('AccountingBundle:AccountOnlineOrder:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing AccountOnlineOrder entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountOnlineOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountOnlineOrder entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountOnlineOrder:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AccountOnlineOrder entity.
    *
    * @param AccountOnlineOrder $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountOnlineOrder $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountOnlineOrderType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_onlineorder_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing AccountOnlineOrder entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountOnlineOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountOnlineOrder entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('account_onlineorder_edit', array('id' => $id)));
        }

        return $this->render('AccountingBundle:AccountOnlineOrder:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
        $entity = $em->getRepository('AccountingBundle:AccountOnlineOrder')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        $lastBalance = $em->getRepository('AccountingBundle:AccountOnlineOrder')->lastInsertSales($this->getUser()->getGlobalOption(),$entity);
        $entity->setAmount($data['value']);
        $entity->setBalance($lastBalance - floatval($data['value']));
        $em->flush();
        exit;
    }

    public function approveAction(AccountOnlineOrder $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            // $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountOnlineOrderTransaction($entity);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

    /**
     * Deletes a Expenditure entity.
     *
     */
    public function deleteAction(AccountOnlineOrder $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }

}
