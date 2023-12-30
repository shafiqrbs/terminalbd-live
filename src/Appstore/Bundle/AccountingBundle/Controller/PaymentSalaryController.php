<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Core\UserBundle\Entity\User;
use Core\UserBundle\UserBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\AccountingBundle\Entity\PaymentSalary;
use Appstore\Bundle\AccountingBundle\Form\PaymentSalaryType;

/**
 * PaymentSalary controller.
 *
 */
class PaymentSalaryController extends Controller
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
     * Lists all DomainUser entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        if(!empty($data['toUser'])){
            $data['toUser'] = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('username'=> $data['toUser']));
        }
        $entities = $em->getRepository('AccountingBundle:PaymentSalary')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:PaymentSalary')->paymentAmountOverview($globalOption,$data);
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'ASC'));
        return $this->render('AccountingBundle:PaymentSalary:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'transactionMethods' => $transactionMethods,
            'overview' => $overview
        ));

    }

    /**
     * Lists all DomainUser entities.
     *
     */
    public function salaryEmployeeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entities = $user->getGlobalOption()->getUsers();
        return $this->render('AccountingBundle:PaymentSalary:employee.html.twig', array(
            'entities' => $entities,
        ));
    }



    /**
     * Creates a new PaymentSalary entity.
     *
     */
    public function createAction(Request $request , User $user)
    {
        $entity = new PaymentSalary();
        $form = $this->createCreateForm($entity,$user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setUser($user);
            $entity->setGlobalOption($user->getGlobalOption());
            if($entity->getTotalAmount() > $entity->getPaidAmount() ){
                $entity->setDueAmount($entity->getTotalAmount() + $entity->getAdjustmentAmount() - $entity->getPaidAmount());
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('account_paymentsalary_show', array('user' => $user->getId())));
        }
        $totalAmount = $this->getDoctrine()->getRepository('AccountingBundle:PaymentSalary')->totalAmount($user);
        return $this->render('AccountingBundle:PaymentSalary:new.html.twig', array(
            'entity' => $entity,
            'user'      => $user,
            'totalAmount'      => $totalAmount,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PaymentSalary entity.
     *
     * @param PaymentSalary $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PaymentSalary $entity,$user)
    {

        $form = $this->createForm(new PaymentSalaryType($user), $entity, array(
            'action' => $this->generateUrl('account_paymentsalary_create',array('user' => $user->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new PaymentSalary entity.
     *
     */
    public function newAction(User $user)
    {
        $entity = new PaymentSalary();
        $form   = $this->createCreateForm($entity,$user);
        $globalOption = $user->getGlobalOption();
        $data = array('toUser' => $user);
        $invoiceAmountOverview = $this->getDoctrine()->getRepository('AccountingBundle:SalarySetting')->invoiceAmountOverview($globalOption,$data);
        $paymentAmountOverview = $this->getDoctrine()->getRepository('AccountingBundle:PaymentSalary')->paymentAmountOverview($globalOption,$data);
        return $this->render('AccountingBundle:PaymentSalary:new.html.twig', array(
            'user'                          => $user,
            'entity'                        => $entity,
            'invoiceAmountOverview'         => $invoiceAmountOverview,
            'paymentAmountOverview'         => $paymentAmountOverview,
            'form'                          => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PaymentSalary entity.
     *
     */
    public function showAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $user->getGlobalOption();
        $data = array('toUser' => $user);
        $invoiceAmountOverview = $this->getDoctrine()->getRepository('AccountingBundle:SalarySetting')->invoiceAmountOverview($globalOption,$data);
        $paymentAmountOverview = $this->getDoctrine()->getRepository('AccountingBundle:PaymentSalary')->paymentAmountOverview($globalOption,$data);
        return $this->render('AccountingBundle:PaymentSalary:show.html.twig', array(
            'entity'      => $user,
            'invoiceAmountOverview'      => $invoiceAmountOverview,
            'paymentAmountOverview'      => $paymentAmountOverview,
        ));
    }

    /**
     * Deletes a SalesItem entity.
     *
     */
    public function deleteAction(PaymentSalary $paymentSalary)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($paymentSalary);
        $em->flush();
        return new \Symfony\Component\HttpFoundation\Response('success');
    }


    public function approveAction(PaymentSalary $entity)
    {

        if (!empty($entity)) {

            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:SalarySetting')->invoiceUpdate($entity);
            $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertSalaryCash($entity);
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertSalaryTransaction($entity);
            return new \Symfony\Component\HttpFoundation\Response('success');
        } else {
            return new \Symfony\Component\HttpFoundation\Response('failed');
        }
        exit;
    }

}
