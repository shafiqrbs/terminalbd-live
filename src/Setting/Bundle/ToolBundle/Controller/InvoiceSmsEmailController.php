<?php

namespace Setting\Bundle\ToolBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmailItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmail;
use Setting\Bundle\ToolBundle\Form\InvoiceSmsEmailType;
use Symfony\Component\HttpFoundation\Response;

/**
 * InvoiceSmsEmail controller.
 *
 */
class InvoiceSmsEmailController extends Controller
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
     * @Secure(roles="ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $option = isset($_REQUEST['option']) ? $_REQUEST['option'] : '';
        if (empty($option) and $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $entities = $em->getRepository('SettingToolBundle:InvoiceSmsEmail')->findBy(array(), array('updated' => 'DESC'));
        }else{
            $option = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->find($option);
            $entities = $em->getRepository('SettingToolBundle:InvoiceSmsEmail')->findBy(array('globalOption' => $option),array('updated'=>'desc'));
        }
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:InvoiceSmsEmail:index.html.twig', array(
            'entities' => $entities,
            'globalOption' => $option,
        ));
    }

    /**
     * Lists all InvoiceSmsEmail entities.
     *
     */
    public function domainAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingToolBundle:InvoiceSmsEmail')->findBy(array('globalOption'=>$globalOption),array('updated'=>'desc'));
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:InvoiceSmsEmail:index.html.twig', array(
            'entities' => $entities,
        ));
    }


     /**
     * Displays a form to create a new InvoiceSmsEmail entity.
     *
     */
    public function newAction(GlobalOption $option)
    {
        $entity = new InvoiceSmsEmail();
        $em = $this->getDoctrine()->getManager();
        $entity->setGlobalOption($option);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('invoicesmsemail_edit', array('invoice' => $entity->getInvoice())));

    }

    /**
     * Finds and displays a InvoiceSmsEmail entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:InvoiceSmsEmail')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }
        return $this->render('SettingToolBundle:InvoiceSmsEmail:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing InvoiceSmsEmail entity.
     *
     */
    public function editAction(InvoiceSmsEmail $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }

        $editForm = $this->createEditForm($entity);
        $sePricing = $this->getDoctrine()->getRepository('SettingToolBundle:SmsEmailPricing')->findBy(array('status'=>1),array('quantity'=>'asc'));

        $itemIds = array();
        foreach ($entity->getInvoiceSmsEmailItems() as $row ){
            $itemIds[] = $row->getSmsEmailPricing()->getId();;
        }

        return $this->render('SettingToolBundle:InvoiceSmsEmail:new.html.twig', array(
            'entity'      => $entity,
            'itemIds'      => $itemIds,
            'packagePricing'      => $sePricing,
            'form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a InvoiceSmsEmail entity.
    *
    * @param InvoiceSmsEmail $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(InvoiceSmsEmail $entity)
    {
        $form = $this->createForm(new InvoiceSmsEmailType(), $entity, array(
            'action' => $this->generateUrl('invoicesmsemail_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing InvoiceSmsEmail entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:InvoiceSmsEmail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $data = $request->request->all();
            $intlMobile = $entity->getPaymentMobile();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
            $entity->setPaymentMobile($mobile);
            if($entity->getTransactionMethod()->getId() > 0){
                $entity->setProcess('In-progress');
            }
            $em->flush();
            $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceSmsEmailItem')->insertItem($entity,$data);
            $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceSmsEmail')->updateInvoice($entity);

            return $this->redirect($this->generateUrl('invoicesmsemail_show', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:InvoiceSmsEmail:show.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a InvoiceSmsEmail entity.
     *
     */
    public function deleteAction(InvoiceSmsEmail $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('invoicesmsemail'));
    }

    /**
     * Deletes a InvoiceSmsEmailItem entity.
     *
     */
    public function deleteItemAction($invoice ,$package)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:InvoiceSmsEmailItem')->findOneBy(array('invoiceSmsEmail'=> $invoice,'smsEmailPricing' => $package));
        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceSmsEmail')->updateInvoice($entity->getInvoiceSmsEmail());
        exit;


    }

    /**
     * Edits an existing InvoiceSmsEmail entity.
     *
     */
    public function paymentAction(Request $request,InvoiceSmsEmail $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $intlMobile = $entity->getPaymentMobile();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
            $entity->setPaymentMobile($mobile);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Data has been updated successfully"
            );
        }
        $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceSmsEmail')->updateInvoice($entity);


        exit;

    }

    /**
     * Deletes a InvoiceSmsEmail entity.
     *
     */
    public function approveAction(InvoiceSmsEmail $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }
        if($entity->getProcess() == 'Pending'){
            $entity->setProcess('In-progress');
        }elseif($entity->getProcess() == 'In-progress'){
            $entity->setProcess('Done');
            $entity->setReceivedBy($this->getUser());
        } if($entity->getProcess() == 'Pending') {
            $entity->setProcess('Done');
            $entity->setReceivedBy($this->getUser());
        }

        $em->getRepository('SettingToolBundle:SmsSenderTotal')->updateSmsBundle($entity);

        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been updated successfully"
        );
        return new Response('success');
    }



}
