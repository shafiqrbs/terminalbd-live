<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Doctrine\DBAL\Connection;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Setting\Bundle\AppearanceBundle\Entity\MenuGrouping;
use Setting\Bundle\AppearanceBundle\Form\MenuGroupingType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;

/**
 * MenuGrouping controller.
 *
 */
class MenuGroupingController extends Controller
{

    /**
     * Lists all MenuGrouping entities.
     *
     */
    public function indexAction()
    {
        /** @var EntityManager $em */

        $entities = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGroup')->findBy(array('status'=>1),array('name'=>'ASC'));
        return $this->render('SettingAppearanceBundle:MenuGrouping:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Creates a new MenuGrouping entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();

        $posts = $request->request->all();

        $entity = new MenuGrouping();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $menuGroup = $request->request->get('menuGroup');
            $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->insertMenuGrouping($posts,$globalOption,$menuGroup);
            return $this->redirect($this->generateUrl('menugrouping_sorting', array('menuGroup' => $menuGroup )));
        }

        return $this->render('SettingAppearanceBundle:MenuGrouping:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MenuGrouping entity.
     *
     * @param MenuGrouping $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MenuGrouping $entity)
    {

        $form = $this->createForm(new MenuGroupingType(), $entity, array(
            'action' => $this->generateUrl('menugrouping_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new MenuGrouping entity.
     *
     */
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $menuGroup = $em->getRepository('SettingAppearanceBundle:MenuGroup')->find($id);
        $entities = $em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('globalOption' => $globalOption,'status'=>1));

        $grouping = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption' => $globalOption,'menuGroup'=>$id));

        if(empty($grouping)){

            $entity = new MenuGrouping();
            $form   = $this->createCreateForm($entity);

            return $this->render('SettingAppearanceBundle:MenuGrouping:new.html.twig', array(
                'entity' => $entity,
                'entities' => $entities,
                'menuGroup' => $menuGroup,
                'form'   => $form->createView(),
            ));

        }else{

            return $this->redirect($this->generateUrl('menugrouping_edit', array('id' => $id)));
        }

    }


    /**
     * Finds and displays a MenuGrouping entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MenuGrouping entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingAppearanceBundle:MenuGrouping:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MenuGrouping entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $groupings =array();


        $globalOption = $this->getUser()->getGlobalOption();

        $menuGroup = $em->getRepository('SettingAppearanceBundle:MenuGroup')->find($id);

        $entities = $em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('globalOption' => $globalOption,'status'=>1));

        $grouping = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption' => $globalOption,'menuGroup'=>$id));
        foreach($grouping as $row){
            $groupings[] = $row->getMenu()->getId();
        }

        $entity = new MenuGrouping();
        $editForm   = $this->createCreateForm($entity);

        return $this->render('SettingAppearanceBundle:MenuGrouping:edit.html.twig', array(
            'entities'      => $entities,
            'form'          => $editForm->createView(),
            'menuGroup'     => $menuGroup,
            'groupings'     => $groupings,

        ));

    }

    public function sortingAction($menuGroup)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();

        $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=>$menuGroup),array('sorting'=>'asc'));
        $menusTree = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->getMenuTree($menus);
        $menuGroup = $em->getRepository('SettingAppearanceBundle:MenuGroup')->find($menuGroup);

        return $this->render('SettingAppearanceBundle:MenuGrouping:sorting.html.twig', array(
            'menu' => $menusTree,
            'menuGroup' => $menuGroup,


        ));

    }

    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('menuItem');
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->setMenuOrdering($data);
        exit;

    }


}


