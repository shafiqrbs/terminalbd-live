<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\ApplicationPricing;
use Setting\Bundle\ToolBundle\Form\ApplicationPricingType;

/**
 * ApplicationPricing controller.
 *
 */
class ApplicationPricingController extends Controller
{

    /**
     * Lists all ApplicationPricing entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine();

        $apps = $em->getRepository('SettingToolBundle:AppModule')->findBy(array('status'=>1),array('name'=>'ASC'));
        $syndicates = $em->getRepository('SettingToolBundle:SyndicateModule')->findBy(array('status'=>1),array('name'=>'ASC'));
        $modules = $em->getRepository('SettingToolBundle:Module')->findBy(array('status'=>1),array('name'=>'ASC'));

        return $this->render('SettingToolBundle:ApplicationPricing:index.html.twig', array(
            'apps'          => $apps,
            'syndicates'    => $syndicates,
            'modules'       => $modules,
        ));
    }

    public function inlineAppAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:AppModule')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $entity->setPrice($data['value']);
        $em->flush();
        exit;
    }

    public function inlineSyndicateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:SyndicateModule')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $entity->setPrice($data['value']);
        $em->flush();
        exit;
    }

    public function inlineModuleAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:Module')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $entity->setPrice($data['value']);
        $em->flush();
        exit;
    }


}
