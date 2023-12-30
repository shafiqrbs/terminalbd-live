<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\HumanResourceBundle\Entity\LeavePolicy;
use Appstore\Bundle\HumanResourceBundle\Form\LeavePolicyType;

/**
 * LeavePolicy controller.
 *
 */
class LeavePolicyController extends Controller
{

    /**
     * Lists all LeavePolicy entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('HumanResourceBundle:LeavePolicy')->findAll();
        $entity = new LeavePolicy();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        return $this->render('HumanResourceBundle:LeavePolicy:index.html.twig', array(
            'entities' => $entities,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Creates a new LeavePolicy entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new LeavePolicy();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('leavepolicy'));
        }

        return $this->render('HumanResourceBundle:LeavePolicy:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a LeavePolicy entity.
     *
     * @param LeavePolicy $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(LeavePolicy $entity)
    {
        $form = $this->createForm(new LeavePolicyType(), $entity, array(
            'action' => $this->generateUrl('leavepolicy_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new LeavePolicy entity.
     *
     */
    public function newAction()
    {
        $entity = new LeavePolicy();
        $form   = $this->createCreateForm($entity);

        return $this->render('HumanResourceBundle:LeavePolicy:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a LeavePolicy entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HumanResourceBundle:LeavePolicy')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LeavePolicy entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('HumanResourceBundle:LeavePolicy:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing LeavePolicy entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HumanResourceBundle:LeavePolicy')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LeavePolicy entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('HumanResourceBundle:LeavePolicy:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )

        ));
    }

    /**
    * Creates a form to edit a LeavePolicy entity.
    *
    * @param LeavePolicy $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(LeavePolicy $entity)
    {
        $form = $this->createForm(new LeavePolicyType(), $entity, array(
            'action' => $this->generateUrl('leavepolicy_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing LeavePolicy entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HumanResourceBundle:LeavePolicy')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LeavePolicy entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('leavepolicy_edit', array('id' => $id)));
        }

        return $this->render('HumanResourceBundle:LeavePolicy:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView()
        ));
    }
    /**
     * Deletes a LeavePolicy entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('HumanResourceBundle:LeavePolicy')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find LeavePolicy entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('leavepolicy'));
    }

    /**
     * Creates a form to delete a LeavePolicy entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('leavepolicy_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
