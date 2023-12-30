<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\DoctorType;
use Appstore\Bundle\HospitalBundle\Form\ParticularType;
use Appstore\Bundle\HospitalBundle\Form\PathologyType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\Constraint;


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
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:Particular')->findWithSearch($config , $service = 5, $data);
        $pagination = $this->paginate($entities);
        $categories = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory')->findBy(array('parent'=>2),array('name' =>'asc' ));
        $departments = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory')->findBy(array('parent'=>7),array('name' =>'asc' ));
        return $this->render('HospitalBundle:Doctor:index.html.twig', array(
            'entities' => $pagination,
            'categories' => $categories,
            'departments' => $departments,
            'searchForm' => $data,
        ));

    }

    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Particular();
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createCreateForm($entity,$globalOption);
        $form->handleRequest($request);
        $data = $request->request->all();
        $visitMods = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('service'=>13,'hospitalConfig' => $globalOption->getHospitalConfig()));
        $valid = 0;
        if($entity->getAssignDoctor()){
            $validCount = $this->getDoctrine()->getRepository(Particular::class)->findOneBy(array('assignDoctor'=> $entity->getAssignDoctor()));
            $valid = count($validCount);
        }
        if($valid > 0 ){
            $this->get('session')->getFlashBag()->add(
                'notice',"Doctor already existing,Please try again."
            );
        }
        if ($form->isValid() and $valid == 0) {
            $em = $this->getDoctrine()->getManager();
            $entity->setHospitalConfig($globalOption->getHospitalConfig());
            $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(5);
            $entity->setService($service);
            if($entity->getParticularCode()){
               $entity->setParticularCode($entity->getParticularCode());
            }
            $entity->setIsDoctor(1);
            $em->persist($entity);
            $entity->upload();
            $entity->signatureUpload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            $this->getDoctrine()->getRepository(Particular::class)->insertDoctorVisitModes($entity,$data);
            return $this->redirect($this->generateUrl('hms_doctor_new', array('id' => $entity->getId())));
        }

        return $this->render('HospitalBundle:Doctor:new.html.twig', array(
            'entity' => $entity,
            'visitMods' => $visitMods,
            'form'   => $form->createView(),
        ));
    }


    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = (string) $child->getErrors(true, false);
            }
        }
        return $errors;
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Particular $entity, $globalOption)
    {

        $em = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $form = $this->createForm(new DoctorType($em,$globalOption), $entity, array(
            'action' => $this->generateUrl('hms_doctor_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Particular entity.
     *
     */
    public function newAction()
    {
        $entity = new Particular();
        $globalOption = $this->getUser()->getGlobalOption();
        $form   = $this->createCreateForm($entity,$globalOption);
        $visitMods = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('service'=>13,'hospitalConfig' => $globalOption->getHospitalConfig()));
        return $this->render('HospitalBundle:Doctor:new.html.twig', array(
            'entity' => $entity,
            'visitMods' => $visitMods,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Particular entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HospitalBundle:Particular')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        return $this->render('HospitalBundle:Doctor:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Particular entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $config = $globalOption->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $config,'id'=>$id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity,$globalOption);
        $visitMods = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('service'=>13,'hospitalConfig' => $config));
        $visitAmounts = array();
        if($entity->getVisitModes()){
            foreach ( $entity->getVisitModes() as $mod){
                $visitAmounts[$mod->getService()->getId()] = $mod->getAmount();
            }
        }
        return $this->render('HospitalBundle:Doctor:new.html.twig', array(
            'entity'      => $entity,
            'visitMods'      => $visitMods,
            'visitAmounts'      => $visitAmounts,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Particular $entity,$globalOption)
    {
        $em = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $form = $this->createForm(new DoctorType($em,$globalOption), $entity, array(
            'action' => $this->generateUrl('hms_doctor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing Particular entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $entity Particular */
        $entity = $em->getRepository('HospitalBundle:Particular')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {
            if($entity->upload() && !empty($entity->getFile())){
                $entity->removeUpload();
            }
            if($entity->signatureUpload() && !empty($entity->getSignatureFile())){
                $entity->removeSignatureUpload();
            }
            if($entity->getParticularCode()){
                $entity->setParticularCode($entity->getParticularCode());
            }
            $entity->upload();
            $entity->setIsDoctor(1);
            $entity->signatureUpload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            $this->getDoctrine()->getRepository(Particular::class)->updateDoctorVisitModes($entity,$data);
            return $this->redirect($this->generateUrl('hms_doctor_edit',array('id'=>$id)));
        }
        $visitMods = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('service'=>13,'hospitalConfig' => $globalOption->getHospitalConfig()));
        $visitAmounts = array();
        if($entity->getVisitModes()){
            foreach ( $entity->getVisitModes() as $mod){
                $visitAmounts[$mod->getService()->getId()] = $mod->getAmount();
            }
        }
        return $this->render('HospitalBundle:Doctor:new.html.twig', array(
            'entity'      => $entity,
            'visitMods'      => $visitMods,
            'visitAmounts'      => $visitAmounts,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Particular entity.
     *
     */
    public function deleteAction(Particular $entity)
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
        return $this->redirect($this->generateUrl('hms_doctor'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Particular $entity)
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
        return $this->redirect($this->generateUrl('hms_doctor'));
    }
}
