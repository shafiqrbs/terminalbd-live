<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Controller;

use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DoctorService;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsConfig;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsService;
use Appstore\Bundle\DoctorPrescriptionBundle\Form\ConfigType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * DpsConfigController.
 *
 */
class DpsConfigController extends Controller
{


    public function manageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getUser()->getGlobalOption()->getDpsConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
        $form = $this->createEditForm($entity);
        $pagination = $em->getRepository('DoctorPrescriptionBundle:DpsService')->getServiceForPrescription($entity);
        return $this->render('DoctorPrescriptionBundle:Config:manage.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DpsConfig $entity)
    {

        $config = $this->getUser()->getGlobalOption()->getDpsConfig();
        $form = $this->createForm(new ConfigType($config), $entity, array(
            'action' => $this->generateUrl('dps_config_update'),
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
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getUser()->getGlobalOption()->getDpsConfig();
        $pagination = $em->getRepository('DoctorPrescriptionBundle:DpsService')->getServiceForPrescription($entity);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Report has been created successfully"
            );
            $data = $request->request->all();
            $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsService')->prescriptionServiceUpdate($entity,$data);
            if($entity->isCustomPrescription() == 1){
               /*// $print = '../src/Appstore/Bundle/DoctorPrescriptionBundle/Resources/views/Print/print.html.twig';
               // $copy = '../src/Appstore/Bundle/DoctorPrescriptionBundle/Resources/views/Print/'.$entity->getGlobalOption()->getSlug().'.html.twig';

                $srcfile='src/Appstore/Bundle/DoctorPrescriptionBundle/Resources/views/Print/print.html.twig';
                echo $dstfile='src/Appstore/Bundle/DoctorPrescriptionBundle/Resources/views/Print/'.$entity->getGlobalOption()->getSlug().'.html.twig';
               // mkdir(dirname($dstfile), 0777, true);
              //  copy($srcfile, $dstfile);


               // copy($print,$copy);
               // chmod($copy,0777);*/
            }
            return $this->redirect($this->generateUrl('dps_config_manage'));
        }

        return $this->render('DoctorPrescriptionBundle:Config:manage.html.twig', array(
            'entity'        => $entity,
            'pagination'    => $pagination,
            'form'          => $editForm->createView(),
        ));
    }

    public function resetDefaultServiceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getDpsConfig();
        $entities = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DoctorService')->findBy(array('status'=>1));
        /* @var $entity DoctorService */
        foreach ($entities as $entity){
            $exist = $this->getDoctrine()->getRepository('DoctorPrescriptionBundle:DpsService')->findOneBy(array('dpsConfig'=> $config,'doctorService' => $entity));
            if(empty($exist)){
                $service = new DpsService();
                $service->setName(trim($entity->getName()));
                if($entity->getSlug() != 'other-service'){
                    $service->setDoctorService($entity);
                }
                $service->setSlug(trim($entity->getSlug()));
                $service->setServiceFormat(trim($entity->getSlug()));
                $service->setStatus(1);
                $service->setDpsConfig($config);
                $em->persist($service);
                $em->flush();
            }

        }
        return $this->redirect($this->generateUrl('dps_config_manage'));
    }

}

