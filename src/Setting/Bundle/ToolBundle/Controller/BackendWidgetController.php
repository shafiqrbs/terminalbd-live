<?php

namespace Setting\Bundle\ToolBundle\Controller;


use Proxies\__CG__\Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class BackendWidgetController extends Controller
{

    public function topBarAction($slug='')
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('@SettingTool/Widget/common.html.twig');

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {

            $globalOption = $this->getUser()->getGlobalOption();
            return $this->render('@SettingTool/Widget/admin.html.twig', array(
                'globalOption' => $globalOption
            ));

        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN')) {

            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
           // $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($inventory);
            //$accountHeads = $em->getRepository('AccountingBundle:Transaction')->specificParentAccountHead($inventory,23);
            return $this->render('@SettingTool/Widget/common.html.twig', array(
               // 'todaySalesOverview'      => $todaySalesOverview,
                //'accountHeads'      => $accountHeads,
            ));

        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY') ) {

            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($inventory);
            $accountHeads = $em->getRepository('AccountingBundle:Transaction')->specificParentAccountHead($inventory,23);
            return $this->render('@SettingTool/Widget/admin.html.twig', array(
                'todaySalesOverview'      => $todaySalesOverview,
                'accountHeads'      => $accountHeads,
            ));

        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_SALES')) {

            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($inventory);
            $accountHeads = $em->getRepository('AccountingBundle:Transaction')->specificParentAccountHead($inventory,23);
            return $this->render('@SettingTool/Widget/admin.html.twig', array(
                'todaySalesOverview'      => $todaySalesOverview,
                'accountHeads'      => $accountHeads,
            ));

        }else{
            return $this->redirect($this->generateUrl('bindu_homepage'));
        }
    }

    public function inventory()
    {


    }

}
