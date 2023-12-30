<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoicePathologicalReport;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\ConfigType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceParticularType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * HospitalConfigController.
 *
 */
class HospitalConfigController extends Controller
{


    /**
     * @Secure(roles="ROLE_DOMAIN");
     */

    public function manageAction()
    {

        $entity = $this->getUser()->getGlobalOption()->getHospitalConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
        $form = $this->createEditForm($entity);
        return $this->render('HospitalBundle:Config:manage.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param HospitalConfig $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(HospitalConfig $entity)
    {

        $hospitalConfig = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $form = $this->createForm(new ConfigType($hospitalConfig), $entity, array(
            'action' => $this->generateUrl('hms_config_update'),
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
     * @Secure(roles="ROLE_DOMAIN");
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var  $entity HospitalConfig */
        $entity = $this->getUser()->getGlobalOption()->getHospitalConfig();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->HeaderUpload() && !empty($entity->getHeaderFile())){
                $entity->removeHeaderUpload();
            }
            if($entity->footerUpload() && !empty($entity->getFooterFile())){
                $entity->removeFooterUpload();
            }
            $entity->headerUpload();
            $entity->footerUpload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Report has been created successfully"
            );

            return $this->redirect($this->generateUrl('hms_config_manage'));
        }

        return $this->render('HospitalBundle:Config:manage.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

}

