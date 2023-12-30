<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * Vendor controller.
 *
 */
class BusinessReverseController extends Controller
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


    /**
     * Lists all Vendor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessReverse')->findAll();
        $pagination = $this->paginate($entities);
        return $this->render('BusinessBundle:Reverse:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function showAction($id)
    {

    	$config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessReverse')->findOneBy(array('businessConfig' => $config, 'id' => $id));
        if(empty($entity)){
            throw $this->createNotFoundException('Unable to find reverse entity.');
        }
        $twig = 'sales';
        if($entity->getProcess() == 'purchase'){
          $twig = 'purchase';
        }
        return $this->render("BusinessBundle:Reverse:{$twig}.html.twig", array(
            'entity' => $entity,
        ));
    }
}
