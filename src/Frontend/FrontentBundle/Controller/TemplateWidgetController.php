<?php

namespace Frontend\FrontentBundle\Controller;
use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\CustomerRegisterType;
use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\AppearanceBundle\Entity\FeatureWidget;
use Setting\Bundle\AppearanceBundle\Entity\Menu;
use Setting\Bundle\ContentBundle\Entity\PageModule;
use Setting\Bundle\ToolBundle\Entity\Branding;
use Product\Bundle\ProductBundle\Entity\Product;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\Module;
use Setting\Bundle\ToolBundle\Entity\SubscribeEmail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Syndicate\Bundle\ComponentBundle\Entity\Education;
use Syndicate\Bundle\ComponentBundle\Entity\Vendor;

class TemplateWidgetController extends Controller
{


    public function mobileMenuAction(GlobalOption $globalOption)
    {
        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption' => $globalOption,'parent'=> NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
        $menuTree = $this->get('setting.menuTreeSettingRepo')->getWebsiteMobileMenuTree($menus,$globalOption->getSubDomain());
        return $this->render('@Frontend/Template/Mobile/Widget/webMenu.html.twig', array(
            'menuTree'           => $menuTree,
        ));
    }
    

    public function headerAction(GlobalOption $globalOption, Menu $menu ,Request $request)
    {
        /* Device Detection code desktop or mobile */

        $em = $this->getDoctrine()->getManager();
        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=> $globalOption,'parent' => NULL ,'menuGroup'=> 1),array('sorting'=>'asc'));
        $menuTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$globalOption);
        $countries = $this->getDoctrine()->getRepository('SettingLocationBundle:Country')->findBy(array(),array('name'=>'asc'));
        return $this->render('@Frontend/Template/Desktop/WebsiteWidget/header.html.twig', array(
            'menuTree'              => $menuTree,
            'globalOption'          => $globalOption,
            'countries'             => $countries,
            'menu'                  => $menu,

        ));
    }

    public function footerMenuAction(GlobalOption $globalOption, $menuGroup , Request $request)
    {
        /* Device Detection code desktop or mobile */

        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> $menuGroup ),array('sorting'=>'asc'));
        $footerMenu = $this->get('setting.menuTreeSettingRepo')->getFooterMenu($menus,$globalOption->getSubDomain());
        return $this->render('@Frontend/Template/Desktop/Widget/FooterMenu.html.twig', array(
            'footerMenu'           => $footerMenu,
        ));
    }

    public function footerAction(GlobalOption $globalOption,Request $request)
    {

        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
        $footerMenu = $this->get('setting.menuTreeSettingRepo')->getFooterMenu($menus,$globalOption->getSubDomain(),'desktop');

        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        $user = new User();
        $form   = $this->createCreateForm($globalOption->getSubDomain(),$user);

        $cart = new Cart($request->getSession());
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';

        return $this->render('@Frontend/Template/Desktop/footer.html.twig', array(
            'globalOption'             => $globalOption,
            'footerMenu'               => $footerMenu,
            'cartResult'               => $cartResult,
            'csrfToken'   => $csrfToken,
            'form'   => $form->createView(),
        ));
    }

    public function modalLoginAction(GlobalOption $globalOption)
    {

        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        $user = new User();
        $form   = $this->createCreateForm($globalOption->getSubDomain(),$user);

        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();

        $detect = new MobileDetect();
        if( $detect->isMobile() || $detect->isTablet()) {
            $template = "Template/Mobile/{$themeName}/EcommerceWidget";
        }else{
            $template = "Template/Desktop/{$themeName}/EcommerceWidget";
        }
        return $this->render('@Frontend/'.$template.'/modalLogin.html.twig', array(
            'globalOption'             => $globalOption,
            'csrfToken'   => $csrfToken,
            'form'   => $form->createView(),
        ));
    }

    public function modalWebLoginAction(GlobalOption $globalOption)
    {

        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        $user = new User();
        $form   = $this->createCreateForm($globalOption->getSubDomain(),$user);

        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();

        $detect = new MobileDetect();
        if( $detect->isMobile() || $detect->isTablet()) {
            $template = "Template/Mobile/Widget";
        }else{
            $template = "Template/Desktop/Widget";
        }
        return $this->render('@Frontend/'.$template.'/modalLogin.html.twig', array(
            'globalOption'             => $globalOption,
            'csrfToken'   => $csrfToken,
            'form'   => $form->createView(),
        ));
    }


    public function memberRegistrationAction(GlobalOption $globalOption)
    {
        $detect = new MobileDetect();
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        if( $detect->isMobile() || $detect->isTablet()) {
            $template = "Template/Mobile/{$themeName}/EcommerceWidget";
        }else{
            $template = "Template/Desktop/{$themeName}/EcommerceWidget";
        }
        return $this->render('@Frontend/'.$template.'/memberRegister.html.twig', array(
            'globalOption'             => $globalOption,
        ));
    }

    public function sidebarAction(GlobalOption $globalOption,Request $request)
    {

        $cart = new Cart($request->getSession());
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        return $this->render('@Frontend/Template/Desktop/EcommerceWidget/sidebarCart.html.twig', array(
            'globalOption'             => $globalOption,
            'cartResult'               => $cartResult,
        ));
    }


    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */

    private function createCreateForm($subdomain,User $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new CustomerRegisterType($location), $entity, array(
            'action' => $this->generateUrl($subdomain.'_webservice_customer_insert'),
            'method' => 'POST',
            'attr' => array(
                'id' => 'signup',
                'class' => 'register',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;

    }

    public function aboutusAction(GlobalOption $globalOption,$wordlimit)
    {

        $slug = $globalOption->getSlug().'-about-us';
        $about                     = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption,'slug' => $slug ));

        if(!empty($about)){
            return $this->render('@Frontend/Widget/aboutus.html.twig', array(
                'about'           => $about->getPage(),
                'wordlimit'           => $wordlimit,
            ));
        }else{
            return new Response('');
        }

    }

    public function countryAction()
    {

        $countries = $this->getDoctrine()->getRepository('SettingLocationBundle:Country')->findBy(array(),array('name'=>'asc'));
        $data = "";
        foreach ($countries as $country ):
            $selected = $country->getCode() == "BD" ? 'selected=selected' : "";
            $data .="<option {$selected}  '{$country->getId()}'>{$country->getName()}</option>";
        endforeach;
        return new Response($data);
    }

    public function aboutAction(GlobalOption $globalOption,$wordlimit)
    {

        $slug = $globalOption->getSlug().'-about-us';
        $about                     = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption,'slug' => $slug));
        if(!empty($about)){
            return $this->render('@Frontend/Widget/about.html.twig', array(
                'about'           => $about->getPage(),
                'wordlimit'           => $wordlimit,
            ));
        }else{
            return new Response('');
        }
    }

    public function moduleBaseContentAction(GlobalOption $globalOption , PageModule $pageModule )
    {
        $limit = $pageModule->getShowLimit() > 0 ? $pageModule->getShowLimit() : 5;
        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(),$pageModule->getModule()->getId(),$limit);
        return $this->render('@Frontend/Template/Desktop/Widget/'.$pageModule->getModule()->getModuleClass().'.html.twig', array(
            'entities'           => $entities,
            'pageModule'           => $pageModule,
            'globalOption'           => $globalOption,
        ));
    }

    public function moduleSidebarBaseContentAction(GlobalOption $globalOption , PageModule $pageModule)
    {

        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(),$pageModule->getModule()->getId(),$pageModule->getShowLimit());

        return $this->render('@Frontend/Template/Desktop/Widget/sidebar.html.twig', array(
            'entities'          => $entities,
            'pageModule'        => $pageModule,
            'globalOption'      => $globalOption,
        ));
    }

    public function pageBaseModuleContentAction(GlobalOption $globalOption , FeatureWidget $widget , $module )
    {

        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(), $module ,$widget->getModuleShowingLimit());

        return $this->render('@Frontend/Template/Desktop/Widget/page.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption,
        ));
    }





}
