<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Entity\PathologicalReport;
use Appstore\Bundle\HospitalBundle\Form\PathologicalReportType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 * Pathology controller.
 *
 */
class PathologicalReportController extends Controller
{



    /**
     * Displays a form to edit an existing Particular entity.
     *
     */
    public function pathologicalReportAction(Particular $pathology)
    {
        $hmsc = $this->getUser()->getGlobalOption()->getHospitalConfig();
        if($hmsc->getId() == $pathology->getHospitalConfig()->getId()){
            $em = $this->getDoctrine()->getManager();
            $entity = New PathologicalReport();
            $form = $this->createCreateForm($entity,$pathology);
            $reportFormats = $this->getDoctrine()->getRepository('HospitalBundle:PathologicalReport')->findBy(array('particular' => $pathology),array('sorting'=>'asc','parent'=>'asc'));
            return $this->render('HospitalBundle:Pathology:pathologicalReport.html.twig', array(
                'entity'        => $entity,
                'pathology'     => $pathology,
                'reportFormats'     => $reportFormats,
                'form'          => $form->createView(),
            ));
        }else{
            throw $this->createNotFoundException('Unable to find wrong access.');
        }


    }

    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request,Particular $pathology)
    {
        $entity = new PathologicalReport();
        $form = $this->createCreateForm($entity,$pathology);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setParticular($pathology);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('hms_pathological_report', array('pathology' => $pathology->getId())));
        }

        return $this->render('HospitalBundle:Pathology:new.html.twig', array(
            'entity'        => $entity,
            'pathology'     => $pathology,
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
    private function createCreateForm(PathologicalReport $entity, Particular $pathology)
    {

        $form = $this->createForm(new PathologicalReportType($pathology), $entity, array(
            'action' => $this->generateUrl('hms_pathological_report_create', array('pathology' => $pathology->getId())),
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
    public function editAction(Particular $pathology,PathologicalReport $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createEditForm($entity,$pathology);
        return $this->render('HospitalBundle:Pathology:pathologicalReport.html.twig', array(
            'entity'        => $entity,
            'pathology'     => $pathology,
            'form'          => $form->createView(),
        ));
    }



    /**
     * Creates a form to edit a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PathologicalReport $entity,Particular $pathology)
    {
        $form = $this->createForm(new PathologicalReportType($pathology), $entity, array(
            'action' => $this->generateUrl('hms_pathological_report_update', array('pathology' => $pathology->getId(),'id' => $entity->getId())),
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
    public function updateAction(Request $request, Particular $pathology, PathologicalReport $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $editForm = $this->createEditForm($entity,$pathology);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('hms_pathological_report',array('pathology'=>$pathology->getId())));
        }

        return $this->render('HospitalBundle:Pathology:pathologicalReport.html.twig', array(
            'entity'      => $entity,
            'pathology'     => $pathology,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Deletes a Particular entity.
     *
     */
    public function deleteAction(Particular $pathology, PathologicalReport $pathologicalReport)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$pathologicalReport) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        try {

            $em->remove($pathologicalReport);
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
        return $this->redirect($this->generateUrl('hms_pathological_report',array('pathology'=>$pathology->getId())));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Particular $pathology , PathologicalReport $entity)
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
        return $this->redirect($this->generateUrl('hms_pathological_report',array('pathology' => $pathology->getId())));
    }

    public function sortingAction(Request $request,Particular $pathology)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('HospitalBundle:PathologicalReport')->updateSorting($data);
        exit;
    }

    public function inlineReportUpdateAction(Request $request)
    {
        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager();
        /* @var $entity PathologicalReport */
        $entity = $this->getDoctrine()->getRepository(PathologicalReport::class)->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PathologicalReport entity.');
        }
        $setField = 'set' . $data['name'];
        $entity->$setField($data['value']);
        $em->flush();
        return new Response('success');

    }
}
