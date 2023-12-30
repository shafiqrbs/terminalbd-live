<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\DeliveryReturn;
use Appstore\Bundle\InventoryBundle\Form\DeliveryReturnType;
use Symfony\Component\HttpFoundation\Response;

/**
 * DeliveryReturn controller.
 *
 */
class DeliveryReturnController extends Controller
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
     * Lists all DeliveryReturn entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:DeliveryReturn')->findWithSearch($user,$data);
        $paginate = $this->paginate($entities);
        return $this->render('InventoryBundle:DeliveryReturn:index.html.twig', array(
            'entities' => $paginate,
            'searchForm' => $data
        ));
    }

    public function todayDeliveryReturn()
    {
        $data = array('startDate'=> date('Y-m-d'),'endDate'=> date('Y-m-d'));
        $entities = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:DeliveryReturn')->findWithSearch( $this->getUser() ,$data);
        if($entities){
            $paginate = $this->paginate($entities);
            return $paginate;
        }else{
            return false;
        }

    }

    /**
     * Creates a new DeliveryReturn entity.
     *
     */
    public function createAction(Request $request)
    {

        $branch = $this->getUser()->getBranches();
        $entity = new DeliveryReturn();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        $barcode = trim($data['appstore_bundle_inventorybundle_delivery_return']['purchaseItem']);
        $purchaseItem = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->findOneBy(array('barcode' => $barcode));

         if ($form->isValid() and !empty($purchaseItem)) {

             $deliveryItem = $this->getDoctrine()->getRepository('InventoryBundle:DeliveryItem')->checkItem($this->getUser(),$purchaseItem,$entity->getQuantity());
             if(!empty($deliveryItem) and $deliveryItem == 'valid') {

                $em = $this->getDoctrine()->getManager();
                $entity->setInventoryConfig($this->getUser()->getGlobalOption()->getInventoryConfig());
                $entity->setBranch($branch);
                $entity->setItem($purchaseItem->getItem());
                $entity->setPurchaseItem($purchaseItem);
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', "Data has been added successfully"
                );
                return $this->redirect($this->generateUrl('inventory_deliveryreturn_new'));

            }else{

                $this->get('session')->getFlashBag()->add(
                    'error', "Your current branch stock ".$deliveryItem
                );

                $todayDeliveryReturn = $this->todayDeliveryReturn();
                return $this->render('InventoryBundle:DeliveryReturn:new.html.twig', array(
                    'entity' => $entity,
                    'entities' => $todayDeliveryReturn,
                    'form'   => $form->createView(),
                ));
            }

        }
        $this->get('session')->getFlashBag()->add(
            'error', "This barcode not found at delivery invoice in branch"
        );
        $todayDeliveryReturn = $this->todayDeliveryReturn();
        return $this->render('InventoryBundle:DeliveryReturn:new.html.twig', array(
            'entity' => $entity,
            'entities' => $todayDeliveryReturn,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a DeliveryReturn entity.
     *
     * @param DeliveryReturn $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DeliveryReturn $entity)
    {
        $form = $this->createForm(new DeliveryReturnType(), $entity, array(
            'action' => $this->generateUrl('inventory_deliveryreturn_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new DeliveryReturn entity.
     *
     */
    public function newAction()
    {
        $entity = new DeliveryReturn();
        $form   = $this->createCreateForm($entity);
        $entities = $this->todayDeliveryReturn();
        return $this->render('InventoryBundle:DeliveryReturn:new.html.twig', array(
            'entity' => $entity,
            'entities' => $entities,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DeliveryReturn entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:DeliveryReturn')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DeliveryReturn entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:DeliveryReturn:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DeliveryReturn entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:DeliveryReturn')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DeliveryReturn entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        $todayDeliveryReturn = $this->todayDeliveryReturn();
        return $this->render('InventoryBundle:DeliveryReturn:new.html.twig', array(
            'entity'      => $entity,
            'entities'      => $todayDeliveryReturn,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a DeliveryReturn entity.
    *
    * @param DeliveryReturn $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(DeliveryReturn $entity)
    {
        $form = $this->createForm(new DeliveryReturnType(), $entity, array(
            'action' => $this->generateUrl('inventory_deliveryreturn_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing DeliveryReturn entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:DeliveryReturn')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DeliveryReturn entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('inventory_deliveryreturn_edit', array('id' => $id)));
        }
        $todayDeliveryReturn = $this->todayDeliveryReturn();
        return $this->render('InventoryBundle:DeliveryReturn:new.html.twig', array(
            'entity'      => $entity,
            'entities'      => $todayDeliveryReturn,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a DeliveryReturn entity.
     *
     */
    public function deleteAction($id)
    {

            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity = $em->getRepository('InventoryBundle:DeliveryReturn')->findOneBy(array('inventoryConfig'=>$inventory,'id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find DeliveryReturn entity.');
            }

            $em->remove($entity);
            $em->flush();

           return $this->redirect($this->generateUrl('inventory_deliveryreturn'));
    }

    /**
     * Creates a form to delete a DeliveryReturn entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventory_deliveryreturn_delete', array('id' => $id)))
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
            $item = $this->getDoctrine()->getRepository('InventoryBundle:DeliveryReturn')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchDeliveryReturnNameAction($size)
    {
        return new JsonResponse(array(
            'id'=>$size,
            'text'=>$size
        ));
    }

    public function approveAction(DeliveryReturn $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }
}
