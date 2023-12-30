<?php

namespace Frontend\FrontentBundle\Controller;


use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
use Setting\Bundle\ToolBundle\Entity\SiteSetting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

class WebServiceModuleController extends Controller
{


    public function moduleAction(Request $request ,$subdomain,$module)
    {

        $em = $this->getDoctrine()->getManager();
        $page ='';
        $pagination ='';
        $cart = new Cart($request->getSession());
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $siteEntity = $globalOption->getSiteSetting();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => $module));
            if(!empty($menu)){

                /* @var SiteSetting $siteEntity */
                $themeName = $siteEntity->getTheme()->getFolderName();
                $moduleName = $this->get('setting.menuTreeSettingRepo')->getCheckModule($menu);

                if($moduleName){
                    $twigName = "module";
                    $pagination = $em->getRepository('SettingContentBundle:Page')->findBy(array('globalOption' => $globalOption,'module' => $menu->getModule()->getId()),array('id'=>'desc'));
                    $pagination = $this->paginate( $pagination,$limit= 10 );

                }else{

                    $page = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption,'slug' => $module));
                    if(!empty($page->getMenuCustom()) and $page->getMenuCustom()->getId() == 1){
                        $twigName = "index";
                    }else{
                        $twigName = "content";
                    }
                }

            }else{

                $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption ,'slug' => 'home'));
                $themeName = $siteEntity->getTheme()->getFolderName();
                $twigName = "index";
            }

        }

        $pagination = ($pagination) ? $pagination :'';
        $page = ($page) ? $page->getPage() :'';

        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }

        return $this->render('FrontendBundle:'.$theme.':'.$twigName.'.html.twig',

            array(

                'globalOption'  => $globalOption,
                'pagination'    => $pagination,
                'page'          => $page,
                'cart'          => $cart,
                'menu'          => $menu,
                'searchForm'        => $_REQUEST,
                'pageName'      => 'pageName',
            )
        );
    }

    public function modulePageAction($subdomain, $module ,$slug)
    {


        $em = $this->getDoctrine()->getManager();

        $categories ='';
        $page ='';
	    $twigName = "content";
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $entityModule = $em->getRepository('SettingToolBundle:Module')->findOneBy(array('slug' => $module));
            if(!empty($entityModule)){
                $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => $module));
                if($entityModule){

                    $page = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption' => $globalOption,'slug' => $slug));
                    $twigName = "moduleDetails";

                }else{

                    /** @pram $page Page */

                    $page = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'slug' => $module));
                    $twigName = "content";
                }
            }

        }
	    $siteEntity = $globalOption->getSiteSetting();
	    $themeName = $siteEntity->getTheme()->getFolderName();
        $page = ($page) ? $page :'';

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':'.$twigName.'.html.twig',
            array(
                'globalOption'          => $globalOption,
                'page'                  => $page,
                'menu'                  => $menu,
                'pageName'              => 'pageName',
            )
        );
    }

    public function moduleCategoryAction($subdomain,$module,$slug)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => $module));

            $page ='';
            $pagination ='';
            $moduleName ='';
            $category ='';
            if(!empty($menu)){

                $siteEntity = $globalOption->getSiteSetting();
                $themeName = $siteEntity->getTheme()->getFolderName();

                $moduleName = $this->get('setting.menuTreeSettingRepo')->getCheckModule($menu);
                if($moduleName){
                    $twigName = "module";
                    $cat = $em->getRepository('SettingContentBundle:ModuleCategory')->findOneBy(array('slug' => $slug));
                    $category = $cat;
                    $pagination = $em->getRepository('SettingContentBundle:Page')->getCategoryPage($globalOption,$menu->getModule(),$cat);
                    $pagination = $this->paginate( $pagination,$limit= 12 );
                    if(!empty($menu->getModule())){
                        $categories = $em->getRepository('SettingContentBundle:ModuleCategory')->moduleBaseCategory($globalOption->getId(),$menu->getModule()->getId());
                    }

                }else{

                    $page = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'slug' => $module));
                    $twigName = "content";

                }
            }

        }

        $pagination = ($pagination) ? $pagination :'';
        $page = ($page) ? $page :'';

        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':'.$twigName.'.html.twig',
            array(

                'globalOption'  => $globalOption,
                'pagination'    => $pagination,
                'menu'          => $menu,
                'page'          => $page,
                'category'      => $category,
                'pageName'      => 'pageName',
            )
        );
    }

    public function contactAction($subdomain){

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => 'contact'));
            $page ='';
            if(!empty($menu)){
                $siteEntity = $globalOption->getSiteSetting();
                $themeName = $siteEntity->getTheme()->getFolderName();
            }

        }
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':contact.html.twig',
            array(
                'globalOption'        => $globalOption,
                'menu'                => $menu,
                'page'                => $globalOption->getContactPage(),
                'pageName'      => 'pageName',
              )
        );

    }

    public function contentAction($subdomain){

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => 'contact'));
            $page ='';
            $moduleName ='';

            if(!empty($menu)){

                $siteEntity = $globalOption->getSiteSetting();

                if(!empty($siteEntity)){
                    $themeName = $siteEntity->getTheme()->getFolderName();
                }else{
                    $themeName ='Default';
                }

                $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
                $menusTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$subdomain,'desktop');

                $page = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'slug' => $module));
                $twigName = "content";

            }

        }

        $page = ($page) ? $page :'';

        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':content.html.twig',
            array(

                'globalOption'  => $globalOption,
                'page'          => $page,
                'moduleName'    => $moduleName
            )
        );

    }

    public function searchAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();

        $page ='';
        $pagination ='';

        $data = $_REQUEST;
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $siteEntity = $globalOption->getSiteSetting();
        $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption ,'slug' => 'search'));
        if(!empty($siteEntity)){
            $themeName = $siteEntity->getTheme()->getFolderName();
        }else{
            $themeName ='Default';
        }

        if(!empty($globalOption)){
            $pagination = $em->getRepository('SettingContentBundle:Page')->searchResult($globalOption,$data['keyword']);
            $pagination = $this->paginate( $pagination,$limit= 10 );
        }
        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':search.html.twig',
            array(

                'globalOption'  => $globalOption,
                'pagination'    => $pagination,
                'menu'                => $menu,
                'searchForm' => $data,
                'pageName'      => 'Search',
            )
        );

    }

    public function paginate($entities,$limit = 10)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $limit /*limit per page*/
        );
        return $pagination;
    }




}
