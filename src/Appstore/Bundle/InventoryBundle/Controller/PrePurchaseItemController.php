<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\PrePurchaseItem;
use Appstore\Bundle\InventoryBundle\Form\PrePurchaseItemType;

/**
 * PrePurchaseItem controller.
 *
 */
class PrePurchaseItemController extends Controller
{

    /**
     * Lists all PrePurchaseItem entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('InventoryBundle:PrePurchaseItem')->findBy(array(),array('updated'=>'asc'));
        return $this->render('InventoryBundle:PrePurchaseItem:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new PrePurchaseItem entity.
     *
     */

    public function createAction(Request $request)
    {
        $entity = new PrePurchaseItem();
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
            return $this->redirect($this->generateUrl('prepurchaseitem'));
        }

        return $this->render('InventoryBundle:PrePurchaseItem:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PrePurchaseItem entity.
     *
     * @param PrePurchaseItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PrePurchaseItem $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PrePurchaseItemType($config), $entity, array(
            'action' => $this->generateUrl('prepurchaseitem_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new PrePurchaseItem entity.
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */
    public function newAction()
    {
        $entity = new PrePurchaseItem();
        $form   = $this->createCreateForm($entity);
        return $this->render('InventoryBundle:PrePurchaseItem:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PrePurchaseItem entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PrePurchaseItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PrePurchaseItem entity.');
        }
        return $this->render('InventoryBundle:PrePurchaseItem:show.html.twig', array(
            'entity'      => $entity,

        ));
    }

    /**
     * Displays a form to edit an existing PrePurchaseItem entity.
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PrePurchaseItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PrePurchaseItem entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:PrePurchaseItem:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a PrePurchaseItem entity.
    *
    * @param PrePurchaseItem $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PrePurchaseItem $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PrePurchaseItemType($config), $entity, array(
            'action' => $this->generateUrl('prepurchaseitem_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing PrePurchaseItem entity.
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PrePurchaseItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PrePurchaseItem entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('prepurchaseitem_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:PrePurchaseItem:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    public function statusAction(PrePurchaseItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('prepurchaseitem'));
    }


    /**
     * Deletes a PrePurchaseItem entity.
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */
    public function deleteAction(PrePurchaseItem $entity)
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
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return $this->redirect($this->generateUrl('prepurchaseitem'));
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $item = $this->getDoctrine()->getRepository('InventoryBundle:PrePurchaseItem')->searchAutoComplete($item);
        }
        return new JsonResponse($item);
    }

    public function searchPrePurchaseItemNameAction($color)
    {
        return new JsonResponse(array(
            'id'=>$color,
            'text'=>$color
        ));
    }
}
