<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Setting\Bundle\AppearanceBundle\Entity\Menu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\AppearanceBundle\Entity\MenuCustom;
use Setting\Bundle\AppearanceBundle\Form\MenuCustomType;

/**
 * MenuCustom controller.
 *
 */
class MenuCustomController extends Controller
{

    /**
     * Lists all MenuCustom entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SettingAppearanceBundle:MenuCustom')->findAll();

        /*$globalOptions = $em->getRepository('SettingToolBundle:GlobalOption')->findBy(array('status'=>1));
        foreach ($globalOptions as $globalOption)
        {

            foreach( $entities as $custom){

                $menu = new Menu();
                $menu->setGlobalOption($globalOption);
                $menu->setMenuCustom($custom);
                $menu->setMenu($custom->getMenu());
                $menu->setMenuSlug($globalOption->getSlug().'-'.$custom->getSlug());
                $menu->setSlug($custom->getSlug());
                $menu->setStatus(0);
                $em->persist($menu);
            }

        }*/

        $em->flush();

        return $this->render('SettingAppearanceBundle:MenuCustom:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new MenuCustom entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MenuCustom();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('menucustom'));
        }

        return $this->render('SettingAppearanceBundle:MenuCustom:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MenuCustom entity.
     *
     * @param MenuCustom $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MenuCustom $entity)
    {
        $form = $this->createForm(new MenuCustomType(), $entity, array(
            'action' => $this->generateUrl('menucustom_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new MenuCustom entity.
     *
     */
    public function newAction()
    {
        $entity = new MenuCustom();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingAppearanceBundle:MenuCustom:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MenuCustom entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:MenuCustom')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MenuCustom entity.');
        }
        return $this->render('SettingAppearanceBundle:MenuCustom:show.html.twig', array(
            'entity'      => $entity,

        ));
    }

    /**
     * Displays a form to edit an existing MenuCustom entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:MenuCustom')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MenuCustom entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingAppearanceBundle:MenuCustom:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a MenuCustom entity.
    *
    * @param MenuCustom $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MenuCustom $entity)
    {
        $form = $this->createForm(new MenuCustomType(), $entity, array(
            'action' => $this->generateUrl('menucustom_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing MenuCustom entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:MenuCustom')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MenuCustom entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('menucustom'));
        }

        return $this->render('SettingAppearanceBundle:MenuCustom:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a MenuCustom entity.
     *
     */
    public function deleteAction(MenuCustom $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MenuCustom entity.');
        }

        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('menucustom'));
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingAppearanceBundle:MenuCustom')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

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
        return $this->redirect($this->generateUrl('menucustom'));
    }


}
