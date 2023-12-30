<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\SmsEmailPricing;
use Setting\Bundle\ToolBundle\Form\SmsEmailPricingType;

/**
 * SmsEmailPricing controller.
 *
 */
class SmsEmailPricingController extends Controller
{

    /**
     * Lists all SmsEmailPricing entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:SmsEmailPricing')->findAll();

        return $this->render('SettingToolBundle:SmsEmailPricing:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new SmsEmailPricing entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new SmsEmailPricing();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setAmount($entity->getQuantity() * $entity->getPrice());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('smspricing'));
        }

        return $this->render('SettingToolBundle:SmsEmailPricing:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a SmsEmailPricing entity.
     *
     * @param SmsEmailPricing $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SmsEmailPricing $entity)
    {
        $form = $this->createForm(new SmsEmailPricingType(), $entity, array(
            'action' => $this->generateUrl('smspricing_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new SmsEmailPricing entity.
     *
     */
    public function newAction()
    {
        $entity = new SmsEmailPricing();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:SmsEmailPricing:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SmsEmailPricing entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:SmsEmailPricing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SmsEmailPricing entity.');
        }

        return $this->render('SettingToolBundle:SmsEmailPricing:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing SmsEmailPricing entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:SmsEmailPricing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SmsEmailPricing entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingToolBundle:SmsEmailPricing:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a SmsEmailPricing entity.
    *
    * @param SmsEmailPricing $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SmsEmailPricing $entity)
    {
        $form = $this->createForm(new SmsEmailPricingType(), $entity, array(
            'action' => $this->generateUrl('smspricing_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
       return $form;
    }
    /**
     * Edits an existing SmsEmailPricing entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:SmsEmailPricing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SmsEmailPricing entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->setAmount($entity->getQuantity() * $entity->getPrice());
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            return $this->redirect($this->generateUrl('smspricing'));
        }
        return $this->render('SettingToolBundle:SmsEmailPricing:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a SmsEmailPricing entity.
     *
     */
    public function deleteAction(SmsEmailPricing $entity)
    {

            $em = $this->getDoctrine()->getManager();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SmsEmailPricing entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );
            return $this->redirect($this->generateUrl('smspricing'));
    }



    /**
     * Status a Page entity.
     *
     */
    public function statusAction(SmsEmailPricing $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('smspricing'));
    }

}
