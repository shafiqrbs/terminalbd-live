<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturnItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Appstore\Bundle\InventoryBundle\Form\SalesReturnType;
use Symfony\Component\HttpFoundation\Response;

/**
 * SalesReturn controller.
 *
 */
class SalesReturnController extends Controller
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
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('InventoryBundle:SalesReturn')->findWithSearch($this->getUser());
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:SalesReturn:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */
    public function searchAction(Request $request)
    {

        $salesId = $request->request->get('invoice');
        $invoice = $this->get('settong.toolManageRepo')->specialExpClean($salesId);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findOneBy(array( 'inventoryConfig' => $inventory,'invoice' => $invoice ,'process' =>'Done'));
        if(!empty($sales)){
            $em = $this->getDoctrine()->getManager();
            $entity = new SalesReturn();

            $entity->setSales($sales);
            $entity->setInventoryConfig($inventory);
            $entity->setCreatedBy($this->getUser());
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('inventory_salesreturn_edit', array('id' => $entity->getId())));
        }else{
            return $this->redirect($this->generateUrl('inventory_salesreturn'));
        }

    }

    /**
     * Finds and displays a SalesReturn entity.
     *
     */
    public function showAction(SalesReturn $salesReturn )
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('InventoryBundle:SalesReturn:show.html.twig', array(
            'entity'      => $salesReturn->getSales(),
            'salesReturn'      => $salesReturn,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function editAction(SalesReturn $salesReturn)
    {

        if (!$salesReturn) {
            throw $this->createNotFoundException('Unable to find SalesReturn entity.');
        }elseif ($salesReturn->getProcess() == 'complete'){
            return $this->redirect($this->generateUrl('inventory_salesreturn_show',array('id'=>$salesReturn->getId())));
        }

        $editForm = $this->createEditForm($salesReturn);
        return $this->render('InventoryBundle:SalesReturn:new.html.twig', array(
            'entity'      => $salesReturn->getSales(),
            'salesReturn'      => $salesReturn,
            'form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a SalesReturn entity.
    *
    * @param SalesReturn $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SalesReturn $entity)
    {
        $form = $this->createForm(new SalesReturnType(), $entity, array(
            'action' => $this->generateUrl('inventory_salesreturn_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:SalesReturn')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalesReturn entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $mode = $request->request->get('mode');
        $invoice = $request->request->get('salesAdjustmentInvoice');
	    $sales = $em->getRepository('InventoryBundle:Sales')->findOneBy(array('invoice' => $invoice));

	    if ($editForm->isValid() and $mode == 'adjustment') {
            if(!empty($sales)){
                $entity->setSalesAdjustmentInvoice($sales);
            }
            $entity->setMode($mode);
            $entity->setProcess('complete');
            $em->flush();
            $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->updateSalesReturn($entity);
            return $this->redirect($this->generateUrl('inventory_salesreturn_edit', array('id' => $entity->getId())));
        }elseif ($editForm->isValid() and $mode == 'payment') {
		    $entity->setMode($mode);
		    $entity->setProcess('complete');
		    $em->flush();
		    $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->updateSalesReturn($entity);
		    return $this->redirect($this->generateUrl('inventory_salesreturn_edit', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:SalesReturn:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $salesReturn = $request->request->get('salesReturn');
        $item = $request->request->get('item');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $item = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->find($item);
        $salesReturn = $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->find($salesReturn);
        $currentSalesReturnItem = $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->getSalesReturnItemSum($item);
        $totalQnt = $currentSalesReturnItem + $quantity;
        if($totalQnt > $item->getQuantity()){
            return new Response(json_encode(array('success'=>'invalid','message'=>'Sales return item already added')));
        }else{

            $entity = new SalesReturnItem();
            $entity->setSalesReturn($salesReturn);
            $entity->setSalesItem($item);
            $entity->setQuantity($quantity);
            $entity->setPrice($price);
            $entity->setSubTotal($price * $quantity);
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->updateSalesReturn($salesReturn);
            $totalAmount = number_format($entity->getSalesReturn()->getTotal());
            return new Response(json_encode(array('success'=>'success','totalAmount'=> $totalAmount ,'message'=>'Sales return item added successfully')));
        }

        exit;


    }

    /**
     * Deletes a SalesReturnItem entity.
     *
     */
    public function deleteAction(SalesReturn $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales Return entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response(json_encode(array('success'=>'success','message'=>'Cancel done')));
        exit;

    }

    /**
     * Deletes a SalesReturnItem entity.
     *
     */
    public function itemDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:SalesReturnItem')->findOneBy(array('salesItem'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->updateSalesTotalReturnPrice($entity);
        $em->remove($entity);
        $em->flush();
        return new Response(json_encode(array('success'=>'success','message'=>'Cancel done')));
        exit;

    }


	public function approveAction(SalesReturn $entity)
	{

		$em = $this->getDoctrine()->getManager();
		if (!empty($entity) and $entity->getProcess() !='approved') {
			$em = $this->getDoctrine()->getManager();
			$entity->setProcess('approved');
			if($entity->getMode() == 'payment'){
				$journal = $em->getRepository('AccountingBundle:AccountJournal')->insertInventoryAccountSalesReturn($entity);
				$entity->setJournal($journal);
			}
			if($entity->getMode() == 'adjustment') {
				$accountSales = $em->getRepository( 'AccountingBundle:AccountSales' )->insertInventoryAccountSalesReturn( $entity );
				$em->getRepository( 'AccountingBundle:Transaction' )->salesReturnTransaction( $entity, $accountSales );
				$entity->setSalesAccount($accountSales->getAccountRefNo());
			}
			$em->flush();
			$em->getRepository('InventoryBundle:StockItem')->insertSalesReturnStockItem($entity);
			$em->getRepository('InventoryBundle:Item')->getItemSalesReturnUpdate($entity);
			$em->getRepository('InventoryBundle:GoodsItem')->updateInventorySalesReturnItem($entity);

		    return new Response('success');
		} else {
			return new Response('failed');
		}
		exit;

	}

}
