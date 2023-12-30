<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Controller;

use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsService;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\Particular;
use Appstore\Bundle\DoctorPrescriptionBundle\Form\DoctorType;
use Appstore\Bundle\DoctorPrescriptionBundle\Form\CabinType;
use Appstore\Bundle\DoctorPrescriptionBundle\Form\OtherServiceType;
use Appstore\Bundle\DoctorPrescriptionBundle\Form\ParticularType;
use Appstore\Bundle\DoctorPrescriptionBundle\Form\PathologyType;
use Appstore\Bundle\DoctorPrescriptionBundle\Form\ServiceType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * ParticularController controller.
 *
 */
class ServiceController extends Controller
{


    /**
     * Lists all Particular entities.
     *
     */
    public function indexAction()
    {
        $entity = new DpsService();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getDpsConfig();
        $pagination = $em->getRepository('DoctorPrescriptionBundle:DpsService')->getServiceLists($config);
        $editForm = $this->createCreateForm($entity);
        return $this->render('DoctorPrescriptionBundle:Service:index.html.twig', array(
            'pagination' => $pagination,
            'searchForm' => $data,
            'form'   => $editForm->createView(),
        ));

    }

    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new DpsService();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getDpsConfig();
        $pagination = $em->getRepository('DoctorPrescriptionBundle:DpsService')->getServiceLists($config);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setDpsConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('dps_service'));
        }

        return $this->render('DoctorPrescriptionBundle:Service:index.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param DpsService $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DpsService $entity)
    {
        $form = $this->createForm(new ServiceType(), $entity, array(
            'action' => $this->generateUrl('dps_service_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing Particular entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getDpsConfig();
        $pagination = $em->getRepository('DoctorPrescriptionBundle:DpsService')->getServiceLists($config);
        //$pagination = $this->paginate($pagination);
        $entity = $em->getRepository('DoctorPrescriptionBundle:DpsService')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity);

        return $this->render('DoctorPrescriptionBundle:Service:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Particular entity.
     *
     * @param DpsService $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DpsService $entity)
    {

        $form = $this->createForm(new ServiceType(), $entity, array(
            'action' => $this->generateUrl('dps_service_update', array('id' => $entity->getId())),
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
        $config = $this->getUser()->getGlobalOption()->getDpsConfig();
        $pagination = $em->getRepository('DoctorPrescriptionBundle:DpsService')->getServiceLists($config);
        //$pagination = $this->paginate($pagination);
        $entity = $em->getRepository('DoctorPrescriptionBundle:DpsService')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setSlug($entity->getName());
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('dps_service'));
        }
        $pagination = $em->getRepository('DoctorPrescriptionBundle:DpsService')->getServiceLists($config);
        return $this->render('DoctorPrescriptionBundle:Service:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Particular entity.
     *
     */
    public function deleteAction(DpsService $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
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
        return $this->redirect($this->generateUrl('dps_service'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(DpsService $entity)
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
        return $this->redirect($this->generateUrl('dps_service:'));
    }

    /**
     * Lists all FeatureWidget entities.
     *
     */
    public function sortedAction(Request $request,$fieldName)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsService')->setListOrdering($fieldName,$data);
        exit;
    }

}
