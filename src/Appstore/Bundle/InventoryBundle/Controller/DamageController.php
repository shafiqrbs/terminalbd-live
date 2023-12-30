<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Damage;
use Appstore\Bundle\InventoryBundle\Form\DamageType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Damage controller.
 *
 */
class DamageController extends Controller
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
     * Lists all Damage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:Damage')->findWithSearch($inventory,$data);
        $paginate = $this->paginate($entities);
        return $this->render('InventoryBundle:Damage:index.html.twig', array(
            'entities' => $paginate,
        ));
    }

    public function todayDamage()
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $data = array('startDate'=> date('Y-m-d'),'endDate'=> date('Y-m-d'));
        $entities = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:Damage')->findWithSearch($inventory,$data);
        return $entities;
    }

    /**
     * Creates a new Damage entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Damage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            $barcode = $data['appstore_bundle_inventorybundle_Damage']['purchaseItem'];
            $purchaseItem = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->findOneBy(array('barcode'=>$barcode));
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($inventory);
            $entity->setItem($purchaseItem->getItem());
            $entity->setPurchaseItem($purchaseItem);
            $entity->setUnitPrice($purchaseItem->getPurchasePrice());
            $entity->setTotal($purchaseItem->getPurchasePrice() * $entity->getQuantity());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('inventory_damage_new'));

        }
        $todayDamage = $this->todayDamage();
        return $this->render('InventoryBundle:Damage:new.html.twig', array(
            'entity' => $entity,
            'entities' => $todayDamage,
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
    private function createCreateForm(Damage $entity)
    {
        $form = $this->createForm(new DamageType(), $entity, array(
            'action' => $this->generateUrl('inventory_damage_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Damage entity.
     *
     */
    public function newAction()
    {
        $entity = new Damage();
        $form   = $this->createCreateForm($entity);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $data = array('startDate'=> date('Y-m-d'),'endDate'=> date('Y-m-d'));
        $entities = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:Damage')->findWithSearch($inventory,$data);

        $paginate = $this->paginate($entities);
        return $this->render('InventoryBundle:Damage:new.html.twig', array(
            'entity' => $entity,
            'entities' => $paginate,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Damage entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Damage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Damage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:Damage:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Damage entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Damage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Damage entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        $todayDamage = $this->todayDamage();
        return $this->render('InventoryBundle:Damage:new.html.twig', array(
            'entity'      => $entity,
            'entities'      => $todayDamage,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Damage entity.
    *
    * @param Damage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Damage $entity)
    {
        $form = $this->createForm(new DamageType(), $entity, array(
            'action' => $this->generateUrl('inventory_damage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
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

        $entity = $em->getRepository('InventoryBundle:Damage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Damage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('inventory_damage_edit', array('id' => $id)));
        }
        $todayDamage = $this->todayDamage();
        return $this->render('InventoryBundle:Damage:new.html.twig', array(
            'entity'      => $entity,
            'entities'      => $todayDamage,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Damage entity.
     *
     */
    public function deleteAction($id)
    {

            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity = $em->getRepository('InventoryBundle:Damage')->findOneBy(array('inventoryConfig'=>$inventory,'id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Damage entity.');
            }

            $em->remove($entity);
            $em->flush();

           return $this->redirect($this->generateUrl('inventory_damage'));
    }

    /**
     * Creates a form to delete a Damage entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventory_damage_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Damage')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchDamageNameAction($size)
    {
        return new JsonResponse(array(
            'id'=>$size,
            'text'=>$size
        ));
    }

    public function approveAction(Damage $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->insertDamageItem($entity);
            $this->getDoctrine()->getRepository('InventoryBundle:Item')->itemDamageUpdate($entity);
            $this->getDoctrine()->getRepository('InventoryBundle:GoodsItem')->insertInventoryDamageItem($entity);
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertDamageTransaction($entity);

            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }
}
