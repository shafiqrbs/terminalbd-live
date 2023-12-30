<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\Admission;
use Setting\Bundle\ContentBundle\Form\AdmissionType;

/**
 * Admission controller.
 *
 */
class AdmissionController extends Controller
{


    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            20  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all Admission entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $entities = $em->getRepository('SettingContentBundle:Admission')->findBy(array('user'=> $user),array('name' => 'asc'));
        $entities = $this->paginate($entities);
        return $this->render('SettingContentBundle:Admission:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Admission entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Admission();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $user = $this->get('security.context')->getToken()->getUser();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity ->setUser($user);
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admission_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingContentBundle:Admission:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Admission entity.
     *
     * @param Admission $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Admission $entity)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $modules = $user->getSiteSetting()->getModules();
        $branch ="";
        if(!empty($modules)){
            foreach ($modules as $module):
                if($module->getMenuSlug() == 'branches'){
                    $branch ='branch';
                }else{
                    $branch ='';
                }
            endforeach;
        }
        $form = $this->createForm(new AdmissionType($user->getId(),$branch='branch'), $entity, array(
            'action' => $this->generateUrl('admission_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Admission entity.
     *
     */
    public function newAction()
    {
        $entity = new Admission();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:Admission:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Admission entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Admission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Admission entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:Admission:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Admission entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Admission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Admission entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:Admission:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Admission entity.
    *
    * @param Admission $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Admission $entity)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $modules = $user->getSiteSetting()->getModules();
        $branch ="";
        if(!empty($modules)){
            foreach ($modules as $module):
                if($module->getMenuSlug() == 'branches'){
                    $branch ='branch';
                }else{
                    $branch ='';
                }
            endforeach;
        }
        $form = $this->createForm(new AdmissionType($user->getId(),$branch='branch'), $entity, array(
            'action' => $this->generateUrl('admission_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }
    /**
     * Edits an existing Admission entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Admission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Admission entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('admission_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:Admission:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Admission entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingContentBundle:Admission')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Admission entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admission'));
    }

    /**
     * Creates a form to delete a Admission entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admission_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Status a news entity.
     *
     */
    public function statusAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:Admission')->find($id);

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
        return $this->redirect($this->generateUrl('admission'));
    }

}
