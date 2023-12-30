<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\DomainUserBundle\Entity\NotificationConfig;
use Appstore\Bundle\DomainUserBundle\Form\NotificationConfigType;

/**
 * NotificationConfig controller.
 *
 */
class NotificationConfigController extends Controller
{


    /**
     * Lists all NotificationConfig entities.
     */

    public function indexAction()
    {

        $global = $this->getUser()->getGlobalOption();
        $entity = $this->getDoctrine()->getRepository('DomainUserBundle:NotificationConfig')->findOneBy(array('globalOption'=>$global));
        if(empty($entity)){
            $entity = new NotificationConfig();
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($global);
            $em->persist($entity);
            $em->flush();
        }
        $form   = $this->createEditForm($entity);
        return $this->render('DomainUserBundle:NotificationConfig:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to edit a NotificationConfig entity.
    *
    * @param NotificationConfig $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(NotificationConfig $entity)
    {
        $form = $this->createForm(new NotificationConfigType(), $entity, array(
            'action' => $this->generateUrl('domain_notificationconfig_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing NotificationConfig entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:NotificationConfig')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NotificationConfig entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
            $entity->setMobile($mobile);
            $notification = $this->get('settong.toolManageRepo')->specialExpClean($entity->getPaymentNotification());
            $entity->setPaymentNotification($notification);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('domain_notificationconfig'));
        }

        return $this->render('DomainUserBundle:NotificationConfig:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }



}
