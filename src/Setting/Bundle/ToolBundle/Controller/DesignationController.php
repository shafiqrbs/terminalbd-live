<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Setting\Bundle\ToolBundle\Entity\Designation;
use Setting\Bundle\ToolBundle\Form\DesignationType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;


/**
 * Designation controller.
 *
 */
class DesignationController extends Controller
{

    /**
     * Lists all Designation entities.
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SettingToolBundle:Designation')->findBy(array(),array('code'=>'asc'));
        return $this->render('SettingToolBundle:Designation:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Designation entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Designation();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('designation'));
        }

        return $this->render('SettingToolBundle:Designation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Designation entity.
     *
     * @param Designation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Designation $entity)
    {

        $em = $this->getDoctrine()->getRepository('SettingToolBundle:Designation');
        $form = $this->createForm(new DesignationType($em), $entity, array(
            'action' => $this->generateUrl('designation_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Designation entity.
     * @Secure(roles="ROLE_ADMIN")
     */

    public function newAction()
    {
        $entity = new Designation();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:Designation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Designation entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:Designation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Designation entity.');
        }

        return $this->render('SettingToolBundle:Designation:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Designation entity.
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:Designation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Designation entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('SettingToolBundle:Designation:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Designation entity.
    *
    * @param Designation $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Designation $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingToolBundle:Designation');
        $form = $this->createForm(new DesignationType($em), $entity, array(
            'action' => $this->generateUrl('designation_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing Designation entity.
     * @Secure(roles="ROLE_ADMIN")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:Designation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Designation entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('designation_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:Designation:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Designation entity.
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction(Designation $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }
        return $this->redirect($this->generateUrl('designation'));
    }


}
