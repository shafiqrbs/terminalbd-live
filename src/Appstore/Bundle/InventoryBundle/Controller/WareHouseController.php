<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\WareHouse;
use Appstore\Bundle\InventoryBundle\Form\WareHouseType;

/**
 * WareHouse controller.
 *
 */
class WareHouseController extends Controller
{

    /**
     * Lists all WareHouse entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:WareHouse')->findBy(array('inventoryConfig'=>$inventoryConfig),array('updated'=>'desc'));

        return $this->render('InventoryBundle:WareHouse:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new WareHouse entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new WareHouse();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($inventoryConfig);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_warehouse_show', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:WareHouse:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a WareHouse entity.
     *
     * @param WareHouse $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(WareHouse $entity)
    {
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new WareHouseType($inventoryConfig), $entity, array(
            'action' => $this->generateUrl('inventory_warehouse_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form addPurchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new WareHouse entity.
     *
     */
    public function newAction()
    {
        $entity = new WareHouse();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:WareHouse:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'attr' => array(
                'class' => 'horizontal-form addPurchase',
                'novalidate' => 'novalidate',
            )
        ));
    }

    /**
     * Finds and displays a WareHouse entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:WareHouse')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WareHouse entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:WareHouse:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing WareHouse entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:WareHouse')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WareHouse entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:WareHouse:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a WareHouse entity.
    *
    * @param WareHouse $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(WareHouse $entity)
    {
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new WareHouseType($inventoryConfig), $entity, array(
            'action' => $this->generateUrl('inventory_warehouse_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing WareHouse entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:WareHouse')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WareHouse entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_warehouse_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:WareHouse:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a WareHouse entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InventoryBundle:WareHouse')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find WareHouse entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('inventory_warehouse'));
    }

    /**
     * Creates a form to delete a WareHouse entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventory_warehouse_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
