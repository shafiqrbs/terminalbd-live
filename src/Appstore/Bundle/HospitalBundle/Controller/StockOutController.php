<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HmsStockOut;
use Appstore\Bundle\HospitalBundle\Entity\HmsStockOutItem;
use Appstore\Bundle\HospitalBundle\Entity\HmsVendor;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\StockOutItemType;
use Appstore\Bundle\HospitalBundle\Form\StockOutType;
use Appstore\Bundle\HospitalBundle\Form\VendorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * StockOutController controller.
 *
 */
class StockOutController extends Controller
{

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
     * Lists all Vendor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:HmsStockOut')->findBy(array('hospitalConfig' => $hospital),array('created'=>'DESC'));
        $pagination = $this->paginate($entities);

        return $this->render('HospitalBundle:StockOut:index.html.twig', array(
            'entities' => $pagination,
        ));
    }



    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new HmsStockOut();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity->setHospitalConfig($hospital);
        $entity->setCreatedBy($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_stockout_edit', array('id' => $entity->getId())));

    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:HmsStockOut')->findOneBy(array('hospitalConfig' => $hospital , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $itemForm = $this->createItemForm($entity);
        $particulars = $em->getRepository('HospitalBundle:Particular')->getMedicineParticular($hospital);
        return $this->render('HospitalBundle:StockOut:new.html.twig', array(
            'entity' => $entity,
            'particulars' => $particulars,
            'form' => $editForm->createView(),
            'itemForm' => $itemForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(HmsStockOut $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $form = $this->createForm(new StockOutType($config), $entity, array(
            'action' => $this->generateUrl('hms_stockout_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'posForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createItemForm(HmsStockOut $invoice)
    {
	    $entity = new HmsStockOutItem();
    	$config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $form = $this->createForm(new StockOutItemType($config), $entity, array(
            'action' => $this->generateUrl('hms_stockout_particular_add', array('invoice' => $invoice->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() ,'quantity'=> 1 , 'minimumPrice'=> '', 'instruction'=>'')));
    }

    public function addParticularAction(Request $request, HmsStockOut $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('HospitalBundle:HmsStockOutItem')->insertStockOutItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('HospitalBundle:HmsStockOut')->updateStockOutTotalPrice($invoice);
        $msg = 'Particular added successfully';
	    $result = $this->returnResultData($invoice,$msg);
	    return new Response(json_encode($result));
	    exit;
    }

    public function invoiceParticularDeleteAction(HmsStockOut $invoice, $id){

	    $em = $this->getDoctrine()->getManager();
	    $particular = $this->getDoctrine()->getRepository('HospitalBundle:HmsStockOutItem')->find($id);
        if (empty($particular)) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('HospitalBundle:HmsStockOut')->updateStockOutTotalPrice($invoice);
        $msg = 'Particular deleted successfully';
		$result = $this->returnResultData($invoice,$msg);
	    return new Response(json_encode($result));

        exit;


    }

	public function returnResultData(HmsStockOut $invoice,$msg=''){

		$invoiceParticulars = $this->getDoctrine()->getRepository('HospitalBundle:HmsStockOutItem')->getStockOutItems($invoice);
		$subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
		$grandTotal = $invoice->getNetTotal() > 0 ? $invoice->getNetTotal() : 0;
		$dueAmount = $invoice->getDue() > 0 ? $invoice->getDue() : 0;
		$data = array(
			'subTotal' => $subTotal,
			'grandTotal' => $grandTotal,
			'dueAmount' => $dueAmount ,
			'invoiceParticulars' => $invoiceParticulars ,
			'msg' => $msg ,
			'success' => 'success'
		);
		return $data;

	}

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discount = (float)$request->request->get('discount');
	    $invoice = $request->request->get('invoice');

	    $invoice = $em->getRepository('HospitalBundle:HmsStockOut')->find($invoice);
        $total = ($invoice->getSubTotal() - $discount);
        $vat = 0;
        if($total > $discount ){

	        $invoice->setDiscount($discount);
	        $invoice->setNetTotal($total + $vat);
	        $invoice->setDue($total + $vat);
            $em->persist($invoice);
            $em->flush();
        }
	    $result = $this->returnResultData($invoice);
	    return new Response(json_encode($result));
	    exit;
    }

    public function updateAction(Request $request, HmsStockOut $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $entity->setProcess('Done');
        $entity->setDue($entity->getNetTotal() - $entity->getPayment());
        $em->flush();
        return $this->redirect($this->generateUrl('hms_stockout_show', array('id' => $entity->getId())));

    }


    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HospitalBundle:HmsStockOut')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('HospitalBundle:StockOut:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction(HmsStockOut $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($entity) and $entity->getProcess() == "Done") {
            $em = $this->getDoctrine()->getManager();
	        $entity->setProcess('Approved');
	        $entity->setApprovedBy($this->getUser());
            $em->flush();
          //  $accountStockOut = $em->getRepository('AccountingBundle:AccountStockOut')->insertHmsAccountStockOut($purchase);
          //  $em->getRepository('AccountingBundle:Transaction')->purchaseGlobalTransaction($accountStockOut);
	        $this->getDoctrine()->getRepository('HospitalBundle:Particular')->insertIssueItem($entity);

	        return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }



    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction(HmsStockOut $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_stockout'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HospitalBundle:HmsStockOut')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
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
        return $this->redirect($this->generateUrl('hms_stockout'));
    }


}
