<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\ItemBrand;
use Appstore\Bundle\InventoryBundle\Form\ItemBrandType;

/**
 * ItemBrand controller.
 *
 */
class ItemBrandController extends Controller
{

    /**
     * Lists all ItemBrand entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:ItemBrand')->findBy(array('inventoryConfig'=>$inventory),array('name'=>'asc'));

        return $this->render('InventoryBundle:ItemBrand:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ItemBrand entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ItemBrand();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($inventory);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('itembrand'));
        }

        return $this->render('InventoryBundle:ItemBrand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ItemBrand entity.
     *
     * @param ItemBrand $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ItemBrand $entity)
    {
        $form = $this->createForm(new ItemBrandType(), $entity, array(
            'action' => $this->generateUrl('itembrand_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ItemBrand entity.
     *
     */
    public function newAction()
    {
        $entity = new ItemBrand();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:ItemBrand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ItemBrand entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemBrand entity.');
        }

       return $this->render('InventoryBundle:ItemBrand:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing ItemBrand entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemBrand entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('InventoryBundle:ItemBrand:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ItemBrand entity.
    *
    * @param ItemBrand $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ItemBrand $entity)
    {
        $form = $this->createForm(new ItemBrandType(), $entity, array(
            'action' => $this->generateUrl('itembrand_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing ItemBrand entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemBrand entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('itembrand_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:ItemBrand:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a ItemBrand entity.
     *
     */
    public function deleteAction(ItemBrand $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand entity.');
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
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }

        return $this->redirect($this->generateUrl('itembrand'));
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:ItemBrand')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchItemBrandNameAction($brand)
    {
        return new JsonResponse(array(
            'id'=> $brand,
            'text'=> $brand
        ));
    }
}
