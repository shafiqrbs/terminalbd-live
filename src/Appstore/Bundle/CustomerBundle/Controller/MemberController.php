<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Form\MemberEditProfileType;
use Frontend\FrontentBundle\Service\Cart;
use Setting\Bundle\ToolBundle\Entity\Module;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberController extends Controller
{


    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $data['type'] = 'member';
        $data['process'] = 'Approved';
        $entities = $em->getRepository('DomainUserBundle:Customer')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $batches = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->studentBatchChoiceList();
        return $this->render('CustomerBundle:Member:index.html.twig', array(
            'globalOption' => $globalOption,
            'batches' => $batches,
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    /**
     * Displays a form to edit an existing DomainUser entity.
     *
     */
    public function editAction()
    {
        $user = $this->getUser();
        $profile = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption(),'user' => $user->getId()));
        $editForm = $this->createEditForm($profile);
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('CustomerBundle:Member:editProfile.html.twig', array(
            'globalOption' => $globalOption,
            'entity'      => $profile,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DomainUser entity.
     *
     */
    public function showAction()
    {
        $user = $this->getUser();
        $profile = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption(),'user' => $user->getId()));
        $editForm = $this->createEditForm($profile);
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('CustomerBundle:Member:show.html.twig', array(
            'globalOption' => $globalOption,
            'entity'      => $profile,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DomainUser entity.
     *
     */
    public function memberProfileAction($customer)
    {
        $user = $this->getUser();
        $profile = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption(),'customerId' => $customer));
        $editForm = $this->createEditForm($profile);
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('CustomerBundle:Member:show.html.twig', array(
            'globalOption' => $globalOption,
            'entity'      => $profile,
            'form'   => $editForm->createView(),
        ));
    }



    /**
     * Creates a form to edit a DomainUser entity.
     *
     * @param Customer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Customer $profile)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getRepository('DomainUserBundle:Customer');
        $form = $this->createForm(new MemberEditProfileType($em), $profile, array(
            'action' => $this->generateUrl('member_profile_update', array('shop' => $globalOption->getSlug())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing DomainUser entity.
     *
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profile = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption(),'user' => $user->getId()));
        if (!$profile) {
            throw $this->createNotFoundException('Unable to find customer entity.');
        }
        $editForm = $this->createEditForm($profile);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $profile->upload();
            $em->flush();
            return $this->redirect($this->generateUrl('member_edit_profile', array('shop' => $user->getGlobalOption()->getSlug())));
        }
        return $this->render('CustomerBundle:Member:editProfile.html.twig', array(
            'globalOption'      =>  $user->getGlobalOption(),
            'entity'            => $profile,
            'form'              => $editForm->createView(),

        ));
    }


}
