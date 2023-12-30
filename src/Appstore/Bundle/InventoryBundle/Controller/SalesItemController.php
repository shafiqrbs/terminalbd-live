<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\InventoryBundle\Form\SalesItemType;

/**
 * SalesItem controller.
 *
 */
class SalesItemController extends Controller
{

    /**
     * Lists all SalesItem entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('InventoryBundle:SalesItem')->findAll();

        return $this->render('InventoryBundle:SalesItem:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new SalesItem entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new SalesItem();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('salesitem_show', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:SalesItem:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a SalesItem entity.
     *
     * @param SalesItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SalesItem $entity)
    {
        $form = $this->createForm(new SalesItemType(), $entity, array(
            'action' => $this->generateUrl('salesitem_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new SalesItem entity.
     *
     */
    public function newAction()
    {
        $entity = new SalesItem();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:SalesItem:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SalesItem entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:SalesItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:SalesItem:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing SalesItem entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:SalesItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:SalesItem:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a SalesItem entity.
    *
    * @param SalesItem $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SalesItem $entity)
    {
        $form = $this->createForm(new SalesItemType(), $entity, array(
            'action' => $this->generateUrl('salesitem_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing SalesItem entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:SalesItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('salesitem_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:SalesItem:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a SalesItem entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InventoryBundle:SalesItem')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SalesItem entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('salesitem'));
    }

    /**
     * Creates a form to delete a SalesItem entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('salesitem_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
