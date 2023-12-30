<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup;
use Appstore\Bundle\HospitalBundle\Form\ServiceGroupType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * CabinController controller.
 *
 */
class ServiceGroupController extends Controller
{


    /**
     * Lists all HmsServiceGroup entities.
     *
     */
    public function indexAction()
    {

        $entity = new HmsServiceGroup();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $pagination = $em->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig' => $config),array('name'=>'ASC'));
        $form = $this->createCreateForm($entity);
        return $this->render('HospitalBundle:ServiceGroup:index.html.twig', array(
            'pagination' => $pagination,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

    }

    /**
     * Creates a new HmsServiceGroup entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new HmsServiceGroup();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $pagination = $em->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig' => $config,'service'=>4),array('name'=>'ASC'));
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setHospitalConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('hms_service_group', array('id' => $entity->getId())));
        }

        return $this->render('HospitalBundle:ServiceGroup:index.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a HmsServiceGroup entity.
     *
     * @param HmsServiceGroup $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(HmsServiceGroup $entity)
    {
        $form = $this->createForm(new ServiceGroupType(), $entity, array(
            'action' => $this->generateUrl('hms_service_group_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to edit an existing HmsServiceGroup entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $pagination = $em->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig' => $config),array('name'=>'ASC'));
        $entity = $em->getRepository('HospitalBundle:HmsServiceGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HmsServiceGroup entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('HospitalBundle:ServiceGroup:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a HmsServiceGroup entity.
     *
     * @param HmsServiceGroup $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(HmsServiceGroup $entity)
    {
        $form = $this->createForm(new ServiceGroupType(), $entity, array(
            'action' => $this->generateUrl('hms_service_group_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing HmsServiceGroup entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $pagination = $em->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig' => $config),array('name'=>'ASC'));
        $entity = $em->getRepository('HospitalBundle:HmsServiceGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HmsServiceGroup entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('hms_service_group'));
        }

        return $this->render('HospitalBundle:ServiceGroup:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a HmsServiceGroup entity.
     *
     */
    public function deleteAction(HmsServiceGroup $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HmsServiceGroup entity.');
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
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return $this->redirect($this->generateUrl('hms_service_group'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(HmsServiceGroup $entity)
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
        return $this->redirect($this->generateUrl('hms_service_group'));
    }
}
