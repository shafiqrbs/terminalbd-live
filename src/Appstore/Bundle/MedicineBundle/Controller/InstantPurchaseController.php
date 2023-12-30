<?php

namespace Appstore\Bundle\MedicineBundle\Controller;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Core\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class InstantPurchaseController extends Controller
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
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->findWithInstantItemSearch($config,$data,1);
        $pagination = $this->paginate($entities);
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>1));
        return $this->render('MedicineBundle:InstantPurchase:index.html.twig', array(
            'entities' => $pagination,
            'racks' => $racks,
            'searchForm' => $data,
        ));
    }

    public function returnInstantPurchaseData($salesId){

        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->getInstantPurchaseItem($config);
        $data = '';

        /* @var $instant MedicinePurchaseItem */

        foreach ($invoiceParticulars as $instant){

            $quantity = $instant->getQuantity();
            $remainingQnt = $instant->getRemainingQuantity();
            $purchasePrice = $instant->getPurchasePrice();
            $salesPrice = $instant->getSalesPrice();
            $purchaseSubTotal = $instant->getPurchasePrice()* $instant->getQuantity();
            $subTotal = $instant->getSalesPrice() * $instant->getQuantity();

            $data .="<tr id='removeInstantItem-{$instant->getId()}'>";
            $data .="<td class='numeric'>{$instant->getMedicinePurchase()->getMedicineVendor()->getCompanyName()}</td>";
            $data .="<td class='numeric' >{$instant->getMedicinePurchase()->getCreatedBy()}</td>";
            $data .="<td class='numeric' >{$instant->getMedicinePurchase()->getGrn()}</td>";
            $data .="<td class='numeric' >{$instant->getMedicineStock()->getName()}</td>";
            $data .="<td class='numeric' >{$purchasePrice}</td>";
            $data .="<td class='numeric' >{$purchaseSubTotal}</td>";
            $data .="<td class='numeric' >{$salesPrice}</td>";
            $data .="<td class='numeric' >{$subTotal}</td>";
            $data .="<td class='numeric' >{$quantity}</td>";
            $data .="<td class='numeric' >{$remainingQnt}</td>";
            $data .="<td class='numeric' >";
            $data .='<div class="input-append">';
            $data .="<input type='number' id='quantity-{$instant->getId()}'  style='height: 20px!important;' name='quantity' max='{$instant->getQuantity()}' required='required' class='span5 td-input input-number input' placeholder='quantity'>";
            $data .="<input type='hidden' id='remainingQnt-{$instant->getId()}' name='remainingQnt-{$instant->getId()}' value='{$remainingQnt}'>";
            $data .="<button type='button' class='btn blue mini instantSales' style='height: 25px' data-id='{$instant->getId()}'  data-url='/medicine/sales/{$salesId}/{$instant->getId()}/medicine-instant-sales'> <span class='fa fa-save'></span> Add</button>";
            $data .="<button type='button' class='btn red mini instantDelete' style='height: 25px' id='{$instant->getId()}' data-url='/medicine/instant-purchase/{$instant->getId()}/medicine-instant-purchase-delete'> <span class='fa fa-trash'></span></button>";
            $data .='</div>';
            $data .='</td>';
            $data .='</tr>';
        }
        $data = array(
            'instantPurchaseItem' => $data ,
            'success' => 'success'
        );

        return $data;

    }

    public function addParticularAction(Request $request)
    {
        $user = $this->getUser();
	    $config = $user->getGlobalOption()->getMedicineConfig();
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->insertInstantPurchase($user,$data);
        $purchaseItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->insertPurchaseItems($config,$entity,$data);
	    $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->updatePurchaseTotalPrice($entity);
	    $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->insertInstantSalesItem($data['salesId'],$purchaseItem,$data);
        $result = $this->returnInstantPurchaseData($data['salesId']);
        return new Response(json_encode($result));
        exit;
    }

    public function instantPurchaseLoadAction(MedicineSales $sales)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $instantPurchase = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->getInstantPurchaseItem($config);
        $html = $this->renderView('MedicineBundle:Sales:instant-purchase.html.twig',
            array(
                'instantPurchase' => $instantPurchase,
                'sales' => $sales
            )
        );
        return New Response($html);
    }

    public function instantPurchaseAddAction()
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>'1'));
        $brands = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getBrands($this->getUser()->getGlobalOption()->getMedicineConfig()->getId());
        $html = $this->renderView('MedicineBundle:Sales:instantPurchaseItem.html.twig',
            array( 'racks' => $racks ,'brands' => $brands));
        return New Response($html);
    }

    public function returnTemporaryResultData(User $user,$msg=''){

	    $salesItems = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSalesItems($user);
	    $total = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
	    $subTotal = floor($total['subTotal']);
	    $purchaseSubTotal = floor($total['purchaseSubTotal']);
	    $data = array(
		    'subTotal' => $subTotal,
		    'purchaseSubTotal' => $purchaseSubTotal,
		    'initialGrandTotal' => $subTotal,
		    'discount' => $subTotal,
		    'profit' => ($subTotal - $purchaseSubTotal),
		    'salesItems' => $salesItems ,
		    'msg' => $msg ,
		    'success' => 'success'
	    );
        return $data;

    }


    public function addInstantPurchaseItemAction(Request $request)
    {

        $user = $this->getUser();
	    $config = $user->getGlobalOption()->getMedicineConfig();
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
	    $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->insertInstantPurchase($user,$data);
        $purchaseItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->insertPurchaseItems($config,$entity,$data);
	    $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->updatePurchaseTotalPrice($entity);
	    $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->insertInstantSalesTemporaryItem($this->getUser(),$purchaseItem,$data);
        $result = $this->returnTemporaryResultData($this->getUser());
        return new Response(json_encode($result));
        exit;

    }

    public function instantPurchaseDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->find($id);

        /* @var $item MedicinePurchaseItem */

        if(empty($item->getSalesQuantity())) {
            $this->removeInstantPurchaseItem($item);
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item->getMedicineStock());
        }
        $em->remove($item->getMedicinePurchase());
        $em->flush();
        exit;
    }

    public function removeInstantPurchaseItem(MedicinePurchaseItem $item){
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();
    }




}
