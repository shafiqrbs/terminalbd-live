<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\FooterSetting;
use Setting\Bundle\ToolBundle\Form\FooterSettingType;

/**
 * FooterSetting controller.
 *
 */
class FooterSettingController extends Controller
{

    /**
     * Lists all FooterSetting entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:FooterSetting')->findAll();

        return $this->render('SettingToolBundle:FooterSetting:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new FooterSetting entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new FooterSetting();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('footersetting_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingToolBundle:FooterSetting:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a FooterSetting entity.
     *
     * @param FooterSetting $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FooterSetting $entity)
    {
        $form = $this->createForm(new FooterSettingType(), $entity, array(
            'action' => $this->generateUrl('footersetting_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new FooterSetting entity.
     *
     */
    public function newAction()
    {
        $entity = new FooterSetting();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:FooterSetting:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a FooterSetting entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:FooterSetting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FooterSetting entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:FooterSetting:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing FooterSetting entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:FooterSetting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FooterSetting entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:FooterSetting:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a FooterSetting entity.
    *
    * @param FooterSetting $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(FooterSetting $entity)
    {
        $form = $this->createForm(new FooterSettingType(), $entity, array(
            'action' => $this->generateUrl('footersetting_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing FooterSetting entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:FooterSetting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FooterSetting entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('footersetting_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:FooterSetting:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a FooterSetting entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingToolBundle:FooterSetting')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FooterSetting entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('footersetting'));
    }

    /**
     * Creates a form to delete a FooterSetting entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('footersetting_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
