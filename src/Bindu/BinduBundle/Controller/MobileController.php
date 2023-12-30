<?php
namespace Bindu\BinduBundle\Controller;
use Frontend\FrontentBundle\Service\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MobileController extends Controller
{
    /**
     * @param $subdomain
     * @return mixed
     */
    public function indexAction($subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            /* Device Detection code desktop or mobile */

            $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
            $menuTree = $this->get('setting.menuTreeSettingRepo')->getMobileMenuTree($menus,$subdomain);
            return $this->render('FrontendBundle:Template/Mobile/MobilePreview:index.html.twig',
                array(
                    'entity'    => $globalOption,
                    'menuTree'    => $menuTree,
                    'globalOption'    => $globalOption,
               )
            );

        }else{

            return $this->redirect($this->generateUrl('homepage'));
        }
    }


    public function contentAction($subdomain,$slug){

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            /* Device Detection code desktop or mobile */
            $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
            $menuTree = $this->get('setting.menuTreeSettingRepo')->getMobileMenuTree($menus,$subdomain);
            $content = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption,'slug'=>$slug));
            return $this->render('FrontendBundle:Template/Mobile/MobilePreview:content.html.twig',
                array(
                    'page'    => $content->getPage(),
                    'menuTree'    => $menuTree,
                    'globalOption'    => $globalOption,
                )
            );

        }


    }

    public function contactAction($subdomain){

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=>1),array('sorting'=>'asc'));
            $menusTree = $this->get('setting.menuTreeSettingRepo')->getMobileMenuTree($menus,$subdomain);
            $branches = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findBy(array('status'=>1,'globalOption'=>$globalOption,'module'=>6),array('name'=>'ASC'));
            return $this->render('FrontendBundle:Template/Mobile/MobilePreview:contact.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'menuTree'          => $menusTree,
                    'branches'          => $branches,
                )
            );
        }

    }
}
