<?php
namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProduction;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionElement;
use Appstore\Bundle\BusinessBundle\Form\PreproductionType;
use Appstore\Bundle\BusinessBundle\Form\ProductionType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * ProductionController controller.
 *
 */
class ProductionController extends Controller
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
	 * Lists all Particular entities.
	 * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
	 */
	public function indexAction()
	{

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
	//	$data['type']= 7;
		$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
		$entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessProduction')->findWithSearch($config,$data);
		$pagination = $this->paginate($entities);
		$type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
		$category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('businessConfig'=>$config,'status'=>1));
		return $this->render('BusinessBundle:Production:index.html.twig', array(
			'pagination' => $pagination,
			'types' => $type,
			'categories' => $category,
			'config' => $config,
			'searchForm' => $data,
		));

	}
	/**
     * Creates a form to edit a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createProductionCostingForm(BusinessParticular $entity)
    {

        $form = $this->createForm(new ProductionType(), $entity, array(
            'action' => $this->generateUrl('business_production_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
                'id' => 'production',
            )
        ));
        return $form;
    }



    public function postProductionAction(BusinessParticular  $entity)
    {


    	$em = $this->getDoctrine()->getManager();
        $config =$this->getUser()->getGlobalOption()->getBusinessConfig()->getId();
        if($entity->getBusinessParticularType()->getSlug() != 'post-production' and $entity->getBusinessConfig()->getId() != $config){
            return $this->redirect($this->generateUrl('business_stock'));
        }
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $editForm = $this->createProductionCostingForm($entity);
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($config,$type = array('consumable','stock'));
        return $this->render('BusinessBundle:Production:production.html.twig', array(
            'entity'      => $entity,
            'particulars' => $particulars,
            'form'   => $editForm->createView(),
        ));
    }

    public function productionUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createProductionCostingForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {
            $entity->setSalesPrice($data['productionSalesPrice']);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('business_stock'));
        }

        return $this->render('BusinessBundle:Production:production.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


	public function preProductionAction(BusinessParticular  $item){

		$entity = new BusinessProduction();
    	$em = $this->getDoctrine()->getManager();
		$config =$this->getUser()->getGlobalOption()->getBusinessConfig()->getId();
		if($item->getBusinessParticularType()->getSlug() != 'pre-production' and $item->getBusinessConfig()->getId() != $config){
			return $this->redirect($this->generateUrl('business_stock'));
		}
		$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
		$editForm = $this->createPreProductionForm($entity,$item);
		$particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($config,$type = array('consumable','stock'));
		return $this->render('BusinessBundle:Production:pre-production.html.twig', array(
			'entity'      => $item,
			'particulars' => $particulars,
			'form'   => $editForm->createView(),
		));
	}

	/**
	 * Creates a form to edit a Particular entity.
	 *
	 * @param Particular $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createPreProductionForm(BusinessProduction $entity, BusinessParticular $item)
	{

		$form = $this->createForm(new PreproductionType(), $entity, array(
			'action' => $this->generateUrl('business_pre_production_create', array('item' => $item->getId())),
			'method' => 'POST',
			'attr' => array(
				'class' => 'form-horizontal',
				'novalidate' => 'novalidate',
				'id' => 'production',
			)
		));
		return $form;
	}

	private function updatePreProductionForm(BusinessParticular $entity)
	{

		$form = $this->createForm(new PreproductionType(), $entity, array(
			'action' => $this->generateUrl('business_pre_production_update', array('id' => $entity->getId())),
			'method' => 'POST',
			'attr' => array(
				'class' => 'form-horizontal',
				'novalidate' => 'novalidate',
				'id' => 'production',
			)
		));
		return $form;
	}

	public function preProductionCreateAction(BusinessParticular $item , Request $request)
    {
        $em = $this->getDoctrine()->getManager();
	    $entity = new BusinessProduction();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createPreProductionForm($entity,$item);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        $productionElementPrice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessProductionElement')->getProductPurchaseSalesPrice($item);
        if ($editForm->isValid()) {
	        $entity->setBusinessParticular($item);
	        $entity->setPurchasePrice($productionElementPrice['purchasePrice']);
	        $entity->setSalesPrice($productionElementPrice['salesPrice']);
	        $entity->setQuantity($data['productionQuantity']);
	        $entity->setPurchaseSubTotal($productionElementPrice['purchasePrice'] * $data['productionQuantity']);
	        $entity->setSalesSubTotal($productionElementPrice['salesPrice'] * $data['productionQuantity']);
	        $em->persist($entity);
	        $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('business_production'));
        }

        return $this->render('BusinessBundle:Production:production.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

	public function preProductionUpdateAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$entity = new BusinessProduction();
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Particular entity.');
		}
		$editForm = $this->updatePreProductionForm($entity);
		$editForm->handleRequest($request);
		$data = $request->request->all();
		if ($editForm->isValid()) {
			$particular = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->find($data['particularId']);
            $productionElementPrice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessProductionElement')->getProductPurchaseSalesPrice($particular);
			$entity->setBusinessParticular($particular);
			$entity->setPurchasePrice($data['purchasePrice']);
            $entity->setPurchasePrice($productionElementPrice['purchasePrice']);
            $entity->setSalesPrice($productionElementPrice['salesPrice']);
            $entity->setQuantity($data['quantity']);
            $entity->setPurchaseSubTotal($productionElementPrice['purchasePrice'] * $data['quantity']);
            $entity->setSalesSubTotal($productionElementPrice['salesPrice'] * $data['quantity']);
			$em->persist($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been updated successfully"
			);
			return $this->redirect($this->generateUrl('business_production'));
		}

		return $this->render('BusinessBundle:Production:production.html.twig', array(
			'entity'      => $entity,
			'form'   => $editForm->createView(),
		));
	}


    public function particularSearchAction(BusinessParticular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getSalesPrice() , 'purchasePrice'=> $particular->getPurchasePrice(), 'quantity'=> 1 , 'minimumPrice'=> '')));
    }

    public function addParticularAction(Request $request, BusinessParticular $particular)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $data = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessProductionElement')->insertProductionElement($particular, $data);
	    $particularReturn = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->updateSalesPrice($particular);
        $production = $this->getDoctrine()->getRepository('BusinessBundle:BusinessProductionElement')->particularProductionElements($particular);
        return new Response(json_encode(array('subTotal' => $particularReturn->getSalesPrice(),'purchasePrice' => $particular->getPurchasePrice(),'particulars' => $production)));

    }

    public function deleteAction(BusinessParticular $particular , $id){

        $em = $this->getDoctrine()->getManager();
        $element = $this->getDoctrine()->getRepository('BusinessBundle:BusinessProductionElement')->find($id);
        if (!$element) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($element);
        $em->flush();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->updateSalesPrice($particular);
        exit;
    }

    public function showAction($id)
    {
	    $em = $this->getDoctrine()->getManager();
	    $entity = $em->getRepository('BusinessBundle:BusinessProduction')->find($id);
	    return $this->render('BusinessBundle:Production:show.html.twig', array(
		    'entity'      => $entity,
	    ));
    }

	public function approvedAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$production = $em->getRepository('BusinessBundle:BusinessProduction')->find($id);
		if (!empty($production) and ($production->getProcess() == 'created')) {
			$production->setProcess('approved');
			$em->flush();
			$this->getDoctrine()->getRepository('BusinessBundle:BusinessProduction')->insertStockProductionItem($production);
			return new Response('success');
		} else {
			return new Response('failed');
		}
		exit;
	}

}
