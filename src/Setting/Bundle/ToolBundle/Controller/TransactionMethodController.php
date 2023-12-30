<?php

namespace Setting\Bundle\ToolBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\TransactionMethod;
use Setting\Bundle\ToolBundle\Form\TransactionMethodType;


/**
 * TransactionMethod controller.
 *
 */
class TransactionMethodController extends Controller
{

    /**
     * Creates a new TransactionMethod entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TransactionMethod();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('transactionmethod_new'));
        }
        $entities = $em->getRepository('SettingToolBundle:TransactionMethod')->findAll(array(),array('name'=>'asc'));
        return $this->render('SettingToolBundle:TransactionMethod:new.html.twig', array(
            'entity' => $entity,
            'pagination' => $entities,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TransactionMethod entity.
     *
     * @param TransactionMethod $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TransactionMethod $entity)
    {
        $form = $this->createForm(new TransactionMethodType(), $entity, array(
            'action' => $this->generateUrl('transactionmethod_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }

    /**
     * Displays a form to create a new TransactionMethod entity.
     *
     */
    public function newAction()
    {
        $entity = new TransactionMethod();
        $em = $this->getDoctrine()->getManager();
        $form   = $this->createCreateForm($entity);
        $entities = $em->getRepository('SettingToolBundle:TransactionMethod')->findAll(array(),array('name'=>'asc'));
        return $this->render('SettingToolBundle:TransactionMethod:new.html.twig', array(
            'entity' => $entity,
            'pagination' => $entities,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing TransactionMethod entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:TransactionMethod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TransactionMethod entity.');
        }

        $editForm = $this->createEditForm($entity);
        $entities = $em->getRepository('SettingToolBundle:TransactionMethod')->findAll(array(),array('name'=>'asc'));
        return $this->render('SettingToolBundle:TransactionMethod:new.html.twig', array(

            'entity'      => $entity,
            'pagination'      => $entities,
            'form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a TransactionMethod entity.
    *
    * @param TransactionMethod $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TransactionMethod $entity)
    {
        $form = $this->createForm(new TransactionMethodType(), $entity, array(
            'action' => $this->generateUrl('transactionmethod_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing TransactionMethod entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:TransactionMethod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TransactionMethod entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('transactionmethod_edit', array('id' => $id)));
        }
        $entities = $em->getRepository('SettingToolBundle:TransactionMethod')->findAll(array(),array('name'=>'asc'));
        return $this->render('SettingToolBundle:TransactionMethod:new.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $entities,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a TransactionMethod entity.
     *
     */
    public function deleteAction(TransactionMethod $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TransactionMethod entity.');
        }

        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('transactionmethod_new'));
    }


    /**
     * Status a TransactionMethod entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:TransactionMethod')->find($id);

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
        return $this->redirect($this->generateUrl('transactionmethod_new'));
    }


}
