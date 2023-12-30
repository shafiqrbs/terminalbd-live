<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceReturn;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Response;

/**
 * InvoiceReturn controller.
 *
 */
class InvoiceReturnController extends Controller
{

    /**
     * Lists all Vendor entities.
     *
     */

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    /**
     * @Secure(roles="ROLE_HMS,ROLE_DOMAIN");
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->invoiceLists( $user, $data);
        $pagination = $this->paginate($entities);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5,6));
        return $this->render('HospitalBundle:InvoiceReturn:index.html.twig', array(
            'entities' => $pagination,
            'assignDoctors' => $assignDoctors,
            'referredDoctors' => $referredDoctors,
            'searchForm' => $data,
        ));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_ADMIN,ROLE_DOMAIN");
     */

    public function createAction($id)
    {
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital, 'id' => $id));
        if(!empty($entity->getHmsInvoiceReturn())){
            return $this->redirect($this->generateUrl('hms_invoicereturn'));
        }
        return $this->render('HospitalBundle:InvoiceReturn:confirm.html.twig', array(
            'entity' => $entity,
        ));

    }

    public function paymentAction(Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $_REQUEST;
        $payment = $request['payment'];
        $remark = $request['remark'];
        if ( (!empty($invoice)  and empty($invoice->HmsInvoiceReturn) and !empty($payment)) and $invoice->getTotal() >= $payment ) {
            $em = $this->getDoctrine()->getManager();
            $entity = new HmsInvoiceReturn();
            $entity->setHospitalConfig($invoice->getHospitalConfig());
            $entity->setHmsInvoice($invoice);
            $entity->setRemark($remark);
            $entity->setAmount($payment);
            $entity->setProcess('In-progress');
            $em->persist($entity);
            $em->flush();
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

    public function canceledAction(InvoiceParticular $particular)
    {
        $em = $this->getDoctrine()->getManager();
        $particular->setProcess('Canceled');
        $particular->setParticularDeliveredBy(null);
        $em->flush();
        return new Response('success');
    }

    public function approveAction(HmsInvoiceReturn $invoice )
    {
        $em = $this->getDoctrine()->getManager();
        if(!empty($invoice) and $invoice->getProcess() != 'Approved'){
            $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->hmsReturnInvoice($invoice);
            $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateProcess($invoice);
            $invoice->setProcess('Approved');
            $em->persist($invoice);
            $em->flush($invoice);
            return new Response('success');
        }else{
            return new Response('failed');
        }
        exit;


    }

    public function deleteAction(HmsInvoiceReturn $return )
    {
        $em = $this->getDoctrine()->getManager();
        if (!$return) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($return);
        $em->flush();
        return new Response('success');
        exit;
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HospitalBundle:HmsInvoiceReturn')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceReturn entity.');
        }

        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('hms_InvoiceReturn'));
    }

}
