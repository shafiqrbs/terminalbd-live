<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\SmsSender;
use Setting\Bundle\ToolBundle\Form\SmsSenderType;

/**
 * SmsSender controller.
 *
 */
class SmsSenderController extends Controller
{

    /**
     * Lists all SmsSender entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $global = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('SettingToolBundle:SmsSender')->findBy(array('globalOption' => $global),array('created'=>'DESC'));
        return $this->render('SettingToolBundle:SmsSender:index.html.twig', array(
            'entities' => $entities,
            'globalOption' => $global,
        ));
    }

    /**
     * Deletes a SmsSender entity.
     *
     */
    public function deleteAction()
    {

        $global = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('SettingToolBundle:SmsSender')->findBy(array('globalOption' => $global),array('created'=>'DESC'));
        $em = $this->getDoctrine()->getManager();
        foreach($entities as $send){
            $em->remove($send);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('smssender'));
    }


}
