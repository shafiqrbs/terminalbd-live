<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\AppModule;
use Setting\Bundle\ToolBundle\Form\AppModuleType;

/**
 * AppModule controller.
 *
 */
class AppModuleController extends Controller
{

    /**
     * Lists all AppModule entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:AppModule')->findAll(array(),array('name'=>'ASC'));
        return $this->render('SettingToolBundle:AppModule:index.html.twig', array(
            'pagination' => $entities
        ));
    }



    /**
     * Creates a new AppModule entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AppModule();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('appmodule_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingToolBundle:AppModule:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AppModule entity.
     *
     * @param AppModule $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AppModule $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AppModuleType($globalOption), $entity, array(
            'action' => $this->generateUrl('appmodule_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new AppModule entity.
     *
     */
    public function newAction()
    {
        $entity = new AppModule();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:AppModule:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AppModule entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:AppModule')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AppModule entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:AppModule:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AppModule entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:AppModule')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AppModule entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:AppModule:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AppModule entity.
    *
    * @param AppModule $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AppModule $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AppModuleType($globalOption), $entity, array(
            'action' => $this->generateUrl('appmodule_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }
    /**
     * Edits an existing AppModule entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:AppModule')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AppModule entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->upload();
        	$em->flush();

            return $this->redirect($this->generateUrl('appmodule_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:AppModule:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a AppModule entity.
     *
     */
    public function deleteAction(AppModule $entity)
    {
            $em = $this->getDoctrine()->getManager();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AppModule entity.');
            }

            $em->remove($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('appmodule'));
    }

    /**
     * Creates a form to delete a AppModule entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('appmodule_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Status a Module entity.
     *
     */
    public function statusAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:Module')->find($id);

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
            'error',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('appmodule'));
    }



}
