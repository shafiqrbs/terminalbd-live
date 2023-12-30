<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Form\UserType;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Customer controller.
 *
 */
class UserController extends Controller
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
     * @Secure(roles="ROLE_ACCOUNTING,ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $data['userGroup'] = 'account';
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('UserBundle:User')->getEmployees($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:User:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function restoreCustomerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        /* @var $entity User */
        foreach ($entities as $entity){
            $mobile = $entity->getProfile()->getMobile();
            $exist = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(['globalOption'=>$globalOption,'mobile'=>$mobile]);
            if(empty($exist)){
                $customer = new Customer();
                $customer->setGlobalOption($globalOption);
                $customer->setCustomerType('account');
                $customer->setName($entity->getProfile()->getName());
                $customer->setMobile($entity->getProfile()->getMobile());
                $customer->setEmail($entity->getEmail());
                $customer->setLocation($entity->getProfile()->getLocation());
                $customer->setAddress($entity->getProfile()->getAddress());
                $em->persist($customer);
                $em->flush();
            }

        }
        return $this->redirect($this->generateUrl('account_user'));
    }

    /**
     * Creates a new Customer entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Profile();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $globalOption = $this->getUser()->getGlobalOption();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
	        $entity->setMobile($mobile);
	        $user = $this->getDoctrine()->getRepository('UserBundle:User')->insertAccountUser($globalOption,$mobile,$data);
	        $entity->setUser($user);
            $em->persist($entity);
            $em->flush();
            if($entity->getUserGroup() != 'other'){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->insertUserAccount($entity);
            }
            return $this->redirect($this->generateUrl('account_user'));
        }

        return $this->render('AccountingBundle:User:new.html.twig', array(
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
    private function createCreateForm(Profile $entity)
    {

        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('account_user_create'),
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
        $entity = new Profile();
        $form   = $this->createCreateForm($entity);
        return $this->render('AccountingBundle:User:new.html.twig', array(
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

    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */
    public function editAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $user->getProfile();
     //   $entity = $em->getRepository('UserBundle:Profile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:User:new.html.twig', array(
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
    private function createEditForm(Profile $entity)
    {

        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('account_user_update', array('id' => $entity->getId())),
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

        $entity = $em->getRepository('UserBundle:Profile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
	        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
	        $entity->setMobile($mobile);
            $em->flush();
            if($entity->getUserGroup() != 'other'){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->insertUserAccount($entity);
            }
            return $this->redirect($this->generateUrl('account_user'));
        }

        return $this->render('AccountingBundle:User:new.html.twig', array(
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
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('UserBundle:Profile')->findOneBy(array('globalOption'=>$globalOption,'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }
        try {
            $entity->setIsDelete(true);
            $entity->setEnabled(false);
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }
        return $this->redirect($this->generateUrl('account_user'));
    }


    public function changeUserModeAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        if($user->getUserGroup() == "account" ){
            $user->setUserGroup('user');
            $user->setEnabled(true);
        }else{
            $user->setUserGroup('account');
        }
        $em->persist($user);
        $em->flush();
        return $this->redirect($this->generateUrl('account_user'));
    }





}
