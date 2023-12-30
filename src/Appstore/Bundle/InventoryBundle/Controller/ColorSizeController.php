<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\ColorSize;
use Appstore\Bundle\InventoryBundle\Form\ColorSizeType;

/**
 * ColorSize controller.
 *
 */
class ColorSizeController extends Controller
{

    /**
     * Lists all ColorSize entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('InventoryBundle:ColorSize')->findBy(array(),array('type'=>'asc'));

        return $this->render('InventoryBundle:ColorSize:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ColorSize entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ColorSize();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $lastCode = $em->getRepository('InventoryBundle:ColorSize')->getLastId($inventory,$entity->getType());
            $code= str_pad($lastCode, 2 , "0", STR_PAD_LEFT);
            $entity->setCode($code);
            $entity->setInventoryConfig($inventory);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('colorsize_show', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:ColorSize:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ColorSize entity.
     *
     * @param ColorSize $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ColorSize $entity)
    {
        $form = $this->createForm(new ColorSizeType(), $entity, array(
            'action' => $this->generateUrl('colorsize_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ColorSize entity.
     *
     */
    public function newAction()
    {
        $entity = new ColorSize();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:ColorSize:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ColorSize entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ColorSize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ColorSize entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:ColorSize:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ColorSize entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ColorSize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ColorSize entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:ColorSize:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ColorSize entity.
    *
    * @param ColorSize $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ColorSize $entity)
    {
        $form = $this->createForm(new ColorSizeType(), $entity, array(
            'action' => $this->generateUrl('colorsize_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing ColorSize entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ColorSize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ColorSize entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('colorsize_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:ColorSize:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ColorSize entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InventoryBundle:ColorSize')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ColorSize entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('colorsize'));
    }

    /**
     * Creates a form to delete a ColorSize entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('colorsize_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
