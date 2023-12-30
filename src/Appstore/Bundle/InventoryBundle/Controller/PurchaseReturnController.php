<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Proxies\__CG__\Appstore\Bundle\InventoryBundle\Entity\PurchaseReturnItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn;
use Appstore\Bundle\InventoryBundle\Form\PurchaseReturnType;
use Symfony\Component\HttpFoundation\Response;

/**
 * PurchaseReturn controller.
 *
 */
class PurchaseReturnController extends Controller
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
     * Lists all PurchaseReturn entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:PurchaseReturn')->findBy(array('inventoryConfig'=>$inventory),array('updated'=>'DESC'));
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:PurchaseReturn:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * Creates a new PurchaseReturn entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new PurchaseReturn();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_purchasereturn_show', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:PurchaseReturn:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PurchaseReturn entity.
     *
     * @param PurchaseReturn $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PurchaseReturn $entity)
    {
        $form = $this->createForm(new PurchaseReturnType(), $entity, array(
            'action' => $this->generateUrl('inventory_purchasereturn_create'),
            'method' => 'POST',
        ));
         return $form;
    }

    /**
     * Displays a form to create a new PurchaseReturn entity.
     *
     */
    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new PurchaseReturn();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity->setInventoryConfig($inventory);
        $entity->setCreatedBy($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_purchasereturn_edit', array('id' => $entity->getId())));

    }

    /**
     * Finds and displays a PurchaseReturn entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseReturn')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseReturn entity.');
        }
        return $this->render('InventoryBundle:PurchaseReturn:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing PurchaseReturn entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseReturn')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseReturn entity.');
        }

        if($entity->getProcess() == 'approved'){
            return $this->redirect($this->generateUrl('inventory_purchasereturn'));
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:PurchaseReturn:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PurchaseReturn entity.
    *
    * @param PurchaseReturn $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PurchaseReturn $entity)
    {
        $form = $this->createForm(new PurchaseReturnType(), $entity, array(
            'action' => $this->generateUrl('inventory_purchasereturn_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing PurchaseReturn entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseReturn')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseReturn entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setProcess('complete');
            $em->flush();
            return $this->redirect($this->generateUrl('inventory_purchasereturn'));
        }

        return $this->render('InventoryBundle:PurchaseReturn:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a PurchaseReturn entity.
     *
     */
    public function deleteAction(PurchaseReturn $purchaseReturn)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$purchaseReturn) {
            throw $this->createNotFoundException('Unable to find Purchase Return entity.');
        }
        $em->remove($purchaseReturn);
        $em->flush();
        return new Response(json_encode(array('success'=>'success')));
        exit;
    }

    /**
     * Deletes a PurchaseReturnItem entity.
     *
     */
    public function deleteItemAction( PurchaseReturn $purchaseReturn , $purchaseReturnItem)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseReturnItem')->find($purchaseReturnItem);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Purchase Return Item entity.');
        }
        $em->remove($entity);
        $em->flush();

        $total = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseReturn')->updatePurchaseReturnTotalPrice($purchaseReturn);
        $total = $total > 0 ? $total : 0;
        return new Response(json_encode(array('total'=>$total,'success'=>'success')));
        exit;

    }


    public function searchAction(Request $request)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getManager();
        $purchaseReturn = $request->request->get('purchaseReturn');
        $barcode = $request->request->get('barcode');
        $purchaseReturn = $em->getRepository('InventoryBundle:PurchaseReturn')->find($purchaseReturn);
        $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->returnPurchaseItemDetails($inventory,$barcode);
        $purchaseReturnItems = '';
        $total = '';
        $message = '';
        if($purchaseItem){

            $returnItems = $em->getRepository('InventoryBundle:PurchaseReturnItem')->findOneBy(array('purchaseReturn'=>$purchaseReturn));
            if(empty($returnItems)) {
                $this->getDoctrine()->getRepository('InventoryBundle:PurchaseReturn')->insertPurchaseVendor($purchaseReturn, $purchaseItem->getPurchase());
                $this->getDoctrine()->getRepository('InventoryBundle:PurchaseReturnItem')->insertPurchaseReturnsItems($purchaseReturn,$purchaseItem);
                $message ='Data has been inserted successfully';
            }elseif($purchaseReturn->getVendor()->getId() == $purchaseItem->getPurchase()->getVendor()->getId()){
                $this->getDoctrine()->getRepository('InventoryBundle:PurchaseReturnItem')->insertPurchaseReturnsItems($purchaseReturn,$purchaseItem);
                $message ='Data has been inserted successfully';
            }else{
                $message ='Please ensure adding same vendor for return item';
            }

            $total = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseReturn')->updatePurchaseReturnTotalPrice($purchaseReturn);
            $purchaseReturnItems = $em->getRepository('InventoryBundle:PurchaseReturnItem')->getPurchaseReturnItems($purchaseReturn);
            return new Response(json_encode(array('message' => $message, 'total'=> $total , 'purchaseReturnItem' => $purchaseReturnItems)));
        }else{
            return new Response(json_encode(array('message' => 'failed')));
        }
        exit;
    }

    public function purchaseReturnItemUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $itemId = $request->request->get('itemId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');

        $item = $em->getRepository('InventoryBundle:PurchaseReturnItem')->find($itemId);
        $item->setQuantity($quantity);
        $item->setPrice($price);
        $item->setSubTotal($quantity * $price);
        $em->persist($item);
        $em->flush();
        $total = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseReturn')->updatePurchaseReturnTotalPrice($item->getPurchaseReturn());
        return new Response(json_encode(array('total'=>$total)));
        exit;

    }


    public function approveAction(Request $request,PurchaseReturn $purchaseReturn)
    {

        $em = $this->getDoctrine()->getManager();
        $adjustmentInvoice = $_REQUEST['adjustmentInvoice'];
        $purchase = $em->getRepository('InventoryBundle:Purchase')->findOneBy(array('grn'=>$adjustmentInvoice));
        if(!empty($purchase))
        {
            $purchaseReturn->setPurchase($purchase);
            $purchaseReturn->setProcess('approved');
            $em->persist($purchaseReturn);
            $em->flush();
            $em->getRepository('InventoryBundle:StockItem')->insertPurchaseReturnStockItem($purchaseReturn);
            $em->getRepository('InventoryBundle:Item')->getItemPurchaseReturn($purchaseReturn);
            $em->getRepository('InventoryBundle:GoodsItem')->updateInventoryPurchaseReturnItem($purchaseReturn);
            $accountPurchaseReturn = $em->getRepository('AccountingBundle:AccountPurchaseReturn')->insertAccountPurchaseReturn($purchaseReturn);
            $em->getRepository('AccountingBundle:Transaction')->purchaseReturnTransaction($purchaseReturn,$accountPurchaseReturn);
            return new Response('success');
        }else{
            return new Response('failed');
        }

    }



}
