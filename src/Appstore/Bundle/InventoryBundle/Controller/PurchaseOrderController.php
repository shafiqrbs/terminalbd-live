<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\PurchaseOrder;
use Appstore\Bundle\InventoryBundle\Form\PurchaseOrderType;

/**
 * PurchaseOrder controller.
 *
 */
class PurchaseOrderController extends Controller
{

    /**
     * Lists all PurchaseOrder entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('InventoryBundle:PurchaseOrder')->findAll();

        return $this->render('InventoryBundle:PurchaseOrder:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new PurchaseOrder entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new PurchaseOrder();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->get('security.context')->getToken()->getUser();
            $inventoryConfig = $user->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($inventoryConfig);
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_purchaseitem_new', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:PurchaseOrder:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PurchaseOrder entity.
     *
     * @param PurchaseOrder $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PurchaseOrder $entity)
    {
        $inventoryConfig =  $user = $this->get('security.context')->getToken()->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PurchaseOrderType($inventoryConfig), $entity, array(
            'action' => $this->generateUrl('purchaseorder_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
       return $form;
    }

    /**
     * Displays a form to create a new PurchaseOrder entity.
     *
     */
    public function newAction()
    {
        $entity = new PurchaseOrder();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:PurchaseOrder:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PurchaseOrder entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseOrder entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:PurchaseOrder:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PurchaseOrder entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseOrder entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:PurchaseOrder:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PurchaseOrder entity.
    *
    * @param PurchaseOrder $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PurchaseOrder $entity)
    {
        $inventoryConfig =  $user = $this->get('security.context')->getToken()->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PurchaseOrderType($inventoryConfig), $entity, array(
            'action' => $this->generateUrl('purchaseorder_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing PurchaseOrder entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseOrder entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('purchaseorder_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:PurchaseOrder:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a PurchaseOrder entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InventoryBundle:PurchaseOrder')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PurchaseOrder entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('purchaseorder'));
    }

    /**
     * Creates a form to delete a PurchaseOrder entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('purchaseorder_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
