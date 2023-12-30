<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Proxies\__CG__\Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\ItemTypeGrouping;
use Appstore\Bundle\InventoryBundle\Form\ItemTypeGroupingType;

/**
 * ItemTypeGrouping controller.
 *
 */
class ItemTypeGroupingController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            20  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all ItemTypeGrouping entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('InventoryBundle:ItemTypeGrouping')->findAll();
        $entities = $this->paginate($entities);
        return $this->render('InventoryBundle:ItemTypeGrouping:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ItemTypeGrouping entity.
     *
     */
    public function createAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        if($globalOption){
            $entity = $globalOption->getInventoryConfig()->getItemTypeGrouping();
        }else{
            $entity = new ItemTypeGrouping();
        }

        $data = $request->request->get('categories');
        $array = array();
        foreach($data as $row){
            $array[] = $em->getRepository('ProductProductBundle:Category')->find($row);
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity->setInventoryConfig($this->getUser()->getGlobalOption()->getInventoryConfig());
            $entity->setCategories($array);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('itemtypegrouping_edit',array('id'=>$entity->getInventoryConfig()->getId())));
        }


    }

    /**
     * Creates a form to create a ItemTypeGrouping entity.
     *
     * @param ItemTypeGrouping $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ItemTypeGrouping $entity)
    {
        $form = $this->createForm(new ItemTypeGroupingType(), $entity, array(
            'action' => $this->generateUrl('itemtypegrouping_create'),
            'method' => 'POST',
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing ItemTypeGrouping entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new ItemTypeGrouping();
        $inventoryConfig = $em->getRepository('InventoryBundle:InventoryConfig')->find($id);
        $array = array();
        $grouping =  $inventoryConfig->getItemTypeGrouping();
        if($grouping){
            $groups = $grouping->getCategories();
            foreach($groups as $row ){
                $array[] = $row->getId();
            }
        }else{
            $entity->setInventoryConfig($inventoryConfig);
            $em->persist($entity);
            $em->flush();
        }

        $categories = $em->getRepository('ProductProductBundle:Category')->findBy(array('status' => 1,'inventoryConfig'=>NULL,'parent'=>NULL),array('name'=>'asc'));
        if($categories)

        $entities = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getGroupCategories($categories,$array);
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:ItemTypeGrouping:edit.html.twig', array(
            'entities' => $entities,
            'form'   => $form->createView(),
        ));

    }

    /**
    * Creates a form to edit a ItemTypeGrouping entity.
    *
    * @param ItemTypeGrouping $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ItemTypeGrouping $entity)
    {
        $form = $this->createForm(new ItemTypeGroupingType(), $entity, array(
            'action' => $this->generateUrl('itemtypegrouping_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ItemTypeGrouping entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemTypeGrouping')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemTypeGrouping entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('itemtypegrouping_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:ItemTypeGrouping:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ItemTypeGrouping entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InventoryBundle:ItemTypeGrouping')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ItemTypeGrouping entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('itemtypegrouping'));
    }

    /**
     * Creates a form to delete a ItemTypeGrouping entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('itemtypegrouping_delete', array('id' => $id)))
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
            $item = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchVendorNameAction($category)
    {
        return new JsonResponse(array(
            'id'    =>  $category,
            'text'  =>  $category
        ));
    }
}
