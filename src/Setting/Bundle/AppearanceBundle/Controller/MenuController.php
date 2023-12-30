<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Setting\Bundle\AppearanceBundle\Entity\Menu;
use Setting\Bundle\AppearanceBundle\Form\MenuEditType;
use Setting\Bundle\AppearanceBundle\Form\MenuType;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Menu controller.
 *
 */
class MenuController extends Controller
{

    /**
     * Lists all GlobalOption entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->findAll();

        return $this->render('SettingAppearanceBundle:Menu:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all Menu manage entities.
     *
     */
    public function menuManageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getUser()->getGlobalOption()->getId();
        $entities = $em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('globalOption'=>$id));
        $form = $this->createEditForm($id);
        return $this->render('SettingAppearanceBundle:Menu:edit.html.twig', array(
            'entities' => $entities,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new MenuGroup entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Menu();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $option = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($option);
            $entity->setMode("custom");
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('menu_manage'));
        }

        return $this->render('SettingAppearanceBundle:Menu:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MenuGroup entity.
     *
     * @param Menu $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Menu $entity)
    {
        $form = $this->createForm(new MenuType(), $entity, array(
            'action' => $this->generateUrl('menu_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Menu entity.
     *
     */
    public function newAction()
    {
        $entity = new Menu();
        $form   = $this->createCreateForm($entity);
        return $this->render('SettingAppearanceBundle:Menu:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Lists all menu modify entities.
     *
     */
    public function modifyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('globalOption'=>$globalOption),array('sorting'=>'asc'));
        $form = $this->createEditForm($globalOption->getMenu());
        return $this->render('SettingAppearanceBundle:Menu:edit.html.twig', array(
            'entities' => $entities,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Creates a form to edit a Menu entity.
     *
     * @param Menu $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($id)
    {
        $form = $this->createForm(new MenuEditType(), null, array(
            'action' => $this->generateUrl('menu_update', array('id' => $id)),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Edits an existing MenuGroup entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:Menu')->updateMenu($data);
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }


    /**
     * Status a Page entity.
     *
     */
    public function stopMenuAction(Request $request, $id)
    {


        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:GlobalOption')->find($id);

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
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('menu'));
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction( $globalOption, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));

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
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('menu_manage'));
    }

    public function resetMenuAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $globalOption->getId();
        $entities = $em->getRepository('SettingAppearanceBundle:MenuCustom')->findAll();
        foreach( $entities as $custom){

            $exist = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption,'menuCustom' => $custom->getId()));

            if(empty($exist)){
                $menu = new Menu();
                $menu->setGlobalOption($globalOption);
                $menu->setMenuCustom($custom);
                $menu->setMenu($custom->getMenu());
                $menu->setMenuSlug($globalOption->getSlug().'-'.$custom->getSlug());
                $menu->setSlug($custom->getSlug());
                $menu->setStatus(0);
                $em->persist($menu);
                $em->flush($menu);
            }
        }
        $this->get('session')->getFlashBag()->add(
            'success',"Reset menu has been updated successfully"
        );
        return $this->redirect($this->generateUrl('menu_manage'));
    }





}
