<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\WebTheme;
use Setting\Bundle\ToolBundle\Form\WebThemeType;

/**
 * WebTheme controller.
 *
 */
class WebThemeController extends Controller
{

    /**
     * Lists all WebTheme entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:WebTheme')->findAll();

        return $this->render('SettingToolBundle:WebTheme:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new WebTheme entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new WebTheme();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity ->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('webtheme_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingToolBundle:WebTheme:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a WebTheme entity.
     *
     * @param WebTheme $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(WebTheme $entity)
    {
            $form = $this->createForm(new WebThemeType(), $entity, array(
                'action' => $this->generateUrl('webtheme_create', array('id' => $entity->getId())),
                'method' => 'POST',
                'attr' => array(
                    'class' => 'horizontal-form',
                    'novalidate' => 'novalidate',
                )
            ));

        return $form;
    }

    /**
     * Displays a form to create a new WebTheme entity.
     *
     */
    public function newAction()
    {
        $entity = new WebTheme();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:WebTheme:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a WebTheme entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:WebTheme')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WebTheme entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:WebTheme:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing WebTheme entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:WebTheme')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WebTheme entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:WebTheme:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a WebTheme entity.
    *
    * @param WebTheme $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(WebTheme $entity)
    {
        $form = $this->createForm(new WebThemeType(), $entity, array(
            'action' => $this->generateUrl('webtheme_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }
    /**
     * Edits an existing WebTheme entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:WebTheme')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WebTheme entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity ->upload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('webtheme_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:WebTheme:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a WebTheme entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingToolBundle:WebTheme')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find WebTheme entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('webtheme'));
    }

    /**
     * Creates a form to delete a WebTheme entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('webtheme_delete', array('id' => $id)))
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
        $entity = $em->getRepository('SettingToolBundle:WebTheme')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('webtheme'));
    }

}
