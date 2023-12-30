<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\Marketing;
use Appstore\Bundle\BusinessBundle\Form\MarketingType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Marketing controller.
 *u
 */
class MarketingController extends Controller
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
    public function ledgerAction()
    {

        $data = $_REQUEST;
        $global = $this->getUser()->getGlobalOption();
        $config = $global->getBusinessConfig();
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->srLedger($option,$data);
        $pagination = $this->paginate($entities);
        $marketings = $this->getDoctrine()->getRepository('BusinessBundle:Marketing')->findBy(array('businessConfig' => $config,'status'=>1),array('name'=>"ASC"));
        return $this->render('BusinessBundle:Marketing:ledger.html.twig', array(
            'entities' => $pagination,
            'marketings' => $marketings,
            'searchForm' => $data,
        ));
    }

    /**
     * Lists all Marketing entities.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function indexAction()
    {

        $entity = new Marketing();
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:Marketing')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));
        $form   = $this->createCreateForm($entity);
        return $this->render('BusinessBundle:Marketing:index.html.twig', array(
            'entity' => $entity,
            'entities' => $entities,
            'form'   => $form->createView(),
        ));
    }

    public function updateSalesTargetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        foreach ($data['user'] as $key => $value){
            $user = $this->getDoctrine()->getRepository(Marketing::class)->find($value);
            $user->setMonthlySales($data['monthlySales'][$key]);
            $user->setYearlySales($data['yearlySales'][$key]);
            $user->setDiscount($data['discount'][$key]);
            $em->persist($user);
        }
        $em->flush();
        return $this->redirect($request->headers->get('referer'));
    }




    /**
     * Creates a new Marketing entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:Marketing')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));

        $entity = new Marketing();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setBusinessConfig($option);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('business_marketing'));
        }
        return $this->render('BusinessBundle:Marketing:index.html.twig', array(
            'entity' => $entity,
            'entities'      => $entities,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Marketing entity.
     *
     * @param Marketing $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Marketing $entity)
    {
        $form = $this->createForm(new MarketingType(), $entity, array(
            'action' => $this->generateUrl('business_marketing_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing Marketing entity.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:Marketing')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));

        $entity = $em->getRepository('BusinessBundle:Marketing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Marketing entity.');
        }
        $editForm = $this->createEditForm($entity);

        return $this->render('BusinessBundle:Marketing:index.html.twig', array(
            'entity'      => $entity,
            'entities'      => $entities,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Marketing entity.
     *
     * @param Marketing $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Marketing $entity )
    {
        $form = $this->createForm(new MarketingType(), $entity, array(
            'action' => $this->generateUrl('business_marketing_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing Marketing entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:Marketing')->findBy(array('businessConfig' => $option),array( 'name' =>'asc' ));
        $entity = $em->getRepository('BusinessBundle:Marketing')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Marketing entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('business_marketing'));
        }

        return $this->render('BusinessBundle:Marketing:index.html.twig', array(
            'entity'      => $entity,
            'entities'      => $entities,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Marketing entity.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function deleteAction(Marketing $entity)
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
        return $this->redirect($this->generateUrl('business_marketing'));
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:Marketing')->find($id);

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
        return $this->redirect($this->generateUrl('business_marketing'));
    }



}
