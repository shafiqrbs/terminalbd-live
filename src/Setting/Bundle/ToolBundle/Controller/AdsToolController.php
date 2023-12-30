<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\AdsTool;
use Setting\Bundle\ToolBundle\Form\AdsToolType;

/**
 * AdsTool controller.
 *
 */
class AdsToolController extends Controller
{

    /**
     * Lists all AdsTool entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:AdsTool')->findAll();

        return $this->render('SettingToolBundle:AdsTool:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new AdsTool entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AdsTool();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('adstool_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingToolBundle:AdsTool:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AdsTool entity.
     *
     * @param AdsTool $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AdsTool $entity)
    {
        $form = $this->createForm(new AdsToolType(), $entity, array(
            'action' => $this->generateUrl('adstool_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new AdsTool entity.
     *
     */
    public function newAction()
    {
        $entity = new AdsTool();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:AdsTool:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AdsTool entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:AdsTool')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdsTool entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:AdsTool:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AdsTool entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:AdsTool')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdsTool entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:AdsTool:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AdsTool entity.
    *
    * @param AdsTool $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AdsTool $entity)
    {
        $form = $this->createForm(new AdsToolType(), $entity, array(
            'action' => $this->generateUrl('adstool_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing AdsTool entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:AdsTool')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdsTool entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('adstool_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:AdsTool:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a AdsTool entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingToolBundle:AdsTool')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdsTool entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('adstool'));
    }

    /**
     * Creates a form to delete a AdsTool entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('adstool_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
