<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessAndroidProcess;
use Appstore\Bundle\BusinessBundle\Entity\Courier;
use Appstore\Bundle\BusinessBundle\Form\CourierType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Courier controller.
 *u
 */
class AndroidSalesController extends Controller
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
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */

    public function indexAction()
    {
        $conf = $this->getUser()->getGlobalOption()->getBusinessConfig()->getId();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessAndroidProcess')->getAndroidSalesList($conf,"sales");
        $pagination = $this->paginate($entities);
        return $this->render('BusinessBundle:AndroidProcess:index.html.twig', array(
            'entities' => $pagination,
        ));
    }


    public function androidProcessMysqlAction(BusinessAndroidProcess $android){
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $em = $this->getDoctrine()->getManager();
        $status = $this->getDoctrine()->getRepository('BusinessBundle:BusinessAndroidProcess')->insertApiSalesManual($config->getGlobalOption(),$android);
        if($status > 0 ){
            $android->setStatus(true);
            $em->persist($android);
            $em->flush();
            return new Response('success');
        }else{
            return new Response('failed');
        }
    }

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */

    public function deleteAction(BusinessAndroidProcess $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $this->getDoctrine()->getRepository(BusinessAndroidProcess::class)->removeSalesItem($entity);
        $em->remove($entity);
        $em->flush();
        return new Response(json_encode(array('success' => 'success')));
    }


}
