<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineDamage;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStockAdjustment;
use Appstore\Bundle\MedicineBundle\Form\MedicineDamageType;
use Appstore\Bundle\MedicineBundle\Form\MedicineStockAdjustmentType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Damage controller.
 *
 */
class StockAdjustmentController extends Controller
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
     * Lists all Damage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStockAdjustment')->findWithSearch($config,$data);
	    $pagination = $this->paginate($entities);
	    return $this->render('MedicineBundle:StockAdjustment:index.html.twig', array(
            'entities' => $pagination
        ));
    }

    public function newAction(Request $request){

        $entity = new MedicineStockAdjustment();
        $form = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:StockAdjustment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Damage entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MedicineStockAdjustment();
        $data = $request->request->all();
        /* @var $stock MedicineStock */
        $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($data['adjustment']['medicineStock']);
        if(empty($stock)){
            $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($data['medicineStock']);
        }
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $entity->setMedicineConfig($config);
            $entity->setMedicineStock($stock);
            if($entity->getMode() == "Minus"){
                if($entity->getQuantity() > 0){
                    $entity->setQuantity("-{$entity->getQuantity()}");
                }
                if($entity->getBonus() > 0){
                    $entity->setBonus("-{$entity->getBonus()}");
                }
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('stock_adjustment', array('id' => $entity->getId())));
        }
        return $this->render('MedicineBundle:StockAdjustment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Damage entity.
     *
     * @param Damage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineStockAdjustment $entity)
    {
        $form = $this->createForm(new MedicineStockAdjustmentType(), $entity, array(
            'action' => $this->generateUrl('stock_adjustment_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Deletes a Damage entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineStockAdjustment')->find($id);
        $stock = $entity->getMedicineStock();
        $em->remove($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'damage');
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('stock_adjustment'));
    }

	public function approvedAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $damage = $this->getDoctrine()->getRepository("MedicineBundle:MedicineStockAdjustment")->findOneBy(array('medicineConfig'=>$config,'id'=>$id));
		if (!empty($damage) and ($damage->getProcess() == 'Created')) {
			$em = $this->getDoctrine()->getManager();
			$damage->setProcess('Approved');
			$em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->stockAdjustment($damage->getMedicineStock());
            return new Response('success');
		} else {
			return new Response('failed');
		}
	}

    public function stockSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
        $entities = $em->getRepository('MedicineBundle:MedicineStockAdjustment')->stockAdjustmentReport($config, $data);
        return $this->render('MedicineBundle:StockAdjustment:adjustment.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }


}
