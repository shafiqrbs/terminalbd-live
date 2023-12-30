<?php

namespace Setting\Bundle\AppearanceBundle\Controller;


use Setting\Bundle\AppearanceBundle\Form\SidebarWidgetPanelType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\AppearanceBundle\Entity\SidebarWidget;
use Setting\Bundle\AppearanceBundle\Form\SidebarWidgetType;

/**
 * SidebarWidget controller.
 *
 */
class SidebarWidgetController extends Controller
{

    /**
     * Lists all SidebarWidget entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:SidebarWidget')->findBy(array());
        return $this->render('SettingAppearanceBundle:SidebarWidget:index.html.twig', array(
            'entities' => $entities,
        ));
    }


    /**
     * Creates a new SidebarWidget entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new SidebarWidget();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('sidebarwidget_edit', array('id' => $entity->getId())));
        }

        return $this->render('SettingAppearanceBundle:SidebarWidget:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a SidebarWidget entity.
     *
     * @param SidebarWidget $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SidebarWidget $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new SidebarWidgetType($globalOption,$em), $entity, array(
            'action' => $this->generateUrl('sidebarwidget_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new SidebarWidget entity.
     *
     */
    public function newAction()
    {
        $entity = new SidebarWidget();
        $form   = $this->createCreateForm($entity);
        return $this->render('SettingAppearanceBundle:SidebarWidget:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing SidebarWidget entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:SidebarWidget')->find($id);
        $option = $this->getUser()->getGlobalOption();
        $sidebarWidgetPanel = $em->getRepository('SettingAppearanceBundle:SidebarWidgetPanel')->findOneBy(array('globalOption' => $option,'sidebarWidget' =>$entity));
        if(empty($sidebarWidgetPanel)){

            $sidebarPanel = new SidebarWidgetPanel();
            $sidebarPanel->setGlobalOption($option);
            $sidebarPanel->setSidebarWidget($entity);
            $em->persist($sidebarPanel);
            $em->flush();
            $panel = $sidebarPanel;
        }else{
            $panel = $sidebarWidgetPanel;
        }



        $editForm = $this->createEditForm($panel);

        return $this->render('SettingAppearanceBundle:SidebarWidget:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a SidebarWidget entity.
     *
     * @param SidebarWidget $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(SidebarWidgetPanel $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new SidebarWidgetPanelType($globalOption), $entity, array(
            'action' => $this->generateUrl('sidebarwidget_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing SidebarWidget entity.
     *
     */
    public function updateAction(Request $request, SidebarWidgetPanel $entity)
    {
        $em = $this->getDoctrine()->getManager();



        $deleteForm = $this->createDeleteForm($entity->getId());
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('sidebarwidget_edit', array('id' => $entity->getSidebarWidget()->getId())));
        }

        return $this->render('SettingAppearanceBundle:SidebarWidget:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a SidebarWidget entity.
     *
     */
    public function deleteAction(SidebarWidget $megamenu )
    {

            $em = $this->getDoctrine()->getManager();
            if (!$megamenu) {
                throw $this->createNotFoundException('Unable to find SidebarWidget entity.');
            }

            $em->remove($megamenu);
            $em->flush();
            return $this->redirect($this->generateUrl('megamenu'));
    }

    /**
     * Creates a form to delete a Admission entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sidebarwidget_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }




    public function sortedAction(Request $request){

        $data = $request->request->get('menuItem');
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:SidebarWidget')->setOrdering($data);
        exit;

    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, Page $entity)
    {
        $form = $this->createDeleteForm($entity->getId());
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('megamenu'));
    }


}
