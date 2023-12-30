<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\ItemAttribute;
use Appstore\Bundle\InventoryBundle\Form\ItemAttributeType;

/**
 * ItemAttribute controller.
 *
 */
class ItemAttributeController extends Controller
{

    /**
     * Lists all ItemAttribute entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('InventoryBundle:ItemAttribute')->findBy(array(),array('name'=>'ASC'));
        return $this->render('InventoryBundle:ItemAttribute:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ItemAttribute entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ItemAttribute();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('itemattribute'));
        }

        return $this->render('InventoryBundle:ItemAttribute:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ItemAttribute entity.
     *
     * @param ItemAttribute $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ItemAttribute $entity)
    {
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new ItemAttributeType($em), $entity, array(
            'action' => $this->generateUrl('itemattribute_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ItemAttribute entity.
     *
     */
    public function newAction()
    {
        $entity = new ItemAttribute();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:ItemAttribute:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ItemAttribute entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemAttribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemAttribute entity.');
        }
        return $this->render('InventoryBundle:ItemAttribute:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing ItemAttribute entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemAttribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemAttribute entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:ItemAttribute:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ItemAttribute entity.
    *
    * @param ItemAttribute $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ItemAttribute $entity)
    {
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new ItemAttributeType($em), $entity, array(
            'action' => $this->generateUrl('itemattribute_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing ItemAttribute entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemAttribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemAttribute entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('itemattribute_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:ItemAttribute:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a ItemAttribute entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InventoryBundle:ItemAttribute')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ItemAttribute entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('itemattribute'));
    }

    /**
     * Creates a form to delete a ItemAttribute entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('itemattribute_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Status a ItemAttribute entity.
     *
     */
    public function statusAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:ItemAttribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

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
        return $this->redirect($this->generateUrl('itemattribute'));
    }

}
