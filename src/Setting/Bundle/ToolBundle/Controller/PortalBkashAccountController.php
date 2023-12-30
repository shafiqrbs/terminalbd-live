<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\PortalBkashAccount;
use Setting\Bundle\ToolBundle\Form\PortalBkashAccountType;

/**
 * PortalBkashAccount controller.
 *
 */
class PortalBkashAccountController extends Controller
{

    /**
     * Lists all PortalBkashAccount entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:PortalBkashAccount')->findAll();

        return $this->render('SettingToolBundle:PortalBkashAccount:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new PortalBkashAccount entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new PortalBkashAccount();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $name = $entity->getAccountType().','.$entity->getMobile();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('bkash'));
        }

        return $this->render('SettingToolBundle:PortalBkashAccount:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PortalBkashAccount entity.
     *
     * @param PortalBkashAccount $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PortalBkashAccount $entity)
    {
        $form = $this->createForm(new PortalBkashAccountType(), $entity, array(
            'action' => $this->generateUrl('bkash_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new PortalBkashAccount entity.
     *
     */
    public function newAction()
    {
        $entity = new PortalBkashAccount();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:PortalBkashAccount:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PortalBkashAccount entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:PortalBkashAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PortalBkashAccount entity.');
        }
        return $this->render('SettingToolBundle:PortalBkashAccount:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing PortalBkashAccount entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:PortalBkashAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PortalBkashAccount entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('SettingToolBundle:PortalBkashAccount:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PortalBkashAccount entity.
    *
    * @param PortalBkashAccount $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PortalBkashAccount $entity)
    {
        $form = $this->createForm(new PortalBkashAccountType(), $entity, array(
            'action' => $this->generateUrl('bkash_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing PortalBkashAccount entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:PortalBkashAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PortalBkashAccount entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $name = $entity->getAccountType().','.$entity->getMobile();
            $entity->setName($name);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            return $this->redirect($this->generateUrl('bkash_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:PortalBkashAccount:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a PortalBkashAccount entity.
     *
     */
    public function deleteAction(PortalBkashAccount $entity)
    {

            $em = $this->getDoctrine()->getManager();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PortalBkashAccount entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );
            return $this->redirect($this->generateUrl('bkash'));
    }

    /**
     * Status a PortalBkashAccount entity.
     *
     */
    public function statusAction(PortalBkashAccount $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('bkash'));
    }



}
