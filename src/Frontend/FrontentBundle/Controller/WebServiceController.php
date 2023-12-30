<?php

namespace Frontend\FrontentBundle\Controller;
use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
use Setting\Bundle\ToolBundle\Entity\AppModule;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class WebServiceController extends Controller
{


    /**
     * @param $subdomain
     * @return mixed
     */
    public function indexAction(Request $request , $subdomain)
    {

    	$em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){
        	if($globalOption->getDomainType() == 'ecommerce'){
		        return $this->renderEcommerce($request , $globalOption);
	        }elseif ($globalOption->getDomainType() == 'medicine'){
		        return $this->renderMedicine($request , $globalOption);
	        }else{
		        return $this->renderWebsite($globalOption);
	        }
        }else{
            return $this->redirect($this->generateUrl('homepage'));
        }
    }

    public function adminAuthentictionAction(Request $request)
    {
        $user = $this->getUser();
        if(empty($user)){
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        $globalOption = $user->getGlobalOption();
        /* @var $globalOption GlobalOption */

        $enable = $globalOption->getStatus();

        $apps = array();
        if (!empty($globalOption ->getSiteSetting())) {

            $modules = $globalOption->getSiteSetting()->getAppModules();

            /* @var AppModule $mod */

            foreach ($modules as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $apps[] = $mod->getSlug();
                }
            }
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_WEBSITE') && $this->get('security.authorization_checker')->isGranted('ROLE_MEMBER') && $enable == 1 && in_array('website',$apps)) {
            echo "Valid";
            return $this->redirect($this->generateUrl('member_index'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_WEBSITE') && $enable == 1 && in_array('website',$apps)) {
            return $this->redirect($this->generateUrl('website'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_BUYER') && $enable == 1 && in_array('e-commerce',$apps)) {
            return $this->redirect($this->generateUrl("{$globalOption->getSubDomain()}_webservice_buyer_dashboard"));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_ECOMMERCE_VENDOR') && $enable == 1 && in_array('ecommerce',$apps)) {
            return $this->redirect($this->generateUrl('order',array('shop'=> $globalOption->getSlug())));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ECOMMERCE')) {
            return $this->redirect($this->generateUrl('ecommerce_dashboard'));
        }

    }

	protected function renderEcommerce(Request $request ,GlobalOption $globalOption)
	{

		$em = $this->getDoctrine()->getManager();
		$cart = new Cart($request->getSession());
		$siteEntity = $globalOption->getSiteSetting();
		$themeName = $siteEntity->getTheme()->getFolderName();
		$menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption ,'slug' => 'home'));

		/* Device Detection code desktop or mobile */

		$detect = new MobileDetect();
		if( $detect->isMobile() ||  $detect->isTablet() ) {
			$theme = 'Template/Mobile/'.$themeName;
		}else{
			$theme = 'Template/Desktop/'.$themeName;
		}
		return $this->render('FrontendBundle:'.$theme.':index.html.twig',
			array(
				'entity'    => $globalOption,
				'globalOption'    => $globalOption,
				'pageName'    => 'Home',
				'menu'    => $menu,
				'cart'    => $cart,
				'searchForm'    => array(),
			)
		);

	}
    public function renderWebsite(GlobalOption $globalOption){

	    $em = $this->getDoctrine()->getManager();
	    $siteEntity = $globalOption->getSiteSetting();
	    $themeName = $siteEntity->getTheme()->getFolderName();
	    $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption ,'slug' => 'home'));

	    /* Device Detection code desktop or mobile */

	    $detect = new MobileDetect();

	    if( $detect->isMobile() ||  $detect->isTablet() ) {
		    $theme = 'Template/Mobile/'.$themeName;
	    }else{
		    $theme = 'Template/Desktop/'.$themeName;
	    }
	    return $this->render('FrontendBundle:'.$theme.':index.html.twig',
		    array(
			    'entity'            => $globalOption,
			    'globalOption'      => $globalOption,
			    'pageName'          => 'Home',
			    'menu'              => $menu,
			    'searchForm'    => array(),
		    )
	    );
    }

    public function renderMedicine(Request $request , GlobalOption $globalOption){

	    $em = $this->getDoctrine()->getManager();
	    $cart = new Cart($request->getSession());
	    $siteEntity = $globalOption->getSiteSetting();
	    $themeName = $siteEntity->getTheme()->getFolderName();
	    $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption ,'slug' => 'home'));
	    /* Device Detection code desktop or mobile */

	    $detect = new MobileDetect();
	    if( $detect->isMobile() ||  $detect->isTablet() ) {
		    $theme = 'Template/Mobile/'.$themeName;
	    }else{
		    $theme = 'Template/Desktop/'.$themeName;
	    }
	    return $this->render('FrontendBundle:'.$theme.':index.html.twig',
		    array(
			    'entity'    => $globalOption,
			    'globalOption'    => $globalOption,
			    'pageName'    => 'Home',
			    'menu'    => $menu,
			    'cart'    => $cart,
			    'searchForm'    => array(),
		    )
	    );
    }


    public function moduleContentAction($subdomain,$module,$id){

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:'.$module)->find($id);
        echo '<img class="responsive-image" src="/'.$entity->getWebpath().'">';
        echo $entity->getContent();
        exit;

    }


    public function frontendLogoutAction(Request $request)
    {
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
        return new Response('success');

    }


}
