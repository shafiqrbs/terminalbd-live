<?php

namespace Bindu\BinduBundle\Controller;
use Frontend\FrontentBundle\Service\MobileDetect;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\Syndicate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;


class WidgetController extends Controller
{

    public function headerMenuAction()
    {

        $applications                     = $this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->findBy(array('status'=>1),array('name'=>'ASC'));
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if($detect->isMobile() && $detect->isTablet() ) {
            return $this->render('@Bindu/Widget/mobile-menu.html.twig', array(
		        'applications'           => $applications,
	        ));
        }else{
	        return $this->render('@Bindu/Widget/main-header-menu.html.twig', array(
		        'applications'       => $applications,
	        ));
        }

    }


	public function footerMenuApplicationAction()
	{
		$menus                     = $this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->findBy(array('status'=>1),array('name'=>'ASC'));
		return $this->render('@Bindu/Widget/application-mobile-menu.html.twig', array(
			'applications'           => $menus,
		));
	}


	public function loginAction()
	{

		$csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
		return $this->render('@Bindu/Widget/login.html.twig', array(
			'csrfToken'   => $csrfToken,
		));
	}


    public function aboutusAction($slug='')
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $aboutus = $globalOption->getSlug().'-about-us';
        $about                     = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'menu'=>'About us'));
        return $this->render('@Bindu/Widget/aboutus.html.twig', array(
            'about'           => $about,
        ));
    }

   public function businessAction($directory = NULL )
    {


        $entities                     = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->findBy(array('status'=>1),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if($detect->isMobile() OR  $detect->isTablet() ) {
            return $this->render('@Bindu/Frontend/Mobile/businessContent.html.twig', array(
                'entities'           => $entities,
            ));
        }else{
            return $this->render('@Bindu/Frontend/Mobile/businessContent.html.twig', array(
                'entities'           => $entities,
            ));
        }

    }

   public function locationAction()
    {
        $entities                     = $this->getDoctrine()->getRepository('SettingLocationBundle:Location')->findBy(array('level'=>2),array('name'=>'ASC'));

        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            return $this->render('@Bindu/Frontend/Mobile/locationContent.html.twig', array(
                'entities'           => $entities,
            ));
        }else{
            return $this->render('@Bindu/Frontend/Mobile/locationContent.html.twig', array(
                'entities'           => $entities,
            ));
        }

    }

    public function moduleAction()
    {
        $entities                     = $this->getDoctrine()->getRepository('SettingToolBundle:Module')->findBy(array('status'=>1),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            return $this->render('@Bindu/Frontend/Mobile/module.html.twig', array(
                'entities'           => $entities,
            ));
        }else{
            return $this->render('@Bindu/Frontend/Mobile/module.html.twig', array(
                'entities'           => $entities,
            ));
        }

    }

    public function syndicateModuleAction()
    {
        $entities                     = $this->getDoctrine()->getRepository('SettingToolBundle:SyndicateModule')->findBy(array('status'=>1),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            return $this->render('@Bindu/Frontend/Mobile/syndicateModule.html.twig', array(
                'entities'           => $entities,
            ));
        }else{
            return $this->render('@Bindu/Frontend/Mobile/syndicateModule.html.twig', array(
                'entities'           => $entities,
            ));
        }

    }

    public function appModuleAction()
    {
        $entities                     = $this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->findBy(array('status'=>1),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            return $this->render('@Bindu/Frontend/Mobile/appModule.html.twig', array(
                'entities'           => $entities,
            ));
        }else{
            return $this->render('@Bindu/Frontend/Mobile/appModule.html.twig', array(
                'entities'           => $entities,
            ));
        }

    }



    public function searchAction($search='',$sector='',$location='')
    {
        $entity = new GlobalOption();
        $form   = $this->createCreateForm($entity);
        return $this->render('@Bindu/Widget/search.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }



    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */

    private function createCreateForm(GlobalOption $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $em = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $form = $this->createForm(new \Setting\Bundle\ToolBundle\Form\DomainSearchType($em,$location), $entity, array(
            'action' => $this->generateUrl('bindu_search', array('id' => $entity->getId())),
            'method' => 'GET',
            'attr' => array(
                'id' => 'commentform',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }


	public function siteContentAction($sector = '' , $slug = '', $limit = '')
	{
		$content                     = $this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findOneBy(array('businessSector'=> $sector,  'slug' => $slug));
		return $this->render('@Bindu/Widget/widget-content.html.twig', array(
			'about'           => $content,
			'limit'           => $limit,
		));

	}

	public function footerMenuAction($sector = '' , $parent = '')
	{
		$menus                     = $this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->getSubMenuList($sector,$parent);
		return $this->render('@Frontend/Widget/footer-menu.html.twig', array(
			'menus'           => $menus,
		));

	}




}
