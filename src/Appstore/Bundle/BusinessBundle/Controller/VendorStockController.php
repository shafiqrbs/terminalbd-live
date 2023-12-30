<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessVendorStock;
use Appstore\Bundle\BusinessBundle\Entity\BusinessVendorStockItem;
use Appstore\Bundle\BusinessBundle\Form\VendorStockType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class VendorStockController extends Controller
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
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStock')->findWithSearch($this->getUser(),$data);
        $pagination = $this->paginate($entities);
        return $this->render('BusinessBundle:VendorStock:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * Lists all Vendor entities.
     *
     */
    public function vendorStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStockItem')->findWithSearch($this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $salesItems = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->getSalesStockItem($pagination,$data);
        $purchaseItems = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->getPurchaseStockItem($pagination,$data);
        $vendors = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->findBy(['globalOption' => $this->getUser()->getGlobalOption()],['companyName'=>'ASC']);
        return $this->render("BusinessBundle:VendorStock:vendor-stock.html.twig", array(
            'pagination' => $pagination,
            'salesItems' => $salesItems,
            'purchaseItems' => $purchaseItems,
            'vendors' => $vendors,
            'searchForm' => $data,
        ));
    }





    /**
     * Creates a new Vendor entity.
     *
     */
    public function createAction(Request $request)
    {

    }


    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new BusinessVendorStock();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity->setBusinessConfig($config);
        $receiveDate = new \DateTime('now');
        $entity->setProcess('Created');
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('business_vendor_stock_edit', array('id' => $entity->getId())));

    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $em->getRepository('BusinessBundle:BusinessVendorStock')->findOneBy(array('businessConfig' => $config , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($config,$type = array('consumable','stock'));
        return $this->render("BusinessBundle:VendorStock:new.html.twig", array(
            'entity' => $entity,
            'id' => 'VendorStock',
            'particulars' => $particulars,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessVendorStock $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new VendorStockType($globalOption), $entity, array(
            'action' => $this->generateUrl('business_vendor_stock_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'VendorStock',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function returnResultData(BusinessVendorStock $invoice,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStockItem')->getVendorStockItems($invoice);
        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
         $data = array(
            'subTotal' => $subTotal,
            'invoiceParticulars' => $invoiceParticulars ,
            'msg' => $msg ,
            'success' => 'success'
        );

        return $data;

    }

    public function particularSearchAction(BusinessParticular $particular)
    {
        $unit = !empty($particular->getUnit() && !empty($particular->getUnit()->getName())) ? $particular->getUnit()->getName():'Unit';
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getSalesPrice(),  'quantity'=> 1 , 'unit'=> $unit)));
    }

    public function addParticularAction(Request $request, BusinessVendorStock $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStockItem')->insertVendorStockItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStock')->updateVendorStockTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    public function invoiceParticularDeleteAction(BusinessVendorStock $invoice, BusinessVendorStockItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStock')->updateVendorStockTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    public function selectGrnAction(AccountVendor $vendor)
    {
        $particular = $_REQUEST['particular'];
        $purchaseItems = $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStockItem')->getDropdownList($vendor,$particular);
        return new Response($purchaseItems);

    }

    public function selectVendorStockAction(BusinessVendorStockItem $item)
    {
        $remin = ($item->getQuantity() - $item->getSalesQuantity());
        return new Response($remin);
    }

    public function updateAction(Request $request, BusinessVendorStock $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $data = $request->request->all();
            $entity->setProcess('Done');
            $em->flush();
            return $this->redirect($this->generateUrl('business_vendor_stock_show', array('id' => $entity->getId())));
        }
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($entity->getBusinessConfig(),$type = array('consumable','stock'));
        return $this->render("BusinessBundle:VendorStock:new.html.twig", array(
            'entity' => $entity,
            'particulars' => $particulars,
            'form' => $editForm->createView(),
        ));
    }


    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $em->getRepository('BusinessBundle:BusinessVendorStock')->findOneBy(array('businessConfig' => $config , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('BusinessBundle:VendorStock:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $vendorStock = $em->getRepository('BusinessBundle:BusinessVendorStock')->findOneBy(array('businessConfig' => $config , 'id' => $id));

        if (!empty($vendorStock) and $vendorStock->getprocess() != 'Approved') {
            $em = $this->getDoctrine()->getManager();
            $vendorStock->setProcess('Approved');
            $em->flush();
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStockItem')->vendorStockItemUpdate($vendorStock);
            return new Response('success');
        } else {
            return new Response('failed');
        }

    }

    public function purchaseProcessAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $vendorStock = $em->getRepository('BusinessBundle:BusinessVendorStock')->findOneBy(array('businessConfig' => $config , 'id' => $id));
        if (!empty($vendorStock)) {
            $em = $this->getDoctrine()->getManager();
            $vendorStock->setProcess('Purchase');
            $em->flush();
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->insertVendorStockLotPurchase($vendorStock);
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
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStock')->findOneBy(array('businessConfig' => $config , 'id' => $id));

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('business_vendor_stock'));
    }

    public function itemApproveAction($id)
    {

        $config = $this->getUser()->getGlobalOption()->getBusinessConfig()->getId();
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStockItem')->findOneBy(array('id' => $id));
        $em = $this->getDoctrine()->getManager();
        if ($entity->getBusinessVendorStock()->getBusinessConfig()->getId() == $config ) {
            $entity->setStatus(true);
            $em->flush();
        }
        exit;
    }

    public function reverseAction($id)
    {

        /*
         * Item Remove Total quantity
         * Stock Details
         * VendorStock Item
         * VendorStock Vendor Item
         * VendorStock
         * Account VendorStock
         * Account Journal
         * Transaction
         * Delete Journal & Account VendorStock
         */
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $VendorStock = $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStock')->findOneBy(array('businessConfig' => $config , 'id' => $id));

        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();
        if($VendorStock->getAsInvestment() == 1 ) {
            $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->removeApprovedBusinessVendorStockJournal($VendorStock);
        }
        $this->getDoctrine()->getRepository('AccountingBundle:AccountVendorStock')->accountBusinessVendorStockReverse($VendorStock);
        $VendorStock->setIsReversed(true);
        $VendorStock->setProcess('created');
        $VendorStock->setApprovedBy(NULL);
        $em->flush();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getVendorStockUpdateQnt($VendorStock);
        $template = $this->get('twig')->render('BusinessBundle:VendorStock:VendorStockReverse.html.twig', array(
            'entity' => $VendorStock,
            'config' => $VendorStock->getBusinessConfig(),
        ));
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessReverse')->VendorStockReverse($VendorStock, $template);
        return $this->redirect($this->generateUrl('business_vendor_stock_edit',array('id' => $VendorStock->getId())));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

    }

    public function autoSearchAction(Request $request)
    {

    }

    public function searchVendorNameAction($vendor)
    {

    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        /* @var $entity BusinessVendorStockItem */
        $entity = $em->getRepository('BusinessBundle:BusinessVendorStockItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find particular entity.');
        }
        $quantity = abs($data['value']);
        $entity->setQuantity($quantity);
        $entity->setSubTotal($quantity * $entity->getPrice());
        $em->flush();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessVendorStock')->updateVendorStockTotalPrice($entity->getBusinessVendorStock());
        return new Response('success');

    }

}
