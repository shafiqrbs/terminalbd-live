<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\RestaurantDamage;
use Appstore\Bundle\RestaurantBundle\Form\RestaurantDamageType;
use Appstore\Bundle\RestaurantBundle\Form\ResturantDamageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Damage controller.
 *
 */
class DamageController extends Controller
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
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantDamage')->findBy(array('restaurantConfig' => $config),array('created'=>'ASC'));
	    $pagination = $this->paginate($entities);
        return $this->render('RestaurantBundle:Damage:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    public function newAction(Request $request){

        $entity = new RestaurantDamage();
        $form = $this->createCreateForm($entity);
        return $this->render('RestaurantBundle:Damage:new.html.twig', array(
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
        $entity = new RestaurantDamage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
            $entity->setRestaurantConfig($config);
            $entity->setSubTotal($entity->getParticular()->getPurchasePrice() * $entity->getQuantity());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('restaurant_damage', array('id' => $entity->getId())));
        }

        return $this->render('RestaurantBundle:Damage:new.html.twig', array(
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
    private function createCreateForm(RestaurantDamage $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
    	$form = $this->createForm(new ResturantDamageType($config), $entity, array(
            'action' => $this->generateUrl('restaurant_damage_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing Damage entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantDamage')->findBy(array('medicineConfig' => $config),array('companyName'=>'ASC'));

        $entity = $em->getRepository('RestaurantBundle:RestaurantDamage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Damage entity.');
        }

        $editForm = $this->createEditForm($entity);


        return $this->render('RestaurantBundle:Damage:index.html.twig', array(
            'entities'      => $entities,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Damage entity.
    *
    * @param Damage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(RestaurantDamage $entity)
    {
	    $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
    	$form = $this->createForm(new ResturantDamageType($config), $entity, array(
            'action' => $this->generateUrl('restaurant_damage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Damage entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantDamage')->findBy(array('medicineConfig' => $config),array('companyName'=>'ASC'));

        $entity = $em->getRepository('RestaurantBundle:RestaurantDamage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Damage entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            return $this->redirect($this->generateUrl('restaurant_damage'));
        }
        return $this->render('RestaurantBundle:Damage:index.html.twig', array(
            'entities'      => $entities,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Damage entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('RestaurantBundle:RestaurantDamage')->find($id);
        $stock = $entity->getBusinessParticular();
        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('restaurant_damage'));
    }

	public function approvedAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
		$damage = $em->getRepository('RestaurantBundle:RestaurantDamage')->findOneBy(array('restaurantConfig' => $config , 'id' => $id));
		if (!empty($damage) and ($damage->getProcess() == 'Created')) {
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getDamageQnt($damage);
            if($damage->getRestaurantConfig()->isStockHistory() == 1 ) {
                $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantStockHistory')->processInsertDamageItem($damage);
            }
			$em = $this->getDoctrine()->getManager();
			$damage->setProcess('Approved');
			$em->flush();
			return new Response('success');
		} else {
			return new Response('failed');
		}

	}

}
