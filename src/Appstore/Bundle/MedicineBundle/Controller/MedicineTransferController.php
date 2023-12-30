<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineTransfer;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineTransferItem;
use Appstore\Bundle\MedicineBundle\Form\MedicineTransferItemType;
use Appstore\Bundle\MedicineBundle\Form\MedicineTransferType;
use Appstore\Bundle\MedicineBundle\Form\TransferItemType;
use Appstore\Bundle\MedicineBundle\Form\TransferType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * MedicineTransfer controller.
 *
 */
class MedicineTransferController extends Controller
{

    /**
     * Lists all MedicineTransfer entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new MedicineTransfer();
        $form = $this->createCreateForm($entity);
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineTransfer')->findBy(array('medicineConfig' => $config),array('created'=>'ASC'));
        return $this->render('MedicineBundle:MedicineTransfer:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function newAction(){

        $entity = new MedicineTransfer();
        $form = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:MedicineTransfer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MedicineBundle:MedicineTransfer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('MedicineBundle:MedicineTransfer:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Creates a new MedicineTransfer entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MedicineTransfer();
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
            return $this->redirect($this->generateUrl('medicine_transfer_edit', array('id' => $entity->getId())));
        }

        return $this->render('MedicineBundle:MedicineTransfer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MedicineTransfer entity.
     *
     * @param MedicineTransfer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineTransfer $entity)
    {
        $global = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new TransferType($global), $entity, array(
            'action' => $this->generateUrl('medicine_transfer_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing MedicineTransfer entity.
     *
     */
    public function editAction(MedicineTransfer $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $purchaseItem = new MedicineTransferItem();
        $form = $this->createEditForm($entity,$purchaseItem);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineTransfer entity.');
        }
        return $this->render('MedicineBundle:MedicineTransfer:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to edit a MedicineTransfer entity.
    *
    * @param MedicineTransfer $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MedicineTransfer $entity,MedicineTransferItem $purchaseItem)
    {
        $form = $this->createForm(new TransferItemType(), $purchaseItem, array(
            'action' => $this->generateUrl('medicine_transfer_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing MedicineTransfer entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineTransfer')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineTransfer entity.');
        }
        $data = $request->request->all();
        $medicineTransferItem = new MedicineTransferItem();
        $form = $this->createEditForm($entity,$medicineTransferItem);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $medicineTransferItem->setMedicineTransfer($entity);

            $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($data['medicineStock']);
            $medicineTransferItem->setMedicineStock($stock);
          //  $purchaseItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->find($data['medicinePurchaseItem']);
           // $medicineTransferItem->setMedicinePurchaseItem($purchaseItem);
            $medicineTransferItem->setPurchasePrice($stock->getPurchasePrice());
            $medicineTransferItem->setSubTotal($medicineTransferItem->getPurchasePrice() * $medicineTransferItem->getQuantity());
            $em->persist($medicineTransferItem);
            $em->flush();
           // $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'purchase-return');
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'purchase-return');
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineTransfer')->updateMedicineTransferTotalPrice($entity);
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('medicine_transfer_edit',array('id'=>$entity->getId())));
        }
        return $this->render('MedicineBundle:MedicineTransfer:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Deletes a MedicineTransfer entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineTransfer')->find($id);
        $this->allMedicineTransferItemDelete($entity);
	    $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_transfer'));
    }

	public function allMedicineTransferItemDelete(MedicineTransfer $invoice){

		$em = $this->getDoctrine()->getManager();

		/* @var $particular MedicineTransferItem */

		if($invoice->getMedicineTransferItems()){

			foreach ($invoice->getMedicineTransferItems() as $particular){

				$stock = $particular->getMedicineStock();
				$this->get('session')->set('stock', $stock);
				$em->remove($particular);
				$em->flush();
				$stock = $this->get('session')->get('stock');
				$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'purchase-return');

			}

		}


	}



	/**
     * Deletes a MedicineTransfer entity.
     *
     */
    public function deleteItemAction(MedicineTransfer $purchase,$id)
    {

        $em = $this->getDoctrine()->getManager();
	    $config = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
        if($config == $purchase->getMedicineConfig()->getId()){
		    $entity = $em->getRepository('MedicineBundle:MedicineTransferItem')->find($id);
		    $stock = $entity->getMedicineStock();
		    $medicineTransfer = $entity->getMedicineTransfer()->getId();
		    $this->get('session')->set('stock', $stock);
		    $this->get('session')->set('MedicineTransfer', $medicineTransfer);
		    // $purchaseItem = $entity->getMedicinePurchaseItem();
		    $em->remove($entity);
		    $em->flush();
		    $stock = $this->get('session')->get('stock');
		    $medicineTransfer = $this->get('session')->get('MedicineTransfer');
		    // $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'purchase_return');
		    $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'purchase-return');
	        $this->getDoctrine()->getRepository('MedicineBundle:MedicineTransfer')->updateMedicineTransferTotalPrice($purchase);
	        $this->get('session')->getFlashBag()->add(
			    'error',"Data has been deleted successfully"
		    );
        }

        return $this->redirect($this->generateUrl('medicine_transfer_edit',array('id' => $purchase->getId())));
    }

	public function invoiceDiscountUpdateAction(Request $request)
	{

		$em = $this->getDoctrine()->getManager();
		$discountType = $request->request->get('discountType');
		$discountCal = (float)$request->request->get('discount');
		$invoice = $request->request->get('purchase');
		$entity = $em->getRepository('MedicineBundle:MedicineTransfer')->find($invoice);
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

    public function approveAction(MedicineTransfer $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($entity) and $entity->getProcess() != 'approved') {
            $payment = $_REQUEST['payment'];
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $entity->setPayment($payment);
            $entity->setDue($entity->getTotal() - $entity->getPayment());
            $em->flush();
            $accountSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountTransferInvoice($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesGlobalTransaction($accountSales);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;

    }

}
