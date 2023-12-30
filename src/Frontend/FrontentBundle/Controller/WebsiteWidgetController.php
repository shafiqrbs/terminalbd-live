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

class WebsiteWidgetController extends Controller
{


    public function featureWidgetAction(GlobalOption $globalOption , Menu $menu , $position ='' )
    {

        $widgets                    = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findBy(array('globalOption' => $globalOption, 'widgetFor'=>'website','menu' => $menu ,'position' => $position ),array('sorting'=>'ASC'));

        /* Device Detection code desktop or mobile */

        return $this->render('@Frontend/Template/Desktop/WebsiteWidget/widget.html.twig', array(
            'widgets'                  => $widgets,
            'globalOption'            => $globalOption,
        ));

    }


    public function pageBaseFeatureWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {

        return $this->render('@Frontend/Template/Desktop/WebsiteWidget/featureWidget.html.twig', array(
            'widget'                => $widget,
            'globalOption'          => $globalOption,

        ));
    }

    public function sidebarBaseFeatureWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {
        return $this->render('@Frontend/Template/Desktop/WebsiteWidget/featureWidget.html.twig', array(
            'widget'                => $widget,
            'globalOption'          => $globalOption,

        ));
    }

    public function pageBaseContentModuleAction(GlobalOption $globalOption , FeatureWidget $widget)
    {

        $limit = $widget->getModuleShowLimit() > 0 ? $widget->getModuleShowLimit() : 10;
        $entities                   = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(), $widget->getModule() ,$limit);
        return $this->render('@Frontend/Template/Desktop/WebsiteWidget/page.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption,

        ));
    }

    public function pageBaseModuleFeatureCategoryWidgetAction(GlobalOption $globalOption,FeatureWidget $widget)
    {
        $limit = $widget->getModuleShowLimit() > 0 ? $widget->getModuleShowLimit() : 10;
        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:ModuleCategory')->getFeatureModuleCategory($globalOption,$limit);
        return $this->render('@Frontend/Template/Desktop/WebsiteWidget/featureWidgetCategory.html.twig', array(
            'widget'                => $widget,
            'globalOption'          => $globalOption,
            'categories'              => $entities,
        ));
    }

    public function pageBaseModuleCategoryWidgetAction(GlobalOption $globalOption,FeatureWidget $widget)
    {
        return $this->render('@Frontend/Template/Desktop/WebsiteWidget/moduleCategoryWidget.html.twig', array(
            'widget'                => $widget,
            'globalOption'          => $globalOption,
        ));
    }

    public function sidebarBaseContentModuleAction(GlobalOption $globalOption , FeatureWidget $widget)
    {
        $limit = $widget->getModuleShowLimit() > 0 ? $widget->getModuleShowLimit() : 10;
        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(), $widget->getModule() ,$limit);
        return $this->render('@Frontend/Template/Desktop/WebsiteWidget/sidebar.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption,
        ));
    }

    public function headerFooterBaseContentModuleAction(GlobalOption $globalOption , FeatureWidget $widget)
    {
        $limit = $widget->getModuleShowLimit() > 0 ? $widget->getModuleShowLimit() : 10;
        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(), $widget->getModule() ,$limit);
        return $this->render('@Frontend/Template/Desktop/WebsiteWidget/headerFooter.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption,
        ));
    }

    public function templateWidgetAction(GlobalOption $globalOption , Menu $menu , $position ='' )
    {

        $widgets                    = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findBy(array('globalOption' => $globalOption, 'widgetFor'=>'website','menu' => $menu ,'position' => $position ),array('sorting'=>'ASC'));
        /* Device Detection code desktop or mobile */
        $theme = $globalOption->getSiteSetting()->getTheme()->getName();
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $path = 'Template/Mobile/'.$theme.'/WebsiteWidget/';
        }else{
            $path = 'Template/Desktop/'.$theme.'/WebsiteWidget/';
        }

        return $this->render('@Frontend/'.$path.'widget.html.twig', array(
            'widgets'                  => $widgets,
            'globalOption'            => $globalOption,
        ));
    }

    public function pageBaseTemplateWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {

        $theme = $globalOption->getSiteSetting()->getTheme()->getName();
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $path = 'Template/Mobile/'.$theme.'/WebsiteWidget/';
        }else{
            $path = 'Template/Desktop/'.$theme.'/WebsiteWidget/';
        }

        return $this->render('@Frontend/'.$path.'/featureWidget.html.twig', array(
            'widget'                => $widget,
            'globalOption'          => $globalOption,

        ));
    }

    public function sidebarBaseTemplateWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {
        $theme = $globalOption->getSiteSetting()->getTheme()->getName();
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $path = 'Template/Mobile/'.$theme.'/WebsiteWidget/';
        }else{
            $path = 'Template/Desktop/'.$theme.'/WebsiteWidget/';
        }

        return $this->render('@Frontend/'.$path.'/featureWidget.html.twig', array(
            'widget'                => $widget,
            'globalOption'          => $globalOption,

        ));
    }

    public function pageBaseTemplateContentModuleAction(GlobalOption $globalOption , FeatureWidget $widget)
    {

        $limit      = $widget->getModuleShowLimit() > 0 ? $widget->getModuleShowLimit() : 10;
        $entities   = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(), $widget->getModule() ,$limit);
        $theme = $globalOption->getSiteSetting()->getTheme()->getName();
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $path = 'Template/Mobile/'.$theme.'/WebsiteWidget/';
        }else{
            $path = 'Template/Desktop/'.$theme.'/WebsiteWidget/';
        }
        return $this->render('@Frontend/'.$path.'/page.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption,

        ));
    }

    public function sidebarBaseTemplateContentModuleAction(GlobalOption $globalOption , FeatureWidget $widget)
    {
        $limit      = $widget->getModuleShowLimit() > 0 ? $widget->getModuleShowLimit() : 10;
        $entities   = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(), $widget->getModule() ,$limit);
        $theme = $globalOption->getSiteSetting()->getTheme()->getName();
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $path = 'Template/Mobile/'.$theme.'/WebsiteWidget/';
        }else{
            $path = 'Template/Desktop/'.$theme.'/WebsiteWidget/';
        }
        return $this->render('@Frontend/'.$path.'/sidebar.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption,
        ));
    }

    public function headerFooterBaseTemplateContentModuleAction(GlobalOption $globalOption , FeatureWidget $widget)
    {
        $limit      = $widget->getModuleShowLimit() > 0 ? $widget->getModuleShowLimit() : 10;
        $entities   = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(), $widget->getModule() ,$limit);
        $theme = $globalOption->getSiteSetting()->getTheme()->getName();
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $path = 'Template/Mobile/'.$theme.'/WebsiteWidget/';
        }else{
            $path = 'Template/Desktop/'.$theme.'/WebsiteWidget/';
        }
        return $this->render('@Frontend/'.$path.'/headerFooter.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption,
        ));
    }

    public function featureMobileWidgetAction(GlobalOption $globalOption , Menu $menu , $position ='' )
    {

        $widgets                    = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findBy(array('globalOption' => $globalOption, 'menu' => $menu,'position' => $position ),array('sorting'=>'ASC'));

        return $this->render('@Frontend/Template/Mobile/WebsiteWidget/widget.html.twig', array(
            'widgets'                  => $widgets,
            'globalOption'            => $globalOption,
        ));
    }

    public function sliderMobileFeatureWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {
        return $this->render('@Frontend/Template/Mobile/WebsiteWidget/feature.html.twig', array(
            'widget'                => $widget,
            'globalOption'          => $globalOption,

        ));
    }

    public function pageMobileFeatureWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {
        $limit = $widget->getModuleShowLimit() > 0 ? $widget->getModuleShowLimit() : 10;
        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(), $widget->getModule() ,$limit);
        return $this->render('@Frontend/Template/Mobile/WebsiteWidget/page.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption,
        ));
    }

    public function pageMobileModuleFeatureCategoryWidgetAction(GlobalOption $globalOption,FeatureWidget $widget)
    {
        $limit = $widget->getModuleShowLimit() > 0 ? $widget->getModuleShowLimit() : 10;
        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:ModuleCategory')->getFeatureModuleCategory($globalOption,$limit);
        return $this->render('@Frontend/Template/Mobile/WebsiteWidget/featureWidgetCategory.html.twig', array(
            'widget'                => $widget,
            'globalOption'          => $globalOption,
            'categories'              => $entities,
        ));
    }

    public function pageMobileModuleCategoryWidgetAction(GlobalOption $globalOption,FeatureWidget $widget)
    {
        return $this->render('@Frontend/Template/Mobile/WebsiteWidget/moduleCategoryWidget.html.twig', array(
            'widget'                => $widget,
            'globalOption'          => $globalOption,
        ));
    }


}
