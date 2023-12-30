<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Form\CustomerForDiagonesticType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalType;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Form\InvoicePatientType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer controller.
 *
 */
class CustomerHmsController extends Controller
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
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_OPERATOR")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Customer')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('DomainUserBundle:Patient:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }
    /**
     * Creates a new Customer entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Customer();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $invoice = $request->request->get('patientInvoice');
        if ($form->isValid()) {
            $globalOption  = $this->getUser()->getGlobalOption();
            $mobile = $entity->getMobile();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($mobile);
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $entity->setMobile($mobile);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Customer has been created successfully"
            );
            if($invoice){
                return $this->redirect($this->generateUrl('hms_customer'));
            }else{
                return $this->redirect($this->generateUrl('hms_invoice_new', array('patient' => $entity->getId())));
            }

        }

        return $this->render('DomainUserBundle:Patient:new.html.twig', array(
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
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new CustomerForDiagonesticType($location), $entity, array(
            'action' => $this->generateUrl('hms_customer_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_OPERATOR")
     */

    public function newAction()
    {
        $entity = new Customer();
        $form   = $this->createCreateForm($entity);
        return $this->render('DomainUserBundle:Patient:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Finds and displays a Customer entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        return $this->render('DomainUserBundle:Patient:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_OPERATOR")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('DomainUserBundle:Patient:new.html.twig', array(
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
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new CustomerForDiagonesticType($location), $entity, array(
            'action' => $this->generateUrl('hms_customer_update', array('id' => $entity->getId())),
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

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $mobile = $entity->getMobile();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($mobile);
            $entity->setMobile($mobile);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Customer has been updated successfully"
            );
            return $this->redirect($this->generateUrl('hms_customer'));
        }

        return $this->render('DomainUserBundle:Patient:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_OPERATOR")
     */

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=> $option,'id'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('customer'));
    }

    public function detailsAction(Request $request)
    {
        $customer = $request->request->get('customer');
        $invoice = $request->request->get('invoice');
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=> $option,'id' => $customer));
        if (!empty($entity)) {
            $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePatientInfo($invoice, $entity);
            $data = array(
                'status' => 'valid',
                'patient' => $entity->getId(),
                'customerId' => $entity->getCustomerId(),
                'mobile' => $entity->getMobile(),
                'name' => $entity->getName(),
                'gender' => $entity->getGender(),
                'age' => $entity->getAge(),
                'ageType' => $entity->getAgeType(),
                'location' => !empty($entity->getLocation())? $entity->getLocation()->getId():'',
                'address' => $entity->getAddress()
            );
            return new Response(json_encode($data));
        }else{
            return new Response(json_encode(array('status' => 'invalid')));
        }
        exit;
    }

}
