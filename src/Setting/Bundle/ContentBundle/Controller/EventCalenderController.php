<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\EventCalender;
use Setting\Bundle\ContentBundle\Form\EventCalenderType;

/**
 * EventCalender controller.
 *
 */
class EventCalenderController extends Controller
{

    /**
     * Lists all EventCalender entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingContentBundle:EventCalender')->findAll();

        return $this->render('SettingContentBundle:EventCalender:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new EventCalender entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new EventCalender();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('eventcalender_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingContentBundle:EventCalender:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a EventCalender entity.
     *
     * @param EventCalender $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EventCalender $entity)
    {
        $form = $this->createForm(new EventCalenderType(), $entity, array(
            'action' => $this->generateUrl('eventcalender_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new EventCalender entity.
     *
     */
    public function newAction()
    {
        $entity = new EventCalender();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:EventCalender:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a EventCalender entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:EventCalender')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventCalender entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:EventCalender:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing EventCalender entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:EventCalender')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventCalender entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:EventCalender:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a EventCalender entity.
    *
    * @param EventCalender $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EventCalender $entity)
    {
        $form = $this->createForm(new EventCalenderType(), $entity, array(
            'action' => $this->generateUrl('eventcalender_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing EventCalender entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:EventCalender')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventCalender entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('eventcalender_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:EventCalender:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a EventCalender entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingContentBundle:EventCalender')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EventCalender entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('eventcalender'));
    }

    /**
     * Creates a form to delete a EventCalender entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eventcalender_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
