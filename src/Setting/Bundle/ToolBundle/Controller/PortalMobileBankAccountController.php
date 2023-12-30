<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\PortalMobileBankAccount;
use Setting\Bundle\ToolBundle\Form\PortalMobileBankAccountType;

/**
 * PortalMobileBankAccount controller.
 *
 */
class PortalMobileBankAccountController extends Controller
{

    /**
     * Lists all PortalMobileBankAccount entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:PortalMobileBankAccount')->findAll();

        return $this->render('SettingToolBundle:PortalMobileBankAccount:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new PortalMobileBankAccount entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new PortalMobileBankAccount();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $intlMobile = $entity->getMobile();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
            $entity->setMobile($mobile);
            $name = $entity->getMobile().','.$entity->getServiceName().','.$entity->getAccountType();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Status has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('mobilebankaccount'));
        }

        return $this->render('SettingToolBundle:PortalMobileBankAccount:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PortalMobileBankAccount entity.
     *
     * @param PortalMobileBankAccount $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PortalMobileBankAccount $entity)
    {
        $form = $this->createForm(new PortalMobileBankAccountType(), $entity, array(
            'action' => $this->generateUrl('mobilebankaccount_create'),
            'method' => 'POST',
        ));
         return $form;
    }

    /**
     * Displays a form to create a new PortalMobileBankAccount entity.
     *
     */
    public function newAction()
    {
        $entity = new PortalMobileBankAccount();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:PortalMobileBankAccount:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PortalMobileBankAccount entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:PortalMobileBankAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PortalMobileBankAccount entity.');
        }
       return $this->render('SettingToolBundle:PortalMobileBankAccount:show.html.twig', array(
            'entity'      => $entity,
       ));
    }

    /**
     * Displays a form to edit an existing PortalMobileBankAccount entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:PortalMobileBankAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PortalMobileBankAccount entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('SettingToolBundle:PortalMobileBankAccount:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PortalMobileBankAccount entity.
    *
    * @param PortalMobileBankAccount $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PortalMobileBankAccount $entity)
    {
        $form = $this->createForm(new PortalMobileBankAccountType(), $entity, array(
            'action' => $this->generateUrl('mobilebankaccount_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing PortalMobileBankAccount entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:PortalMobileBankAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PortalMobileBankAccount entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $intlMobile = $entity->getMobile();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
            $entity->setMobile($mobile);
            $name = $entity->getMobile().','.$entity->getServiceName().','.$entity->getAccountType();
            $entity->setName($name);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('mobilebankaccount_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:PortalMobileBankAccount:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a PortalMobileBankAccount entity.
     *
     */
    public function deleteAction(PortalMobileBankAccount $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PortalMobileBankAccount entity.');
        }

        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('mobilebankaccount'));
    }

    /**
     * Status a PortalMobileBankAccount entity.
     *
     */
    public function statusAction(PortalMobileBankAccount $entity)
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
        return $this->redirect($this->generateUrl('mobilebankaccount'));
    }



}
