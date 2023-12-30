<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\Purchase;
use Appstore\Bundle\RestaurantBundle\Entity\PurchaseItem;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Form\PurchaseType;
use Appstore\Bundle\RestaurantBundle\Form\VendorType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class PurchaseController extends Controller
{

    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_DOMAIN_RESTAURANT_MANAGER,ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:Purchase')->findBy(array('restaurantConfig' => $config),array('created'=>'DESC'));
        $pagination = $this->paginate($entities);

        return $this->render('RestaurantBundle:Purchase:index.html.twig', array(
            'entities' => $pagination,
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
        $entity = new Purchase();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity->setRestaurantConfig($config);
        $entity->setCreatedBy($this->getUser());
        $entity->setTransactionMethod($this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->find(1));
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('restaurant_purchase_edit', array('id' => $entity->getId())));

    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Purchase')->findOneBy(array('restaurantConfig' => $config , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $particulars = $em->getRepository('RestaurantBundle:Particular')->getMedicineParticular($config);
        return $this->render('RestaurantBundle:Purchase:new.html.twig', array(
            'entity' => $entity,
            'particulars' => $particulars,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Purchase $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Purchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseType($globalOption), $entity, array(
            'action' => $this->generateUrl('restaurant_purchase_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'posForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function particularSearchAction(Particular $particular)
    {
        $unit = !empty($particular->getUnit() && !empty($particular->getUnit()->getName())) ? $particular->getUnit()->getName():'Unit';

        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'purchasePrice'=> $particular->getPurchasePrice(), 'quantity'=> 1 , 'minimumPrice'=> '', 'instruction'=>'', 'unit'=> $unit)));
    }

    public function returnResultData(Purchase $invoice,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:PurchaseItem')->getPurchaseItems($invoice);
        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
        $netTotal = $invoice->getNetTotal() > 0 ? $invoice->getNetTotal() : 0;
        $due = $invoice->getDue() > 0 ? $invoice->getDue() : 0;
        $discount = $invoice->getDiscount() > 0 ? $invoice->getDiscount() : 0;
        $data = array(
            'subTotal' => $subTotal,
            'grandTotal' => $netTotal,
            'due' => $due,
            'discount' => $discount,
            'invoiceParticulars' => $invoiceParticulars ,
            'msg' => $msg ,
            'success' => 'success'
        );

        return $data;

    }


    public function addParticularAction(Request $request, Purchase $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('RestaurantBundle:PurchaseItem')->insertPurchaseItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:Purchase')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

     }

    public function invoiceParticularDeleteAction(Purchase $invoice, PurchaseItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:Purchase')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));


    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discount = (float)$request->request->get('discount');
        $purchase = $request->request->get('invoice');

        /* @var $entity Purchase */
        $entity = $em->getRepository('RestaurantBundle:Purchase')->find($purchase);
        $total = ($entity->getSubTotal() - $discount);
        $vat = 0;
        if($total > $discount ){

            $entity->setDiscount($discount);
            $entity->setNetTotal($total);
            $entity->setDue($total);
            $em->persist($entity);
            $em->flush();
        }
        $result = $this->returnResultData($entity);
        return new Response(json_encode($result));

    }

    public function updateAction(Request $request, Purchase $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $data = $request->request->all();
            $deliveryDateTime = $data['purchase']['receiveDate'];
            $receiveDate = (new \DateTime($deliveryDateTime));
            $entity->setReceiveDate($receiveDate);
            $entity->setProcess('Done');
            $entity->setDue($entity->getNetTotal() - $entity->getPayment());
            $em->flush();
            return $this->redirect($this->generateUrl('restaurant_purchase_show', array('id' => $entity->getId())));
        }
        return $this->render('RestaurantBundle:Purchase:new.html.twig', array(
            'entity' => $entity,
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

        $entity = $em->getRepository('RestaurantBundle:Purchase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('RestaurantBundle:Purchase:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction(Purchase $purchase)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($purchase) and $purchase->getProcess() == "Done") {
            $em = $this->getDoctrine()->getManager();
            $purchase->setProcess('Approved');
            $purchase->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getPurchaseUpdateQnt($purchase);
            if($purchase->getRestaurantConfig()->isStockHistory() == 1 ) {
                $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantStockHistory')->processInsertPurchaseItem($purchase);
            }
            $em->getRepository('AccountingBundle:AccountPurchase')->insertRestaurantAccountPurchase($purchase);
            return new Response('success');
        } else {
            return new Response('failed');
        }
    }


    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction(Purchase $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('restaurant_purchase'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {


        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('RestaurantBundle:Purchase')->find($id);

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
        return $this->redirect($this->generateUrl('restaurant_purchase'));
    }

    public function reverseAction(Purchase $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('RestaurantBundle:Purchase')->purchaseTransactionReverse($entity);
        $em->getRepository('RestaurantBundle:Purchase')->reversePurchaseParticularUpdate($entity);
        $em = $this->getDoctrine()->getManager();
        $entity->setProcess('Revised');
        $entity->setReceiveDate(NULL);
        $entity->setDue($entity->getNetTotal() - $entity->getPayment());
        $em->flush();
        if($entity->getRestaurantConfig()->isStockHistory() == 1 ) {
            $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantStockHistory')->processReversePurchaseItem($entity);
        }
        return $this->redirect($this->generateUrl('restaurant_purchase_edit',array('id' => $entity->getId())));

    }


}
