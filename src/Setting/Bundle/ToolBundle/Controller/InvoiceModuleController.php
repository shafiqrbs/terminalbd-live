<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\InvoiceModuleItem;
use Setting\Bundle\ToolBundle\Form\InvoicePaymentReceiveType;
use Setting\Bundle\ToolBundle\Form\InvoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\InvoiceModule;
use Symfony\Component\HttpFoundation\Response;


/**
 * InvoiceModule controller.
 *
 */
class InvoiceModuleController extends Controller
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
     * Lists all InvoiceModule entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:InvoiceModule')->findBy(array(),array('updated'=>'DESC'));
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:InvoiceModule:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Lists all InvoiceModule entities.
     *
     */
    public function domainAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingToolBundle:InvoiceModule')->domainInvoice($globalOption);
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:InvoiceModule:domain.html.twig', array(
            'entities' => $entities,
        ));
    }


    public function addInvoiceAction()
    {

        $entity = new InvoiceModule();
        $em = $this->getDoctrine()->getManager();
        $entity->setProcess('In-progress');
        $entity->setCustomInvoice(1);
        $em->persist($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Invoice  has been generated successfully"
        );
        return $this->redirect($this->generateUrl('invoicemodule_modify', array('id' => $entity->getId())));

    }

    public function modifyAction(InvoiceModule $entity)
    {
        $editForm = $this->createEditForm($entity);
        return $this->render('SettingToolBundle:InvoiceModule:edit.html.twig', array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
        ));
    }


    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:InvoiceModule')->find($id);
        $data = $request->request->all();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Theme entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($data['month'] and $data['year']){
                $billMonth =$data['month'].','.$data['year'];
                $entity->setBillMonth($billMonth);
            }
            if ($entity->isCustomInvoice() == 1){
                $entity->setProcess('In-progress');
            }
            $entity->setProcess('Pending');
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceModule')->insertInvoiceModuleItem($entity,$data);
            $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceModule')->updateInvoice($entity);
            return $this->redirect($this->generateUrl('invoicemodule'));
        }

        return $this->render('SettingToolBundle:InvoiceModule:edit.html.twig', array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
        ));
    }

    public function deleteItemAction(InvoiceModule $invoice, InvoiceModuleItem $item)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$item) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }

        $em->remove($item);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Invoice  has been deleted successfully"
        );
        $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceModule')->updateInvoice($invoice);
        return $this->redirect($this->generateUrl('invoicemodule'));
    }

    /**
     * Creates a form to edit a Theme entity.
     *
     * @param Theme $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(InvoiceModule $entity)
    {
        $form = $this->createForm(new InvoiceType(), $entity, array(
            'action' => $this->generateUrl('invoicemodule_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new InvoiceModule entity.
     *
     */
    public function newAction(GlobalOption $option)
    {
        $billMonth = date('F,Y');
        $exits = $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceModule')->findBy(array('billMonth'=>$billMonth,'globalOption'=>$option));
        if(empty($exits)){

            $entity = new InvoiceModule();
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($option);
            $entity->setBillMonth($billMonth);
            $entity->setProcess('Created');
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceModule')->insertInvoiceItem($entity);
            $this->get('session')->getFlashBag()->add(
                'success',"Invoice  has been generated successfully"
            );
            return $this->redirect($this->generateUrl('invoicemodule_edit', array('invoice' => $entity->getInvoice())));

        }else{
            $this->get('session')->getFlashBag()->add(
                'success',"Invoice  has been already created"
            );
            return $this->redirect($this->generateUrl('tools_domain'));
        }


    }

     /**
     * Displays a form to create a new InvoiceModule entity.
     *
     */
    public function editAction(InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        $editForm = $this->createReceivePaymentForm($entity);
        return $this->render('SettingToolBundle:InvoiceModule:new.html.twig', array(
            'entity'      => $entity,
            'form'          => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new InvoiceModule entity.
     *
     */
    public function generateInvoiceAction(InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        $entity->setProcess('Pending');
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceModule')->updateInvoice($entity);
        return $this->redirect($this->generateUrl('invoicemodule_show', array('id' => $entity->getId())));
    }

    /**
     * Finds and displays a InvoiceModule entity.
     *
     */
    public function showAction(InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        $editForm = $this->createReceivePaymentForm($entity);
        return $this->render('SettingToolBundle:InvoiceModule:show.html.twig', array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),

        ));
    }

    /**
     * Creates a form to edit a Theme entity.
     *
     * @param Theme $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createReceivePaymentForm(InvoiceModule $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new InvoicePaymentReceiveType($option), $entity, array(
            'action' => $this->generateUrl('invoicemodule_ajax_payment', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }




    public function deleteAction(InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }

        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Invoice  has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('invoicemodule'));
    }



    /**
     * Edits an existing InvoiceModule entity.
     *
     */
    public function paymentAction(Request $request,InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        $entity->setCreatedBy($this->getUser());
        if($entity->getPaidAmount() >= $entity->getTotalAmount() ){
            $entity->setProcess('Paid');
        }
        $em->flush();
        return $this->redirect($this->generateUrl('invoicemodule_show', array('id' => $entity->getId())));
    }

    /**
     * Deletes a InvoiceModule entity.
     *
     */
    public function approveAction(InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        if($entity->getProcess() == 'Paid'){
            $entity->setProcess('In-progress');
        }elseif($entity->getProcess() == 'In-progress'){
            $entity->setProcess('Done');
            $entity->setReceivedBy($this->getUser());
        }
        $em->flush();
        return new Response('success');
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:InvoiceModuleItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $entity->setAmount($data['value']);
        $em->flush();
        exit;
    }

    public function printAction(InvoiceModule $entity)
    {

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        return $this->render('SettingToolBundle:InvoiceModule:print.html.twig', array(
            'entity'      => $entity
        ));
    }



}
