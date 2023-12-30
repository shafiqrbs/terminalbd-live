<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Doctrine\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\ContactMessage;
use Setting\Bundle\ContentBundle\Form\ContactMessageType;

/**
 * ContactMessage controller.
 *
 */
class ContactMessageController extends Controller
{


    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            20  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all ContactMessage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entities = $em->getRepository('SettingContentBundle:ContactMessage')->findBy(array('user'=> $user ,'archive' => 0 ),array('created' => 'desc'));
        $entities = $this->paginate($entities);
        return $this->render('SettingContentBundle:ContactMessage:index.html.twig', array(
            'pagination' => $entities,
        ));
    }

    /**
     * Lists all ContactMessage entities.
     *
     */
    public function archiveAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();

        $entities = $em->getRepository('SettingContentBundle:ContactMessage')->findBy(array('user'=> $user ,'archive' => 1),array('created' => 'desc'));
        $entities = $this->paginate($entities);
        return $this->render('SettingContentBundle:ContactMessage:archive.html.twig', array(
            'pagination' => $entities,
        ));
    }


    /**
     * Edits an existing ContactMessage entity.
     *
     */
    public function updateAction(Request $request)
    {
        $data = $request ->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:ContactMessage')->reply($data);
        exit;

    }

    public function deleteAction($id){

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entity = $this->getDoctrine()->getRepository('SettingContentBundle:ContactMessage')->findOneBy(array('user' => $user,'id' => $id));

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('setting_tool.post.sms_message', new \Setting\Bundle\ToolBundle\Event\SmsEvent($entity));
        $dispatcher->dispatch('setting_tool.post.email_message', new \Setting\Bundle\ToolBundle\Event\EmailEvent($entity));



        $em->remove($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',"Data has been deleted successfully"
        );

        return $this->redirect($this->generateUrl('contactmessage_archive'));
    }


}
