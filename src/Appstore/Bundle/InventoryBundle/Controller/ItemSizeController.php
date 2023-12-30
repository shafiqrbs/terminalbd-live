<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\ItemSizeGroup;
use Appstore\Bundle\InventoryBundle\Form\ItemSizeGroupingType;
use Appstore\Bundle\InventoryBundle\Form\ItemSizeGroupType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\ItemSize;
use Appstore\Bundle\InventoryBundle\Form\ItemSizeType;

/**
 * ItemSize controller.
 *
 */
class ItemSizeController extends Controller
{

    /**
     * Lists all ItemSize entities.
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_MANAGER")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('InventoryBundle:ItemSize')->findBy(array('isValid'=>1),array('code'=>'asc'));
        return $this->render('InventoryBundle:ItemSize:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ItemSize entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ItemSize();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($inventory);
            $entity->setStatus(false);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('itemsize'));
        }

        return $this->render('InventoryBundle:ItemSize:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ItemSize entity.
     *
     * @param ItemSize $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ItemSize $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new ItemSizeType($em,$inventory), $entity, array(
            'action' => $this->generateUrl('itemsize_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ItemSize entity.
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_MANAGER")
     */

    public function newAction()
    {
        $entity = new ItemSize();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:ItemSize:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ItemSize entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemSize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }

        return $this->render('InventoryBundle:ItemSize:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing ItemSize entity.
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemSize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:ItemSize:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ItemSize entity.
    *
    * @param ItemSize $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ItemSize $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new ItemSizeType($em,$inventory), $entity, array(
            'action' => $this->generateUrl('itemsize_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing ItemSize entity.
     * @Secure(roles="ROLE_ADMIN_OPERATION_USER")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemSize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('itemsize_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:ItemSize:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a ItemSize entity.
     * @Secure(roles="ROLE_ADMIN_OPERATION_USER")
     */
    public function deleteAction(ItemSize $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }
        return $this->redirect($this->generateUrl('itemsize'));
    }


    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $item = $this->getDoctrine()->getRepository('InventoryBundle:ItemSize')->searchAutoComplete($item);
        }
        return new JsonResponse($item);
    }

    public function searchItemSizeNameAction($size)
    {
        return new JsonResponse(array(
            'id'=>$size,
            'text'=>$size
        ));
    }

    public function sizeGroupAction(){


        $em = $this->getDoctrine()->getManager();
        $entity = new ItemSizeGroup();
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $array = array();
        $grouping =  $inventoryConfig->getSizeGroup();
        if($grouping){
            $groups = $grouping->getSizes();
            foreach($groups as $row ){
                $array[] = $row->getId();
            }
        }else{
            $entity->setInventoryConfig($inventoryConfig);
            $em->persist($entity);
            $em->flush();
        }

        $sizes = $em->getRepository('InventoryBundle:ItemSize')->findBy(array('isValid'=>1),array('code'=>'asc'));
        if($sizes)
        $entities = $this->getDoctrine()->getRepository('InventoryBundle:ItemSize')->getGroupSizes($sizes,$array);
        $form   = $this->createSizeGroupForm($entity);

        return $this->render('InventoryBundle:ItemSize:group.html.twig', array(
            'entities' => $entities,
            'form'   => $form->createView(),
        ));

    }

    private function createSizeGroupForm(ItemSizeGroup $entity)
    {

        $form = $this->createForm(new ItemSizeGroupType(), $entity, array(
            'action' => $this->generateUrl('itemsize_group_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    public function sizeGroupCreateAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        if($globalOption){
            $entity = $globalOption->getInventoryConfig()->getSizeGroup();
        }else{
            $entity = new ItemSizeGroup();
        }

        $data = $request->request->get('categories');
        $array = array();
        foreach($data as $row){
            $array[] = $em->getRepository('InventoryBundle:ItemSize')->find($row);
        }

        $form = $this->createSizeGroupForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity->setInventoryConfig($this->getUser()->getGlobalOption()->getInventoryConfig());
            $entity->setSizes($array);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('itemsize_group'));
        }


    }
}
