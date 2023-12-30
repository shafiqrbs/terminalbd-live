<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseReturn;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseReturnItem;
use Appstore\Bundle\MedicineBundle\Form\PurchaseReturnItemType;
use Appstore\Bundle\MedicineBundle\Form\PurchaseReturnType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * PurchaseReturn controller.
 *
 */
class MedicinePurchaseReturnController extends Controller
{

    /**
     * Lists all PurchaseReturn entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new MedicinePurchaseReturn();
        $form = $this->createCreateForm($entity);
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseReturn')->findBy(array('medicineConfig' => $config),array('created'=>'ASC'));
        return $this->render('MedicineBundle:PurchaseReturn:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function newAction(){

        $entity = new MedicinePurchaseReturn();
        $form = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:PurchaseReturn:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MedicineBundle:MedicinePurchaseReturn')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('MedicineBundle:PurchaseReturn:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Creates a new PurchaseReturn entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MedicinePurchaseReturn();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $entity->setMedicineConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('medicine_purchase_return_edit', array('id' => $entity->getId())));
        }

        return $this->render('MedicineBundle:PurchaseReturn:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PurchaseReturn entity.
     *
     * @param MedicinePurchaseReturn $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicinePurchaseReturn $entity)
    {
        $global = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseReturnType($global), $entity, array(
            'action' => $this->generateUrl('medicine_purchase_return_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing PurchaseReturn entity.
     *
     */
    public function editAction(MedicinePurchaseReturn $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $purchaseItem = new MedicinePurchaseReturnItem();
        $form = $this->createEditForm($entity,$purchaseItem);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseReturn entity.');
        }
        return $this->render('MedicineBundle:PurchaseReturn:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to edit a PurchaseReturn entity.
    *
    * @param PurchaseReturn $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MedicinePurchaseReturn $entity,MedicinePurchaseReturnItem $purchaseItem)
    {
        $form = $this->createForm(new PurchaseReturnItemType(), $purchaseItem, array(
            'action' => $this->generateUrl('medicine_purchase_return_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
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
        $entity = $em->getRepository('MedicineBundle:MedicinePurchaseReturn')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseReturn entity.');
        }
        $data = $request->request->all();
        $purchaseReturnItem = new MedicinePurchaseReturnItem();
        $form = $this->createEditForm($entity,$purchaseReturnItem);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $purchaseReturnItem->setMedicinePurchaseReturn($entity);

            $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($data['medicineStock']);
            $purchaseReturnItem->setMedicineStock($stock);
          //  $purchaseItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->find($data['medicinePurchaseItem']);
           // $purchaseReturnItem->setMedicinePurchaseItem($purchaseItem);
            $purchaseReturnItem->setPurchasePrice($stock->getPurchasePrice());
            $purchaseReturnItem->setSubTotal($purchaseReturnItem->getPurchasePrice() * $purchaseReturnItem->getQuantity());
            $em->persist($purchaseReturnItem);
            $em->flush();
           // $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'purchase-return');
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'purchase-return');
            $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseReturn')->updatePurchaseReturnTotalPrice($entity);
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('medicine_purchase_return_edit',array('id'=>$entity->getId())));
        }
        return $this->render('MedicineBundle:PurchaseReturn:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Deletes a PurchaseReturn entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicinePurchaseReturn')->find($id);
        $this->allPurchaseReturnItemDelete($entity);
	    $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_purchase_return'));
    }

	public function allPurchaseReturnItemDelete(MedicinePurchaseReturn $invoice){

		$em = $this->getDoctrine()->getManager();

		/* @var $particular MedicinePurchaseReturnItem */

		if(!empty($invoice->getMedicinePurchaseReturnItems())){

			foreach ($invoice->getMedicinePurchaseReturnItems() as $particular){

				//$item = $particular->getMedicinePurchaseItem();
				$stock = $particular->getMedicineStock();
				//$this->get('session')->set('item', $item);
				$this->get('session')->set('stock', $stock);
				$em->remove($particular);
				$em->flush();
				//$item = $this->get('session')->get('item');
				$stock = $this->get('session')->get('stock');
				//$this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($item,'purchase_return');
				$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'purchase-return');

			}

		}


	}



	/**
     * Deletes a PurchaseReturn entity.
     *
     */
    public function deleteItemAction(MedicinePurchaseReturn $purchase,$id)
    {

        $em = $this->getDoctrine()->getManager();
	    $config = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
        if($config == $purchase->getMedicineConfig()->getId()){
		    $entity = $em->getRepository('MedicineBundle:MedicinePurchaseReturnItem')->find($id);
		    $stock = $entity->getMedicineStock();
		    $purchaseReturn = $entity->getMedicinePurchaseReturn()->getId();
		    $this->get('session')->set('stock', $stock);
		    $this->get('session')->set('purchaseReturn', $purchaseReturn);
		    // $purchaseItem = $entity->getMedicinePurchaseItem();
		    $em->remove($entity);
		    $em->flush();
		    $stock = $this->get('session')->get('stock');
		    $purchaseReturn = $this->get('session')->get('purchaseReturn');
		    // $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'purchase_return');
		    $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'purchase-return');
	        $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseReturn')->updatePurchaseReturnTotalPrice($purchase);
	        $this->get('session')->getFlashBag()->add(
			    'error',"Data has been deleted successfully"
		    );
        }

        return $this->redirect($this->generateUrl('medicine_purchase_return_edit',array('id' => $purchase->getId())));
    }

	public function invoiceDiscountUpdateAction(Request $request)
	{

		$em = $this->getDoctrine()->getManager();
		$discountType = $request->request->get('discountType');
		$discountCal = (float)$request->request->get('discount');
		$invoice = $request->request->get('purchase');
		$entity = $em->getRepository('MedicineBundle:MedicinePurchaseReturn')->find($invoice);
		$subTotal = $entity->getSubTotal();
		if($discountCal > 0){
			if($discountType == 'flat'){
				$total = ($subTotal  - $discountCal);
				$discount = $discountCal;
			}else{
				$discount = ($subTotal * $discountCal)/100;
				$total = ($subTotal  - $discount);
			}
			if($subTotal > $discount ){
				$entity->setDiscountType($discountType);
				$entity->setDiscountCalculation($discountCal);
				$entity->setDiscount(round($discount));
				$entity->setTotal(round($total));

			}
		}else{
			$entity->setDiscountType('percentage');
			$entity->setDiscountCalculation(0);
			$entity->setDiscount(0);
			$entity->setTotal($entity->getSubTotal());
		}
		$em->flush();
		$data = array(
			'subTotal' => $entity->getSubTotal(),
			'total' => $entity->getTotal(),
			'discount' => $entity->getDiscount(),

		);
		return new Response(json_encode($data));
		exit;
	}

    public function approveAction(MedicinePurchaseReturn $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($entity) and $entity->getProcess() != "approved") {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->insertMedicineAccountPurchaseReturn($entity);
            $em->getRepository('AccountingBundle:Transaction')->purchaseReturnGlobalTransaction($accountPurchase);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;

    }

}
