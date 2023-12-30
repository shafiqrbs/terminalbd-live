<?php

namespace Setting\Bundle\ToolBundle\Controller;

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
use Setting\Bundle\ToolBundle\Form\SizeType;

/**
 * ItemSize controller.
 *
 */
class SizeController extends Controller
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
     * Lists all ItemSize entities.
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('InventoryBundle:ItemSize')->findBy(array('isValid'=>1),array('code'=>'asc'));
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:Size:index.html.twig', array(
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
            $entity->setStatus(false);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('size'));
        }

        return $this->render('SettingToolBundle:Size:new.html.twig', array(
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
        $form = $this->createForm(new SizeType(), $entity, array(
            'action' => $this->generateUrl('size_create'),
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
     * @Secure(roles="ROLE_ADMIN")
     */

    public function newAction()
    {
        $entity = new ItemSize();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:Size:new.html.twig', array(
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

        $entity = $em->getRepository('InventoryBundle:Size')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }

        return $this->render('SettingToolBundle:Size:show.html.twig', array(
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
        return $this->render('SettingToolBundle:Size:new.html.twig', array(
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
         $form = $this->createForm(new SizeType(), $entity, array(
            'action' => $this->generateUrl('size_update', array('id' => $entity->getId())),
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
     * @Secure(roles="ROLE_ADMIN")
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
            return $this->redirect($this->generateUrl('size_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:Size:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a ItemSize entity.
     * @Secure(roles="ROLE_ADMIN")
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
        return $this->redirect($this->generateUrl('size'));
    }

    public function autoInsertAction()
    {

        $a = 0;
        $em = $this->getDoctrine()->getManager();
        for( $i = 0; $i < 200; $i++ ) {
            $a += 5;
            $entity = $em->getRepository('InventoryBundle:ItemSize')->findOneBy(array('isValid'=>1,'name'=>$a));
            if(empty($entity)) {
                $entity = new ItemSize();
                $entity->setName($a);
                $entity->setStatus(true);
                $entity->setIsValid(true);
                $em->persist($entity);
            }
            $em->flush();
        }



    }


}
