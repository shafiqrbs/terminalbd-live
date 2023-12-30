<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Form\BranchInvoiceItemType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\BranchInvoiceItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\InventoryBundle\Entity\BranchInvoice;
use Symfony\Component\HttpFoundation\Response;
use Knp\Snappy\Pdf;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Hackzilla\BarcodeBundle\Utility\Barcode;
/**
 * BranchInvoice controller.
 *
 */
class BranchInvoiceController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function indexAction()
    {



        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:BranchInvoice')->invoiceLists($inventory,$data);
        $pagination = $this->paginate($entities);
        $branches = $em->getRepository('DomainUserBundle:Branches')->findBy(array('globalOption'=>$this->getUser()->getGlobalOption()));
        return $this->render('InventoryBundle:BranchInvoice:index.html.twig', array(
            'entities' => $pagination,
            'branches' => $branches,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sales = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $sales = $em->getRepository('InventoryBundle:BranchInvoice')->find($sales);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->returnPurchaseItemDetails($inventory,$barcode);
        $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:BranchInvoiceItem')->checkPurchaseQuantity($purchaseItem);
        $itemDetails= '';
        $salesItems = '';
        $salesTotal = '';
        if($purchaseItem && $checkQuantity == true ){
            //$itemDetails = $em->getRepository('InventoryBundle:PurchaseItem')->findBarcode($purchaseItem);
            $this->getDoctrine()->getRepository('InventoryBundle:BranchInvoiceItem')->insertBranchInvoiceItems($sales,$purchaseItem);
            $salesTotal = $this->getDoctrine()->getRepository('InventoryBundle:BranchInvoice')->updateBranchInvoiceTotalPrice($sales);
            $salesItems = $em->getRepository('InventoryBundle:BranchInvoiceItem')->getBranchInvoiceItems($sales);
        }else{
            $salesTotal = $this->getDoctrine()->getRepository('InventoryBundle:BranchInvoice')->updateBranchInvoiceTotalPrice($sales);
            $salesItems = $em->getRepository('InventoryBundle:BranchInvoiceItem')->getBranchInvoiceItems($sales);
        }

        //return new Response($salesItems);
        return new Response(json_encode(array('salesTotal'=>$salesTotal,'purchaseItem' => $itemDetails ,'salesItem'=>$salesItems)));
        exit;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $salesItemId = $request->request->get('salesItemId');
        $quantity = $request->request->get('quantity');
        $salesPrice = $request->request->get('salesPrice');
        $customPrice = $request->request->get('customPrice');

        $salesItem = $em->getRepository('InventoryBundle:BranchInvoiceItem')->find($salesItemId);
        $salesItem->setQuantity($quantity);
        $salesItem->setBranchInvoicePrice($salesPrice);
        if (!empty($customPrice)){
            $salesItem->setCustomPrice(1);
        }
        $salesItem->setSubTotal($quantity * $salesPrice);
        $em->persist($salesItem);
        $em->flush();
        $salesTotal = $this->getDoctrine()->getRepository('InventoryBundle:BranchInvoice')->updateBranchInvoiceTotalPrice($salesItem->getBranchInvoice());
        return new Response(json_encode(array('salesTotal'=>$salesTotal)));
        exit;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function newAction()
    {
        $branch = $_REQUEST['branch'];

        $em = $this->getDoctrine()->getManager();
        $branches = $em->getRepository('DomainUserBundle:Branches')->find($branch);
        if(!empty($branches)){
            $entity = new BranchInvoice();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($inventory);
            $entity->setCreatedBy($this->getUser());
            $entity->setBranches($branches);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Invoice has been generated successfully, add your item"
            );
            return $this->redirect($this->generateUrl('inventory_branchinvoice_edit', array('code' => $entity->getInvoice())));
        }else{
            $this->get('session')->getFlashBag()->add(
                'error', "Invoice has not been generated, please select branch "
            );
            return $this->redirect($this->generateUrl('inventory_branch'));

        }


    }

    /**
     * Finds and displays a BranchInvoice entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:BranchInvoice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BranchInvoice entity.');
        }
        return $this->render('InventoryBundle:BranchInvoice:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function editAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:BranchInvoice')->findOneBy(array('invoice'=>$code));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BranchInvoice entity.');
        }
        $branchItem = new BranchInvoiceItem();
        $form = $this->createEditForm($entity,$branchItem);
        return $this->render('InventoryBundle:BranchInvoice:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to edit a BranchInvoice entity.wq
     *
     * @param BranchInvoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BranchInvoice $entity, BranchInvoiceItem $branchItem )
    {
        $form = $this->createForm(new BranchInvoiceItemType(), $branchItem, array(
            'action' => $this->generateUrl('inventory_branchinvoice_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function updateAction(Request $request, BranchInvoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $branchItem = new BranchInvoiceItem();
        $form = $this->createEditForm($entity,$branchItem);
        $form->handleRequest($request);

        $data = $request->request->all();
        $barcode = $data["appstore_bundle_inventorybundle_branchInvoiceItem"]["barcode"];
        $quantity = $data["appstore_bundle_inventorybundle_branchInvoiceItem"]["quantity"];
        $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->findOneBy(array('barcode'=>$barcode));
        if(!empty($purchaseItem) && $quantity > 0 ) {

            if ($form->isValid()) {
                $branchItem->setBranchInvoice($entity);
                $branchItem->setPurchaseItem($purchaseItem);
                $branchItem->setSalesPrice($purchaseItem->getSalesPrice());
                $branchItem->setSubTotal($purchaseItem->getSalesPrice() * $branchItem->getQuantity());
                $em->persist($branchItem);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', "Data has been added successfully"
                );
                $em->getRepository('InventoryBundle:BranchInvoice')->updateBranchInvoiceTotalPrice($entity);
                return $this->redirect($this->generateUrl('inventory_branchinvoice_edit', array('code' => $entity->getInvoice())));
            }
        }

        $this->get('session')->getFlashBag()->add('error', "Invalid barcode,please add correct barcode");
        return $this->render('InventoryBundle:BranchInvoice:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_APPROVE")
     */

    public function approveAction(BranchInvoice $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Invoice has been approved successfully"
            );
        }
        return $this->redirect($this->generateUrl('inventory_branchinvoice'));


    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function deleteAction(BranchInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BranchInvoice entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }

    /**
     * Deletes a BranchInvoiceItem entity.
     *
     */
    public function itemDeleteAction(BranchInvoiceItem $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BranchInvoiceItem entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;

    }

    public function itemPurchaseDetailsAction(Request $request)
    {
        $item = $request->request->get('item');
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $data = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->itemPurchaseDetails($inventory,$item);
        return new Response($data);
    }

    public function pdfAction(BranchInvoice $entity)
    {

        $html = $this->renderView(
            'InventoryBundle:BranchInvoice:pdf.html.twig', array(
                'entity' => $entity,
                'print' => ''
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="invoicePdf.pdf"');
        echo $pdf;

        return new Response('');
    }

    public function invoicePrintAction(BranchInvoice $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BranchInvoice entity.');
        }

        $barCoder = $this->barCoder($entity->getInvoice());
        return $this->render('InventoryBundle:BranchInvoice:pdf.html.twig', array(
            'entity'      => $entity,
            'barCoder'      => $barCoder,
            'print' => '<script>window.print();</script>'
        ));
    }

    public function barCoder($barcoder){

        $barcode = new BarcodeGenerator();
        $barcode->setText($barcoder);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(30);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .='<div class="barcode-block">';
        $data .='<div class="centered">';
        $data .='<img src="data:image/png;base64,'.$code.'" />';
        $data .='</div>';
        $data .='</div>';
        return $data;
    }



}
