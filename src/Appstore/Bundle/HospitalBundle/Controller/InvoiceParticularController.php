<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoicePathologicalReport;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceParticularType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Hackzilla\BarcodeBundle\Utility\Barcode;
/**
 * InvoiceParticularcontroller.
 *
 */
class InvoiceParticularController extends Controller
{

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_COMMISSION,ROLE_DOMAIN_HOSPITAL_OPERATOR,ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN");
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();

        $entities = $em->getRepository('HospitalBundle:InvoiceParticular')->invoicePathologicalReportLists( $user, $data);
        $pagination = $this->paginate($entities);
        $particularService = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->processPathologicalReports($hospital->getId());
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(6));

        return $this->render('HospitalBundle:InvoiceParticular:index.html.twig', array(
            'entities' => $pagination,
            'particularService' => $particularService,
            'assignDoctors' => $assignDoctors,
            'referredDoctors' => $referredDoctors,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_LAB,ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_DOCTOR");
     */
    public function reportProcessAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:InvoiceParticular')->invoicePathologicalReportLists( $user,$data);
        $pagination = $this->paginate($entities);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(6));
        $particularService = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->processPathologicalReports($hospital->getId());

        return $this->render('HospitalBundle:InvoiceParticular:diagnostic.html.twig', array(
            'entities' => $pagination,
            'particularService' => $particularService,
            'assignDoctors' => $assignDoctors,
            'referredDoctors' => $referredDoctors,
            'searchForm' => $data,
        ));
    }


    public function showAction(Invoice $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        return $this->render('HospitalBundle:InvoiceParticular:show.html.twig', array(
            'entity' => $entity,
        ));
    }

    public function sampleCollectionAction(Invoice $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
        return $this->render('HospitalBundle:InvoiceParticular:sampleCollection.html.twig', array(
            'entity' => $entity,
        ));
    }

    public function getBarcode($barcodePrint)
    {
       
        $barcode = new BarcodeGenerator();
        $barcode->setText($barcodePrint);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(25);
        $barcode->setFontSize(10);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,'.$code .'" />';
        return $data;
    }

    public function sampleCollectionBarcodeAction(InvoiceParticular $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
	   // $barcodePrint = $entity->getParticular()->getParticularCode().'-'.$entity->getReportCode();
	    $barcodePrint = $entity->getHmsInvoice()->getInvoice();
        $barcodeReport = $this->getBarcode($barcodePrint);
        return $this->render('HospitalBundle:InvoiceParticular:sampleCollectionBarcode.html.twig', array(
            'entity' => $entity,
            'barcode' => $barcodeReport,
        ));
    }

    public function sampleCollectionConfirmAction(InvoiceParticular $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice Particular entity preparation.');
        }
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('Collected');
            $entity->setSampleCollectedBy($this->getUser());
            $entity->setCollectionDate(new \DateTime());
            $em->flush();
        }
        exit;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_LAB,ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_DOCTOR");
     */

    public function machineFormatAction(InvoiceParticular $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setParticularDeliveredBy($this->getUser());
        $entity->setParticularPreparedBy($this->getUser());
        $entity->setCollectionDate(new \DateTime());
        $entity->setProcess('Collected');
        $entity->setSampleCollectedBy($this->getUser());
        $entity->setProcess('Done');
        $em->persist($entity);
        $em->flush();
        return new Response('done');
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_LAB,ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_DOCTOR");
     */

    public function preparationAction(InvoiceParticular $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
        $form = $this->createEditForm($entity);

        /** @var  $reportArr */
        $reportArr = array();

        /** @var InvoicePathologicalReport $row */
        $em = $this->getDoctrine()->getManager();
        if (!empty($entity->getInvoicePathologicalReports())){
            foreach ($entity->getInvoicePathologicalReports() as $row):
                if(!empty($row->getPathologicalReport())){
                    $reportArr[$row->getPathologicalReport()->getId()] = $row;
                }
            endforeach;
        }
        return $this->render('HospitalBundle:InvoiceParticular:new.html.twig', array(
            'entity' => $entity,
            'report' => $reportArr,
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
    private function createEditForm(InvoiceParticular $entity)
    {

        $hospitalConfig = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $form = $this->createForm(new InvoiceParticularType($hospitalConfig), $entity, array(
            'action' => $this->generateUrl('hms_invoice_particular_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
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

        $entity = $em->getRepository('HospitalBundle:InvoiceParticular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();

        if ($editForm->isValid()) {
            $entity->setParticularPreparedBy($this->getUser());
            $entity->upload();
            $em->flush();
            $this->getDoctrine()->getRepository('HospitalBundle:InvoicePathologicalReport')->insert($entity,$data);
            $this->get('session')->getFlashBag()->add(
                'success',"Report has been created successfully"
            );
            return $this->redirect($this->generateUrl('hms_invoice_particular_preparation',array('id'=> $entity->getId())));
        }

        return $this->render('HospitalBundle:Pathology:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    public function deliveryAction(InvoiceParticular $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setParticularDeliveredBy($this->getUser());
            $em->flush();
        }
        $invoice = $entity->getHmsInvoice();
        if($invoice->getPaymentStatus() == 'Paid' and $invoice->getReportCount() == $invoice->getDeliveryCount()){
            $em = $this->getDoctrine()->getManager();
            $invoice->setProcess('Done');
            $em->flush();
            $accountInvoice = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertHospitalFinalAccountInvoice($invoice);
           // $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->hmsSalesFinal($invoice, $accountInvoice);
        }
        return $this->redirect($this->generateUrl('hms_invoice_confirm', array('id' => $entity->getHmsInvoice()->getId())));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_MANAGER,ROLE_DOMAIN,ROLE_DOMAIN_HOSPITAL_ADMIN");
     */

    public function reportReverseAction(InvoiceParticular $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('In-progress');
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hms_invoice_particular_preparation',['id'=> $entity->getId()]));
    }

     public function pathologicalReportPrintAction(Request $request ,InvoiceParticular $entity)
    {
        /** @var  $reportArr */
        $reportArr = array();

        /** @var InvoicePathologicalReport $row */
        $global = $this->getUser()->getGlobalOption();
        $config = $global->getHospitalConfig();
        if($config->getId() != $entity->getHmsInvoice()->getHospitalConfig()->getId()){
             $referer = $request->headers->get('referer');
             return new RedirectResponse($referer);
        }
        $barcodePrint = $entity->getHmsInvoice()->getInvoice();
        $barcodeInvoice = $this->getBarcode($barcodePrint);
        $barcodePrint = $entity->getParticular()->getParticularCode().'-'.$entity->getReportCode();
        $barcodeReport = $this->getBarcode($barcodePrint);

        if (!empty($entity->getInvoicePathologicalReports())){
            foreach ($entity->getInvoicePathologicalReports() as $row):
                if(!empty($row->getPathologicalReport())){
                    $reportArr[$row->getPathologicalReport()->getId()] = $row;
                }
            endforeach;
        }
        $hospital = $global->getHospitalConfig();
        if($hospital->isCustomPrint() == 1){
            $template = "Print/{$global->getId()}:pathological";
        }else{
            $template = "InvoiceParticular:pathologicalReportPrint";
        }
        return $this->render("HospitalBundle:{$template}.html.twig", array(
            'entity'            => $entity,
            'global'            => $global,
            'printUser'         => $this->getUser(),
            'barcodeInvoice'    => $barcodeInvoice,
            'barcodeReport'     => $barcodeReport,
            'report'            => $reportArr,
        ));
    }



    public function pathologicalReportDeleteAction(InvoicePathologicalReport $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new  JsonResponse('success');
        exit;

    }

    public function reportStatusSelectAction()
    {
        $items  = array();
        $items[]= array('value' => 'In-progress','text'=>'In-progress');
        $items[]= array('value' => 'Done','text'=>'Done');
        $items[]= array('value' => 'Damage','text'=>'Damage');
        $items[]= array('value' => 'Impossible','text'=>'Impossible');
        return new JsonResponse($items);
    }


}

