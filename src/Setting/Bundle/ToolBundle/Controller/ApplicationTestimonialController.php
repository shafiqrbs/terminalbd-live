<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\ApplicationTestimonial;
use Setting\Bundle\ToolBundle\Form\ApplicationTestimonialType;

/**
 * ApplicationTestimonial controller.
 *
 */
class ApplicationTestimonialController extends Controller
{

    /**
     * Lists all ApplicationTestimonial entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:ApplicationTestimonial')->findAll();

        return $this->render('SettingToolBundle:ApplicationTestimonial:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ApplicationTestimonial entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ApplicationTestimonial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('applicationtestimonial_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingToolBundle:ApplicationTestimonial:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ApplicationTestimonial entity.
     *
     * @param ApplicationTestimonial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ApplicationTestimonial $entity)
    {
        $form = $this->createForm(new ApplicationTestimonialType(), $entity, array(
            'action' => $this->generateUrl('applicationtestimonial_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ApplicationTestimonial entity.
     *
     */
    public function newAction()
    {
        $entity = new ApplicationTestimonial();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:ApplicationTestimonial:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ApplicationTestimonial entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:ApplicationTestimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ApplicationTestimonial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:ApplicationTestimonial:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ApplicationTestimonial entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:ApplicationTestimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ApplicationTestimonial entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:ApplicationTestimonial:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ApplicationTestimonial entity.
    *
    * @param ApplicationTestimonial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ApplicationTestimonial $entity)
    {
        $form = $this->createForm(new ApplicationTestimonialType(), $entity, array(
            'action' => $this->generateUrl('applicationtestimonial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
       return $form;
    }
    /**
     * Edits an existing ApplicationTestimonial entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:ApplicationTestimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ApplicationTestimonial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->upload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('applicationtestimonial_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:ApplicationTestimonial:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ApplicationTestimonial entity.
     *
     */
    public function deleteAction(ApplicationTestimonial $entity)
    {
            $em = $this->getDoctrine()->getManager();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ApplicationTestimonial entity.');
            }
            $entity->removeUpload();
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );
            return $this->redirect($this->generateUrl('theme'));
    }

    /**
     * Creates a form to delete a ApplicationTestimonial entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('applicationtestimonial_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //$data = $request->request->all();


        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:ApplicationTestimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('theme'));
    }

}
