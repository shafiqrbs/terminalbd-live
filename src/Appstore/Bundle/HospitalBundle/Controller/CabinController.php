<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\DoctorType;
use Appstore\Bundle\HospitalBundle\Form\CabinType;
use Appstore\Bundle\HospitalBundle\Form\ParticularType;
use Appstore\Bundle\HospitalBundle\Form\PathologyType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * CabinController controller.
 *
 */
class CabinController extends Controller
{


    /**
     * Lists all Particular entities.
     *
     */
    public function indexAction()
    {
        $entity = new Particular();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $pagination = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $config,'service'=> 2),array('name'=>'ASC'));
        $form = $this->createCreateForm($entity);
        return $this->render('HospitalBundle:Cabin:index.html.twig', array(
            'pagination' => $pagination,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

    }


    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Particular();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $pagination = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $config,'service'=>2),array('name'=>'ASC'));

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setHospitalConfig($config);
            $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(2);
            $entity->setService($service);
            $name = $entity->getServiceGroup()->getName().'-'.$entity->getRoom();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('hms_cabin', array('id' => $entity->getId())));
        }

        return $this->render('HospitalBundle:Cabin:index.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Particular $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new CabinType($globalOption), $entity, array(
            'action' => $this->generateUrl('hms_cabin_create', array('id' => $entity->getId())),
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
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $pagination = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $config,'service'=> 2),array('name'=>'ASC'));
        $entity = $em->getRepository('HospitalBundle:Particular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('HospitalBundle:Cabin:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
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
    private function createEditForm(Particular $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new CabinType($globalOption), $entity, array(
            'action' => $this->generateUrl('hms_cabin_update', array('id' => $entity->getId())),
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
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $pagination = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $config,'service'=> 2),array('name'=>'ASC'));
        $entity = $em->getRepository('HospitalBundle:Particular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $name = $entity->getServiceGroup()->getName().'-'.$entity->getRoom();
            $entity->setName($name);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('hms_cabin'));
        }

        return $this->render('HospitalBundle:Cabin:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
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
        return $this->redirect($this->generateUrl('hms_cabin'));
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
        return $this->redirect($this->generateUrl('hms_cabin'));
    }
}
