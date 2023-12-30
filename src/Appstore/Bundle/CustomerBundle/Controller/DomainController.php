<?php

namespace Appstore\Bundle\CustomerBundle\Controller;


use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\CustomerEditProfileType;
use Frontend\FrontentBundle\Service\Cart;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Form\DomainSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DomainController extends Controller
{


    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new GlobalOption();
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->getActiveDomainList($form);
        $pagination = $this->paginate($entities);
        $shop = $request->request->get('shop');
        if(!empty($globalOption)){
            $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug' => $shop));
        }else{
            $globalOption ='';
        }

        return $this->render('CustomerBundle:Domain:index.html.twig', array(
            'entities' => $pagination,
            'user' => $this->getUser(),
            'form'   => $form->createView(),
            'entity' => $entity,
            'globalOption' => $globalOption,

        ));


    }

    public function domainBookmarkAction($generated)
    {

    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */

    private function createCreateForm(GlobalOption $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new DomainSearchType($em,$location), $entity, array(
            'action' => $this->generateUrl('customer_domain'),
            'method' => 'GET',
            'attr' => array(
                'id' => '',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

   



}
