<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\SiteSetting;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Form\SiteSettingType;

/**
 * SiteSetting controller.
 *
 */
class SiteSettingController extends Controller
{

    /**
     * Lists all SiteSetting entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:SiteSetting')->findAll();
        $paginate = $this->paginate($entities);
        return $this->render('SettingToolBundle:SiteSetting:index.html.twig', array(
            'pagination' => $paginate,
        ));
    }


    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            30  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Creates a new SiteSetting entity.
     *
     */
    public function createAction(Request $request)
    {
        return $this->redirect($this->generateUrl('sitesetting'));
    }

    /**
     * Creates a form to create a SiteSetting entity.
     *
     * @param SiteSetting $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SiteSetting $entity)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('user' => $user));
        if($entities){
            $syndicateId = $entities->getSyndicate()->getId();
        }

        $form = $this->createForm(new SiteSettingType($syndicateId), $entity, array(
            'action' => $this->generateUrl('sitesetting_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }

    /**
     * Displays a form to create a new SiteSetting entity.
     *
     */
    public function newAction()
    {

        $entity = new SiteSetting();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:SiteSetting:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SiteSetting entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:SiteSetting')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteSetting entity.');

        }

        return $this->render('SettingToolBundle:SiteSetting:show.html.twig', array(
            'entity'      => $entity
        ));
    }

    /**
     * Displays a form to edit an existing SiteSetting entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:SiteSetting')->find($id);

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingToolBundle:SiteSetting:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a SiteSetting entity.
    *
    * @param SiteSetting $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SiteSetting $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('user' => $entity->getUser()));
        if($entities){
            $syndicateId = $entities->getSyndicate()->getId();
        }

        $form = $this->createForm(new SiteSettingType($syndicateId), $entity, array(
            'action' => $this->generateUrl('sitesetting_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }
    /**
     * Edits an existing SiteSetting entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:SiteSetting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteSetting entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
        }
        $this->getDoctrine()->getRepository('SettingToolBundle:SiteSetting')->updateSettingMenu($entity);

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }
    /**
     * Deletes a SiteSetting entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingToolBundle:SiteSetting')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SiteSetting entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('sitesetting'));
    }

    /**
     * Creates a form to delete a SiteSetting entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sitesetting_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Displays a form to edit an existing SiteSetting entity.
     *
     */
    public function modifyAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();

        $entity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('user'=>$user));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteSetting entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingToolBundle:SiteSetting:modify.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

}
