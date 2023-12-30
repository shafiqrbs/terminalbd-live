<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountLoanUser;
use Appstore\Bundle\AccountingBundle\Form\AccountLoanUserType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountLoan controller.
 *
 */
class AccountLoanUserController extends Controller
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
     * Lists all AccountLoan entities.
     *
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $option = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:AccountLoanUser')->findBy(array('globalOption'=>$option),array('name'=>'ASC'));
        return $this->render('AccountingBundle:AccountLoan:index-user.html.twig', array(
            'entities' => $entities,
            'data' => $data,
        ));

    }

    /**
     * Creates a new AccountLoan entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountLoanUser();
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
            $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->insertLoanAccount($entity);
            return $this->redirect($this->generateUrl('account_loanuser'));
        }
        return $this->render('AccountingBundle:AccountLoan:user.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountLoan entity.
     *
     * @param AccountLoanUser $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountLoanUser $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountLoanUserType($option), $entity, array(
            'action' => $this->generateUrl('account_loanuser_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new AccountLoan entity.
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountLoanUser();
        $form   = $this->createCreateForm($entity);
        return $this->render('AccountingBundle:AccountLoan:user.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Deletes a Expenditure entity.
     *
     */
    public function deleteAction(AccountLoanUser $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountLoanUser entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
    }

    /**
     * Creates a form to edit a Customer entity.
     *
     * @param Customer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AccountLoanUser $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountLoanUserType($option), $entity, array(
            'action' => $this->generateUrl('account_loanuser_update', array('id' => $entity->getId())),
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
        $entity = $em->getRepository('AccountingBundle:AccountLoanUser')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
            $entity->setMobile($mobile);
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->insertLoanAccount($entity);
            return $this->redirect($this->generateUrl('account_loanuser'));
        }
        return $this->render('AccountingBundle:AccountLoan:index-user.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }






}
