<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\InstituteLevel;
use Setting\Bundle\ToolBundle\Form\InstituteLevelType;

/**
 * InstituteLevel controller.
 *
 */
class InstituteLevelController extends Controller
{

    /**
     * Lists all InstituteLevel entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:InstituteLevel')->findAll();

        return $this->render('SettingToolBundle:InstituteLevel:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new InstituteLevel entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new InstituteLevel();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('institutelevel_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingToolBundle:InstituteLevel:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a InstituteLevel entity.
     *
     * @param InstituteLevel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(InstituteLevel $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel');

        $form = $this->createForm(new InstituteLevelType($em), $entity, array(
            'action' => $this->generateUrl('institutelevel_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }

    /**
     * Displays a form to create a new InstituteLevel entity.
     *
     */
    public function newAction()
    {
        $entity = new InstituteLevel();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:InstituteLevel:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a InstituteLevel entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:InstituteLevel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InstituteLevel entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:InstituteLevel:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to new an existing InstituteLevel entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:InstituteLevel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InstituteLevel entity.');
        }

        $newForm = $this->createnewForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:InstituteLevel:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $newForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to new a InstituteLevel entity.
    *
    * @param InstituteLevel $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createnewForm(InstituteLevel $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel');

        $form = $this->createForm(new InstituteLevelType($em), $entity, array(
            'action' => $this->generateUrl('institutelevel_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * news an existing InstituteLevel entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:InstituteLevel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InstituteLevel entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $newForm = $this->createnewForm($entity);
        $newForm->handleRequest($request);

        if ($newForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('institutelevel_new', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:InstituteLevel:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $newForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a InstituteLevel entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingToolBundle:InstituteLevel')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InstituteLevel entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('institutelevel'));
    }

    /**
     * Creates a form to delete a InstituteLevel entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('institutelevel_delete', array('id' => $id)))
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
        $entity = $em->getRepository('SettingToolBundle:InstituteLevel')->find($id);

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
        return $this->redirect($this->generateUrl('institutelevel'));
    }
}
