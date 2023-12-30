<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\AdmissionComment;
use Setting\Bundle\ContentBundle\Form\AdmissionCommentType;

/**
 * AdmissionComment controller.
 *
 */
class AdmissionCommentController extends Controller
{

    /**
     * Lists all AdmissionComment entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingContentBundle:AdmissionComment')->findAll();

        return $this->render('SettingContentBundle:AdmissionComment:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new AdmissionComment entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AdmissionComment();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admissioncomment_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingContentBundle:AdmissionComment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AdmissionComment entity.
     *
     * @param AdmissionComment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AdmissionComment $entity)
    {
        $form = $this->createForm(new AdmissionCommentType(), $entity, array(
            'action' => $this->generateUrl('admissioncomment_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new AdmissionComment entity.
     *
     */
    public function newAction()
    {
        $entity = new AdmissionComment();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:AdmissionComment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AdmissionComment entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:AdmissionComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmissionComment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:AdmissionComment:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AdmissionComment entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:AdmissionComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmissionComment entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:AdmissionComment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AdmissionComment entity.
    *
    * @param AdmissionComment $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AdmissionComment $entity)
    {
        $form = $this->createForm(new AdmissionCommentType(), $entity, array(
            'action' => $this->generateUrl('admissioncomment_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing AdmissionComment entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:AdmissionComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmissionComment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admissioncomment_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:AdmissionComment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a AdmissionComment entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingContentBundle:AdmissionComment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmissionComment entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admissioncomment'));
    }

    /**
     * Creates a form to delete a AdmissionComment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admissioncomment_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
