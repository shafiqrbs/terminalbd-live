<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturn;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseReturn;
use Appstore\Bundle\BusinessBundle\Form\PurchaseReturnType;
use Appstore\Bundle\BusinessBundle\Form\PurchaseType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class PurchaseReturnController extends Controller
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
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $data = $_REQUEST;
	    $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseReturn')->findWithSearch($config->getId(),$data);
        $pagination = $this->paginate($entities);
        return $this->render('BusinessBundle:PurchaseReturn:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    public function downloadPdf($html,$fileName = '')
    {
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename={$fileName}");
        echo $pdf;
        return new Response('');
    }


    public function newAction(){

        $entity = new BusinessPurchaseReturn();
        $form = $this->createCreateForm($entity);
        return $this->render('BusinessBundle:PurchaseReturn:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Creates a new PurchaseReturn entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new BusinessPurchaseReturn();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $particulars = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->getVendorItem($config,$entity->getVendor());
        if ($form->isValid() and !empty($particulars)) {
            $entity->setBusinessConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('business_purchase_return_edit', array('id' => $entity->getId())));
        }
        return $this->render('BusinessBundle:PurchaseReturn:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param BusinessPurchaseReturn $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessPurchaseReturn $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseReturnType($globalOption), $entity, array(
            'action' => $this->generateUrl('business_purchase_return_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $em->getRepository('BusinessBundle:BusinessPurchaseReturn')->findOneBy(array('businessConfig' => $config , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $particulars = $em->getRepository('BusinessBundle:BusinessPurchaseItem')->getVendorItem($config,$entity->getVendor());
	    return $this->render("BusinessBundle:PurchaseReturn:edit.html.twig", array(
            'entity' => $entity,
            'id' => 'purchase',
            'particulars' => $particulars,
        ));
    }


    public function updateAction(Request $request, BusinessPurchaseReturn $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $data = $request->request->all();
        $entity->setProcess('Done');
        $em->flush();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseReturnItem')->insertPurchaseReturnItem($entity,$data);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseReturn')->updatePurchaseTotalPrice($entity);
        $this->approvePurchaseReturn($entity);
        return $this->redirect($this->generateUrl('business_purchase_return'));

    }


    /**
     * Finds and displays a BusinessPurchaseReturn entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

	    $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $entity = $em->getRepository('BusinessBundle:BusinessPurchaseReturn')->findOneBy(array('businessConfig' => $config , 'id' => $id));


	    if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('BusinessBundle:PurchaseReturn:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction($id)
    {
        $em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $purchase = $em->getRepository('BusinessBundle:BusinessPurchaseReturn')->findOneBy(array('businessConfig' => $config , 'id' => $id));
	    $arrs = array('created','sales','commission','Done');
	    if (!empty($purchase) and !empty($purchase->getVendor()) and in_array($purchase->getProcess(),$arrs)) {
            $this->approvePurchaseReturn($purchase);
            return new Response('success');
        } else {
            return new Response('failed');
        }
    }

    public function approvePurchaseReturn(BusinessPurchaseReturn $purchase)
    {
        $em = $this->getDoctrine()->getManager();
        $purchase->setProcess('Approved');
        $em->flush();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getPurchaseUpdateReturnQnt($purchase);
        if($purchase->getBusinessConfig()->isStockHistory() == 1 ){
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->processInsertPurchaseReturnItem($purchase);
        }
        $em->getRepository('AccountingBundle:AccountPurchase')->insertBusinessAccountPurchaseReturn($purchase);

    }

    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction($id)
    {

    	$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseReturn')->findOneBy(array('businessConfig' => $config , 'id' => $id));

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('business_purchase_return'));
    }

    public function vendorSelectAction()
    {
        $config = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->findBy(
            array('globalOption' => $config,'status'=>1)
        );
        $items = array();
        $items[]=array('value' => '','text'=> '-Select Vendor-');
        foreach ($entities as $entity):
            $items[]=array('value' => $entity->getId(),'text'=> $entity->getName());
        endforeach;
        return new JsonResponse($items);
    }

    public function vendorUpdateAction(Request $request,$id)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseReturn')->findOneBy(array('businessConfig' => $config , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessInvoiceReturn entity.');
        }
        $setValue = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->find($data['value']);
        $entity->setVendor($setValue);
        $em->persist($entity);
        $em->flush();
        exit;
    }



}
