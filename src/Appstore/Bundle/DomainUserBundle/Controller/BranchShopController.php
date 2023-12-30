<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;
use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\DomainUserBundle\Form\BranchesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;

/**
 * BranchShop controller.
 *
 */
class BranchShopController extends Controller
{


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Branches')->findBy(array('globalOption' => $globalOption),array('name'=>'asc'));
        return $this->render('DomainUserBundle:Branches:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all Branches entities.
     *
     */

    public function branchesEmployeeAction(Branches $branches)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('DomainUserBundle:Branches:employee.html.twig', array(
            'branches' => $branches,
        ));
    }

    /**
     * Creates a new Branches entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Branches();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $globalOption = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('appsetting_branchshop'));
        }

        return $this->render('DomainUserBundle:Branches:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Branches entity.
     *
     * @param Branches $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Branches $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new BranchesType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('appsetting_branchshop_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Branches entity.
     * @Secure(roles="ROLE_DOMAIN_INVENTORY")
     */

    public function newAction()
    {
        $entity = new Branches();
        $form   = $this->createCreateForm($entity);

        return $this->render('DomainUserBundle:Branches:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Branches entity.
     *
     */

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Branches')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Branches entity.');
        }
        return $this->render('DomainUserBundle:Branches:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Branches entity.
     * @Secure(roles="ROLE_DOMAIN_INVENTORY")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Branches')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Branches entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('DomainUserBundle:Branches:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Branches entity.
     *
     * @param Branches $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Branches $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new BranchesType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('appsetting_branchshop_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Branches entity.
     * @Secure(roles="ROLE_DOMAIN_INVENTORY")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Branches')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Branches entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('appsetting_branchshop_edit', array('id' => $id)));
        }

        return $this->render('DomainUserBundle:Branches:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }




}


