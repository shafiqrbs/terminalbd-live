<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturn;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessDistributionReturn;
use Appstore\Bundle\BusinessBundle\Form\DistributionReturnType;
use Appstore\Bundle\BusinessBundle\Form\PurchaseType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class DistributionReturnController extends Controller
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
	    $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessDistributionReturnItem')->findWithSearch($config->getId());
	    $todayReturnItems = $this->getDoctrine()->getRepository('BusinessBundle:BusinessDistributionReturnItem')->todayReturnItems($config->getId());
        $remains = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseReturnItem')->remainingStock($config->getId());
        $vendors = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->findBy(['globalOption' => $this->getUser()->getGlobalOption(),'status'=>1]);
        return $this->render('BusinessBundle:DistributionReturn:index.html.twig', array(
            'entities' => $entities,
            'todayReturnItems' => $todayReturnItems,
            'remains' => $remains,
            'vendors' => $vendors,
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

    }


    /**
     * Creates a new DistributionReturn entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        if(!empty($data['vendor']) and !empty($data['grandTotal'])) {
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseReturn')->insertInvoiceDamageItem($this->getUser(), $data);
            return $this->redirect($this->generateUrl('business_purchase_return'));
        }
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param BusinessDistributionReturn $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessDistributionReturn $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new DistributionReturnType($globalOption), $entity, array(
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
        $entity = $em->getRepository('BusinessBundle:BusinessDistributionReturn')->findOneBy(array('businessConfig' => $config , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $particulars = $em->getRepository('BusinessBundle:BusinessPurchaseItem')->getVendorItem($config,$entity->getVendor());

	    return $this->render("BusinessBundle:DistributionReturn:edit.html.twig", array(
            'entity' => $entity,
            'id' => 'purchase',
            'particulars' => $particulars,
        ));
    }


    public function updateAction(Request $request, BusinessDistributionReturn $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $data = $request->request->all();
        $entity->setProcess('Done');
        $em->flush();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessDistributionReturnItem')->insertDistributionReturnItem($entity,$data);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessDistributionReturn')->updatePurchaseTotalPrice($entity);
        return $this->redirect($this->generateUrl('business_purchase_return'));

    }


    /**
     * Finds and displays a BusinessDistributionReturn entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

	    $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $entity = $em->getRepository('BusinessBundle:BusinessDistributionReturn')->findOneBy(array('businessConfig' => $config , 'id' => $id));


	    if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('BusinessBundle:DistributionReturn:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction($id)
    {
        $em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $purchase = $em->getRepository('BusinessBundle:BusinessDistributionReturn')->findOneBy(array('businessConfig' => $config , 'id' => $id));
	    $arrs = array('created','sales','commission');
	    if (!empty($purchase) and !empty($purchase->getVendor()) and in_array($purchase->getProcess(),$arrs)) {
            $em = $this->getDoctrine()->getManager();
            $purchase->setProcess('Approved');
		    $em->flush();
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getPurchaseUpdateReturnQnt($purchase);
            if($config->isStockHistory() == 1 ){
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->processInsertDistributionReturnItem($purchase);
            }
            $em->getRepository('AccountingBundle:AccountPurchase')->insertBusinessAccountDistributionReturn($purchase);
            return new Response('success');
        } else {
            return new Response('failed');
        }
    }

    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction($id)
    {

    	$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessDistributionReturn')->findOneBy(array('businessConfig' => $config , 'id' => $id));

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
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessDistributionReturn')->findOneBy(array('businessConfig' => $config , 'id' => $id));
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
