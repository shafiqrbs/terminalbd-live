<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Controller;

use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular;
use Appstore\Bundle\DoctorPrescriptionBundle\Form\DoctorType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * DoctorController controller.
 *
 */
class DoctorController extends Controller
{

    public function paginate($entities)
    {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all DpsParticular entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getDpsConfig();
        $pagination = $em->getRepository('DoctorPrescriptionBundle:DpsParticular')->getFindDentalServiceParticular($config , array('doctor'));
        return $this->render('DoctorPrescriptionBundle:Doctor:index.html.twig', array(
            'entities' => $pagination,
            'categories' => '',
            'departments' => '',
            'searchForm' => $data,
        ));

    }

    /**
     * Creates a new DpsParticular entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new DpsParticular();
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createCreateForm($entity,$globalOption);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setDpsConfig($globalOption ->getDpsConfig());
            $entity->setMode('doctor');
            $entity->setName($entity->getAssignDoctor()->getProfile()->getName());
            $entity->setMobile($entity->getAssignDoctor()->getProfile()->getMobile());
            if(empty($entity->getDesignation())){
                $entity->setDesignation($entity->getAssignDoctor()->getProfile()->getDesignation()->getName());
            }
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('dps_doctor_new', array('id' => $entity->getId())));
        }

        return $this->render('DoctorPrescriptionBundle:Doctor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a DpsParticular entity.
     *
     * @param DpsParticular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DpsParticular $entity, $globalOption)
    {

        $form = $this->createForm(new DoctorType($globalOption), $entity, array(
            'action' => $this->generateUrl('dps_doctor_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new DpsParticular entity.
     *
     */
    public function newAction()
    {
        $entity = new DpsParticular();
        $globalOption = $this->getUser()->getGlobalOption();
        $form   = $this->createCreateForm($entity,$globalOption);

        return $this->render('DoctorPrescriptionBundle:Doctor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DpsParticular entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('DoctorPrescriptionBundle:DpsParticular')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DpsParticular entity.');
        }
        return $this->render('DoctorPrescriptionBundle:Doctor:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing DpsParticular entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DoctorPrescriptionBundle:DpsParticular')->find($id);

        if (!$entity and $this->getUser()->getDpsConfig()->getId() == $entity ->getDpsConfig()->getId()) {
            throw $this->createNotFoundException('Unable to find DpsParticular entity.');
        }
        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);

        return $this->render('DoctorPrescriptionBundle:Doctor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a DpsParticular entity.
     *
     * @param DpsParticular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DpsParticular $entity,$globalOption)
    {

        $form = $this->createForm(new DoctorType($globalOption), $entity, array(
            'action' => $this->generateUrl('dps_doctor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing DpsParticular entity.
     *
     */
    public function updateAction(Request $request, DpsParticular $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity and $this->getUser()->getDpsConfig()->getId() == $entity ->getDpsConfig()->getId()) {
            throw $this->createNotFoundException('Unable to find DpsParticular entity.');
        }

        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setName($entity->getAssignDoctor()->getProfile()->getName());
            $entity->setMobile($entity->getAssignDoctor()->getProfile()->getMobile());
            if(empty($entity->getDesignation())){
                $entity->setDesignation($entity->getAssignDoctor()->getProfile()->getDesignation()->getName());
            }
            if($entity->upload()){
                $entity->removeUpload();
                $entity->upload();
            }else{
                $entity->upload();
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('dps_doctor_edit',array('id' => $entity->getId())));
        }

        return $this->render('DoctorPrescriptionBundle:Doctor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a DpsParticular entity.
     *
     */
    public function deleteAction(DpsParticular $entity)
    {
        $em = $this->getDoctrine()->getManager();
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
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return $this->redirect($this->generateUrl('dps_doctor'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(DpsParticular $entity)
    {

        $em = $this->getDoctrine()->getManager();
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
        return $this->redirect($this->generateUrl('dps_doctor'));
    }
}
