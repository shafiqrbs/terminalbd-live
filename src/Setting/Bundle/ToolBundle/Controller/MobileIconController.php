<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\MobileIcon;
use Setting\Bundle\ToolBundle\Form\MobileIconType;

/**
 * MobileIcon controller.
 *
 */
class MobileIconController extends Controller
{

    /**
     * Lists all MobileIcon entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:MobileIcon')->findAll();

        return $this->render('SettingToolBundle:MobileIcon:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new MobileIcon entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MobileIcon();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mobileicon_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingToolBundle:MobileIcon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MobileIcon entity.
     *
     * @param MobileIcon $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MobileIcon $entity)
    {
        $form = $this->createForm(new MobileIconType(), $entity, array(
            'action' => $this->generateUrl('mobileicon_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new MobileIcon entity.
     *
     */
    public function newAction()
    {
        $entity = new MobileIcon();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:MobileIcon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MobileIcon entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:MobileIcon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MobileIcon entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:MobileIcon:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MobileIcon entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:MobileIcon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MobileIcon entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:MobileIcon:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a MobileIcon entity.
    *
    * @param MobileIcon $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MobileIcon $entity)
    {
        $form = $this->createForm(new MobileIconType(), $entity, array(
            'action' => $this->generateUrl('mobileicon_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing MobileIcon entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:MobileIcon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MobileIcon entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('mobileicon_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:MobileIcon:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a MobileIcon entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingToolBundle:MobileIcon')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MobileIcon entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mobileicon'));
    }

    /**
     * Creates a form to delete a MobileIcon entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mobileicon_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
