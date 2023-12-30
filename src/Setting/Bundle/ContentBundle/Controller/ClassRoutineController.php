<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\ClassRoutine;
use Setting\Bundle\ContentBundle\Form\ClassRoutineType;

/**
 * ClassRoutine controller.
 *
 */
class ClassRoutineController extends Controller
{

    /**
     * Lists all ClassRoutine entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingContentBundle:ClassRoutine')->findAll();

        return $this->render('SettingContentBundle:ClassRoutine:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ClassRoutine entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ClassRoutine();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('classroutine_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingContentBundle:ClassRoutine:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ClassRoutine entity.
     *
     * @param ClassRoutine $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ClassRoutine $entity)
    {
        $form = $this->createForm(new ClassRoutineType(), $entity, array(
            'action' => $this->generateUrl('classroutine_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ClassRoutine entity.
     *
     */
    public function newAction()
    {
        $entity = new ClassRoutine();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:ClassRoutine:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ClassRoutine entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:ClassRoutine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ClassRoutine entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:ClassRoutine:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ClassRoutine entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:ClassRoutine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ClassRoutine entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:ClassRoutine:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ClassRoutine entity.
    *
    * @param ClassRoutine $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ClassRoutine $entity)
    {
        $form = $this->createForm(new ClassRoutineType(), $entity, array(
            'action' => $this->generateUrl('classroutine_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ClassRoutine entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:ClassRoutine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ClassRoutine entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('classroutine_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:ClassRoutine:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ClassRoutine entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingContentBundle:ClassRoutine')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ClassRoutine entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('classroutine'));
    }

    /**
     * Creates a form to delete a ClassRoutine entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('classroutine_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
