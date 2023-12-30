<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Form\CustomerType;
use Appstore\Bundle\AccountingBundle\Form\UserType;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;


/**
 * Customer controller.
 *
 */
class CustomerController extends Controller
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
        return $this->render('AccountingBundle:Customer:index.html.twig', array(
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
        return $this->redirect($this->generateUrl('account_customer'));
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
            return $this->redirect($this->generateUrl('account_customer'));
        }

        return $this->render('AccountingBundle:Customer:new.html.twig', array(
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
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new UserType($location), $entity, array(
            'action' => $this->generateUrl('account_customer_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
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
        return $this->render('AccountingBundle:Customer:new.html.twig', array(
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

        $entity = $em->getRepository('UserBundle:Profile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        return $this->render('AccountingBundle:Customer:show.html.twig', array(
            'entity'      => $entity,
        ));
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
        return $this->render('AccountingBundle:Customer:new.html.twig', array(
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
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new UserType($location), $entity, array(
            'action' => $this->generateUrl('account_customer_update', array('id' => $entity->getId())),
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
            return $this->redirect($this->generateUrl('account_customer'));
        }
        return $this->render('AccountingBundle:Customer:new.html.twig', array(
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
        return $this->redirect($this->generateUrl('account_customer'));
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

}
