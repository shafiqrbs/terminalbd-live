<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory;
use Appstore\Bundle\AccountingBundle\Form\ExpenseCategoryType;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * ExpenseCategory controller.
 *
 */
class ExpenseCategoryController extends Controller
{

    /**
     * Lists all ExpenseCategory entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:ExpenseCategory')->findBy(array('globalOption' => $option),array( 'parent'=>'asc' , 'name' =>'asc' ));
        $pagination = $this->paginate($entities);
        return $this->render('DoctorPrescriptionBundle:ExpenseCategory:index.html.twig', array(
            'entities' => $pagination,
        ));

    }

    public function paginate($entities)
    {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Creates a new ExpenseCategory entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ExpenseCategory();
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createCreateForm($entity,$globalOption);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('dps_expensecategory_new', array('id' => $entity->getId())));
        }

        return $this->render('DoctorPrescriptionBundle:ExpenseCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ExpenseCategory entity.
     *
     * @param ExpenseCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ExpenseCategory $entity, $globalOption)
    {

        $em = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory');
        $emHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead');
        $form = $this->createForm(new ExpenseCategoryType($em,$emHead,$globalOption), $entity, array(
            'action' => $this->generateUrl('dps_expensecategory_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ExpenseCategory entity.
     *
     */
    public function newAction()
    {
        $entity = new ExpenseCategory();
        $globalOption = $this->getUser()->getGlobalOption();
        $form   = $this->createCreateForm($entity,$globalOption);

        return $this->render('DoctorPrescriptionBundle:ExpenseCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ExpenseCategory entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:ExpenseCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExpenseCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DoctorPrescriptionBundle:ExpenseCategory:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ExpenseCategory entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:ExpenseCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExpenseCategory entity.');
        }
        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);

        return $this->render('DoctorPrescriptionBundle:ExpenseCategory:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a ExpenseCategory entity.
     *
     * @param ExpenseCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ExpenseCategory $entity,$globalOption)
    {
        $em = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory');
        $emHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead');
        $form = $this->createForm(new ExpenseCategoryType($em,$emHead,$globalOption), $entity, array(
            'action' => $this->generateUrl('dps_expensecategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing ExpenseCategory entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:ExpenseCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExpenseCategory entity.');
        }

        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('dps_expensecategory'));
        }

        return $this->render('DoctorPrescriptionBundle:ExpenseCategory:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a ExpenseCategory entity.
     *
     */
    public function deleteAction(ExpenseCategory $entity)
    {
        $em = $this->getDoctrine()->getManager();
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
        return $this->redirect($this->generateUrl('dps_expensecategory'));
    }

    /**
     * Creates a form to delete a ExpenseCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dps_expensecategory_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /**
     * Lists all ExpenseCategory entities.
     *
     */
    public function sortingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AccountingBundle:ExpenseCategory')->findBy(array('level'=> 1 ),array('sorting'=>'asc'));
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:ExpenseCategory:sorting.html.twig', array(
            'entities' => $pagination,
        ));

    }


}
