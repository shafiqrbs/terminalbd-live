<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\InvoicePaymentType;
use Appstore\Bundle\HospitalBundle\Form\NewPatientAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\PatientAdmissionType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class PatientController extends Controller
{

    public function patientAction(Invoice $entity)
    {

        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            return $this->render('HospitalBundle:AdmissionPatient:index.html.twig', array(
                'entity' => $entity,
                'option' => $this->getUser()->getGlobalOption(),
            ));
        }else{
            return $this->redirect($this->generateUrl('hms_invoice_admission'));
        }
    }

    public function invoiceSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig();
            $item = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function invoiceAdmissionSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig();
            $item = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->searchAdmissionAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function invoiceParticularSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig();
            $item = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->searchParticularAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function invoiceDoctorSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig();
            $item = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->searchDoctorReferredAutoComplete($item,$inventory,array(5));
        }
        return new JsonResponse($item);
    }
    
    public function invoiceReferredSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig();
            $item = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->searchDoctorReferredAutoComplete($item,$inventory,array(5,6));
        }
        return new JsonResponse($item);
    }

    public function patientGlobalDetailsAction($invoice)
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        $entity = $this->getDoctrine()->getRepository(Invoice::class)->findOneBy(array('hospitalConfig' => $config,'invoice' => trim($invoice)));
        if ($entity) {
            $view =  $this->renderView('HospitalBundle:Patient:ajax-show.html.twig', array(
                'entity' => $entity,
            ));
            return new Response($view);
        } else {
            return new Response("No Record Found!");
        }
    }

    public function patientInfoAction($invoice)
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        $entity = $this->getDoctrine()->getRepository(Invoice::class)->findOneBy(array('hospitalConfig' => $config,'invoice' => trim($invoice)));
        if ($entity) {
            $customer = $entity->getCustomer();
            $data = array(
                'id' => $customer->getId(),
                'patientId' => $customer->getCustomerId(),
                'name' => $customer->getName(),
                'mobile' => $customer->getMobile(),
                'age' => $customer->getAge(),
                'ageType' => $customer->getAgeType(),
                'gender' => $customer->getGender(),
                'height' => $customer->getHeight(),
                'weight' => $customer->getWeight(),
                'bloodPressure' => $customer->getBloodPressure(),
                'address' => $customer->getAddress()
            );
        }
        return new Response(json_encode($data));

    }

    public function assignDoctorSelectAction()
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getParticularIdName($config,array(5));
        $items = array();
            $items[] = array('value' => '','text'=> '-Change Doctor-');
        foreach ($entities as $entity):
            $items[] = array('value' => $entity['id'],'text'=> $entity['particularCode'].'-'.$entity['name']);
        endforeach;
        $items[]=array('value' => '0','text'=> 'Empty Doctor');
        return new JsonResponse($items);

    }

    public function referredDoctorSelectAction()
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getParticularIdName($config,array(5,6));
        $items = array();
        $items[] = array('value' => '','text'=> '-Change Referred-');
        foreach ($entities as $entity):
            $items[] = array('value' => $entity['id'],'text'=> $entity['particularCode'].'-'.$entity['name']);
        endforeach;
        $items[]=array('value' => '0','text'=> 'Empty Referred');
        return new JsonResponse($items);

    }

    public function marketingExecutiveSelectAction()
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getMarketingExecutiveName($config,array(14));
        $items = array();
        $items[] = array('value' => '','text'=> '-Change Marketing Executive-');
        foreach ($entities as $entity):
            $items[] = array('value' => $entity['id'],'text'=> $entity['particularCode'].'-'.$entity['name']);
        endforeach;
        $items[]=array('value' => '0','text'=> 'Empty Marketing Executive');
        return new JsonResponse($items);

    }

    public function cabinSelectAction(Invoice $invoice)
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $cabin = ($invoice->getCabin()) ? $invoice->getCabin()->getId() : 0;
        $cabins = $this->getDoctrine()->getRepository(Invoice::class)->getExistCabin($config,$cabin);
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getCurrentCabins($config,2,$cabins);
        $items = array();
        $items[] = array('value' => '','text'=> '-Change Patient Cabin-');
        foreach ($entities as $entity):
            $items[] = array('value' => $entity['id'],'text'=> $entity['name']);
        endforeach;
        $items[]=array('value' => '0','text'=> 'Empty Cabin');
        return new JsonResponse($items);

    }

    public function inlinePatientUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Invoice::class)->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }
        if($data['name']){
            $filedName= "set{$data['name']}";
            $setValue = $em->getRepository(Particular::class)->find($data['value']);
            if($setValue){
                $entity->$filedName($setValue);
            }else {
                $entity->$filedName(NULL);
            }
        }
        $em->persist($entity);
        $em->flush();
        exit;

    }

    public function inlinePatientVisitDoctorUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Invoice::class)->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }
        if($data['name']){
            $setValue = $em->getRepository(Particular::class)->find($data['value']);
            if($setValue){
                $entity->setAssignDoctor($setValue);
                $entity->setSubTotal($setValue->getPrice());
                $entity->setTotal($setValue->getPrice());
                $entity->setPayment($setValue->getPrice());
            }else {
                $entity->setAssignDoctor(NULL);
                $entity->setSubTotal(0);
                $entity->setTotal(0);
                $entity->setPayment(0);
            }
        }
        $em->persist($entity);
        $em->flush();
        return new Response('success');

    }

    public function inlinePatientFinalBillUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(InvoiceParticular::class)->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }
        $setValue = $data['value'];
        $entity->setDiscountPrice($setValue);
        $em->persist($entity);
        $em->flush();
        return new Response('success');

    }



}

