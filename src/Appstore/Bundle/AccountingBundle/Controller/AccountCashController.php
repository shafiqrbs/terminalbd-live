<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountCash;
use Appstore\Bundle\AccountingBundle\Form\AccountCashType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountCash controller.
 *
 */
class AccountCashController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all AccountCash entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();

        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->accountCashOverview($globalOption,$data);
        return $this->render('AccountingBundle:AccountCash:index.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }


    /**
     * Finds and displays a AccountCash entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountCash')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountCash entity.');
        }
        return $this->render('AccountingBundle:AccountCash:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

}
