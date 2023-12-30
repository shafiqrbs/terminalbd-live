<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessStore;
use Appstore\Bundle\BusinessBundle\Form\BusinessStoreLedgerType;
use Appstore\Bundle\BusinessBundle\Form\BusinessStoreType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * BusinessStore controller.
 *u
 */
class BusinessStoreController extends Controller
{

    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    /**
     * Lists all BusinessStore entities.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function indexAction()
    {
        $global = $this->getUser()->getGlobalOption();
        $config = $global->getBusinessConfig();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:BusinessStore')->invoiceLists($option,$data);
        $pagination = $this->paginate($entities);
        $areas = $this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->findBy(array('businessConfig' => $config,'status'=>1),array('name'=>"ASC"));
        $marketings = $this->getDoctrine()->getRepository('BusinessBundle:Marketing')->findBy(array('businessConfig' => $config,'status'=>1),array('name'=>"ASC"));
        $stores = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStore')->findBy(array('businessConfig' => $config,'status'=>1),array('name'=>"ASC"));
        $customers = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findBy(array('globalOption' => $global),array('name'=>"ASC"));
        return $this->render('BusinessBundle:BusinessStore:index.html.twig', array(
            'entities' => $pagination,
            'customers' => $customers,
            'marketings' => $marketings,
            'areas' => $areas,
            'stores' => $stores,
            'searchForm' => $data,
        ));
    }

    /**
     * Displays a form to create a new Vendor entity.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */

    public function newAction()
    {
        $entity = new BusinessStore();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $form   = $this->createCreateForm($entity);
        return $this->render('BusinessBundle:BusinessStore:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }




    /**
     * Creates a new BusinessStore entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = new BusinessStore();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entity->setBusinessConfig($option);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('business_store', array('id' => $entity->getId())));
        }
        return $this->render('BusinessBundle:BusinessStore:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BusinessStore entity.
     *
     * @param BusinessStore $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessStore $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new BusinessStoreType($option), $entity, array(
            'action' => $this->generateUrl('business_store_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Creates a form to create a BusinessStore entity.
     *
     * @param BusinessStore $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createLedgerForm(BusinessStore $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new BusinessStoreLedgerType($option), $entity, array(
            'action' => $this->generateUrl('business_store_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing BusinessStore entity.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $em->getRepository('BusinessBundle:BusinessStore')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessStore entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('BusinessBundle:BusinessStore:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a BusinessStore entity.
     *
     * @param BusinessStore $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessStore $entity )
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new BusinessStoreType($option), $entity, array(
            'action' => $this->generateUrl('business_store_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing BusinessStore entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $em->getRepository('BusinessBundle:BusinessStore')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessStore entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('business_store'));
        }

        return $this->render('BusinessBundle:BusinessStore:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a BusinessStore entity.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function deleteAction(BusinessStore $entity)
    {
        $em = $this->getDoctrine()->getManager();
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
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return $this->redirect($this->generateUrl('business_area'));
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStore')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('business_area'));
    }



}
