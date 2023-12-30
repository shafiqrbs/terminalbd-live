<?php

namespace Bindu\BinduBundle\Controller;

use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\SignupType;
use Core\UserBundle\Form\UserConfirmType;
use Frontend\FrontentBundle\Service\MobileDetect;
use Setting\Bundle\ToolBundle\Event\ReceiveSmsEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BinduController extends Controller
{



	public function indexAction()
	{

		$slides = $this->getDoctrine()->getRepository('SettingContentBundle:SiteSlider')->findBy(array(),array('id'=>'DESC'));
		$apps =$this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->findBy(array('status'=>1));
		$testimonials =$this->getDoctrine()->getRepository('SettingContentBundle:Testimonial')->findBy(array('status'=>1));
		$data = array();
        $clients =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findClientSubdomain($data,$limit = 12);
		$entity = new User();
		$form   = $this->createCreateForm($entity);
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':index.html.twig', array(
			'entity' => $entity,
			'slides' => $slides,
			'apps' => $apps,
			'testimonials' => $testimonials,
			'clients' => $clients,
			'error' => '',
			'form'   => $form->createView(),
		));
	}

	public function homeAction()
	{
		return $this->redirect($this->generateUrl('home'));
	}


	public function builderAction()
	{

		$entity = new User();
		$form   = $this->createCreateForm($entity);
		$detect = new MobileDetect();
		if($detect->isMobile() OR $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':webBuilder.html.twig', array(
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

	private function createCreateForm(User $entity)
	{
		$location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
		$em = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
		$form = $this->createForm(new SignupType($em,$location), $entity, array(
			'action' => $this->generateUrl('bindu_create', array('id' => $entity->getId())),
			'method' => 'POST',
			'attr' => array(
				'id' => 'signup',
				'class' => ' registration signupForm',
				'novalidate' => 'novalidate',
			)
		));

		return $form;
	}


	/**
	 * Creates a new User entity.
	 *
	 */

	public function createAction(Request $request)
	{

		$entity = new User();
		$form = $this->createCreateForm($entity);
		$form->handleRequest($request);
		$intlMobile = $entity->getProfile()->getMobile();
		$mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
		$entity->getProfile()->setMobile($mobile);
		$data = $request->request->all();


		if ($form->isValid()) {

			$globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->createGlobalOption($mobile,$data);
			$entity->setPlainPassword("1234");
			$entity->setGlobalOption($globalOption);
			$entity->setEnabled(true);
			$entity->setDomainOwner(true);
			$entity->setUsername($mobile);
			$entity->setEmail($mobile.'@gmail.com');
			$entity->setRoles(array('ROLE_DOMAIN'));
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->initialSetup($entity);
			$this->get('settong.toolManageRepo')->createDirectory($entity->getGlobalOption()->getId());

			$dispatcher = $this->container->get('event_dispatcher');
			$dispatcher->dispatch('setting_tool.post.user_signup_msg', new \Setting\Bundle\ToolBundle\Event\UserSignup($entity));
			return $this->redirect($this->generateUrl('bindu_confirm'));
		}
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet()){

			$theme = 'Frontend/Mobile';
			return $this->render('BinduBundle:'.$theme.':webBuilder.html.twig', array(
				'entity' => $entity,
				'form'   => $form->createView(),

			));
		}else{
			$theme = 'Frontend/Desktop';
			return $this->render('BinduBundle:'.$theme.':index.html.twig', array(
				'entity' => $entity,
				'form'   => $form->createView(),

			));
		}

	}

	public function userCheckingAction(Request $request)
	{

		$intlMobile = $request->query->get('Core_userbundle_user[profile][mobile]',NULL,true);
		$mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
		$username = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('username' => $mobile));
		$profileMobile = $this->getDoctrine()->getRepository('UserBundle:Profile')->findOneBy(array('mobile'=>$mobile));
		$optionMobile = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('mobile'=>$mobile));

		if(empty($username) && empty($profileMobile) && empty($optionMobile)){
			$valid = 'true';
		}else{
			$valid = 'false';
		}
		echo $valid;
		exit;
	}

	public function doaminCheckingAction(Request $request)
	{

		$intdoamin = $request->query->get('domain',NULL,true);
		$doamin = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('domain'=>$intdoamin));

		if(empty($doamin)){
			$valid = 'true';
		}else{
			$valid = 'false';
		}
		echo $valid;
		exit;

	}

	public function subdomainCheckingAction(Request $request)
	{

		$intdoamin = $request->query->get('subDomain',NULL,true);
		$doamin = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$intdoamin));
		if(empty($doamin)){
			$valid = 'true';
		}else{
			$valid = 'false';
		}
		echo $valid;
		exit;
	}

	public function mobileCheckingAction(Request $request)
	{

		$intmobile = $request->query->get('mobile',NULL,true);
		$mobile = $this->get('settong.toolManageRepo')->specialExpClean($intmobile);
		$mobile = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('mobile' => $mobile));

		if(empty($mobile)){
			$valid = 'true';
		}else{
			$valid = 'false';
		}
		echo $valid;
		exit;
	}

	public function emailCheckingAction(Request $request)
	{

		$intemail = $request->query->get('email',NULL,true);
		$email = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('email'=>$intemail));
		if(empty($email)){
			$valid = 'true';
		}else{
			$valid = 'false';
		}
		echo $valid;
		exit;
	}


	public function checkUserNameAction(Request $request)
	{

		$mobile = $request->request->get('mobile');
		$mobile = $this->get('settong.toolManageRepo')->specialExpClean($mobile);

		$username = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('username' => $mobile));
		$profileMobile = $this->getDoctrine()->getRepository('UserBundle:Profile')->findOneBy(array('mobile'=>$mobile));
		$optionMobile = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('mobile'=>$mobile));

		if(empty($username) && empty($profileMobile) && empty($optionMobile)){
			return new Response('success');
		} else {
			return new Response('failed');
		}
		exit;
	}



	public function confirmAction()
	{

		$entity = new User();

		if ($this->has('security.csrf.token_manager')) {
			$csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
		} else {
			// BC for SF < 2.4
			$csrfToken = $this->has('form.csrf_provider')
				? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
				: null;
		}

		$detect = new MobileDetect();
		if($detect->isMobile() && $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}

		$this->get('session')->getFlashBag()->add(
			'success',"Dear Respected Customer, Thank you for being registered.Our Administrator will contact with You as soon as possible.Please wait until we response."
		);
		return $this->render('BinduBundle:'.$theme.':confirm.html.twig', array(
			'entity' => $entity,
			'csrf_token' => $csrfToken,
		));


	}

	/**
	 * Creates a new User entity.
	 *
	 */

	public function checkAction(Request $request)
	{

		$entity = new User();
		$form = $this->createLoginForm($entity);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$data = $form->getData();
			return $this->redirect($this->generateUrl('bindu_build'));
		}
		return $this->render('BinduBundle:Bindu:confirm.html.twig', array(
			'entity' => $entity,
			'form'   => $form->createView(),

		));
	}

	public function previewAction()
	{
		//$user = $this->getUser();
		//$entity = $user->getGlobalOption();
		return $this->render('BinduBundle:Bindu-old:preview.html.twig');

	}

	public function findAction()
	{
		$data = array();
		$entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findClientSubdomain($data);
		if(!empty($entities)){
			$pagination = $this->paginate($entities);
		}
		/* Device Detection code desktop or mobile */
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':find.html.twig', array(
			'entities' => $pagination,
		));


	}

	public function searchingAction(Request $request)
	{
		$data = $request->request->all();
		$entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findClientSubdomain($data['search']);
		if(!empty($entities)){
			$pagination = $this->paginate($entities);
		}else{
			$pagination = '';
		}
		/* Device Detection code desktop or mobile */
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':find.html.twig', array(
			'entities' => $pagination,
		));
	}


	public function partnerAction()
	{
		$entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('status'=>1));
		return $this->render('BinduBundle:Bindu:find.html.twig', array(
			'entities' => $entities,
		));

	}

	public function businessDirectoryAction()
	{
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':business.html.twig', array(
			'directory' => '',
		));


	}

	public function businessDirectoryDetailsAction($syndicate)
	{

		$syndicate = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->findOneBy(array('slug' => $syndicate));
		// $data = array('syndicate' => $syndicate->getId());
		// $entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBySubdomain($data);
		$entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('status'=>1,'syndicate' => $syndicate), array('name'=>'ASC'));

		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':find.html.twig', array(
			'entities' => $entities,
		));

	}

	public function locationDirectoryAction()
	{
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':location.html.twig');

	}

	public function locationDirectoryDetailsAction($location)
	{

		$entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('status'=>1,'location' => $location), array('name'=>'ASC'));

		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':find.html.twig', array(
			'entities' => $entities,
		));

	}

	public function businessLocationAction($business,$location)
	{
		$syndicate = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->findOneBy(array('slug'=>$business));
		$entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('status'=>1,'location'=>$location,'syndicate'=>$syndicate),array('name'=>'ASC'));
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':find.html.twig', array(
			'entities' => $entities,
		));

	}

	public function pageContentAction($slug)
	{
		$entity =$this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findOneBy(array('slug' => $slug));
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':page.html.twig', array(
			'entity' => $entity,
		));
	}

	public function contactAction()
	{
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':contact.html.twig');
	}

	public function aboutAction()
	{
		$entity =$this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findOneBy(array('slug'=>'about-us'));
		$teams =$this->getDoctrine()->getRepository('SettingContentBundle:SiteTeam')->findBy(array('businessSector'=>'portal','status'=> 1));
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':about.html.twig', array(
			'entity' => $entity,
			'teams' => $teams,
		));
	}


	public function serviceAction()
	{
		$detect = new MobileDetect();
		if( $detect->isMobile() &&  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Mobile';
		}
		return $this->render('BinduBundle:'.$theme.':product.html.twig');

	}

	public function receiveSMSAction(Request $request)
	{
		$data = $request->request->all();
		$mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
		$customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->insertContactCustomer($data,$mobile);
		$customerInbox = $this->getDoctrine()->getRepository('DomainUserBundle:CustomerInbox')->sendCustomerMessage($customer,$data,'sms');
		if( $customer->getGlobalOption()->isEmailIntegration() == 1 AND $customer->getGlobalOption()->getEmail() !="" )
		{
			$dispatcher = $this->container->get('event_dispatcher');
			$dispatcher->dispatch('setting_tool.post.email_receive', new ReceiveSmsEvent($customer->getGlobalOption(),$customerInbox));

		}
		return new Response('success');
	}

	public function receiveEmailAction(Request $request)
	{
		$data = $request->request->all();
		$customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->insertContactCustomer($data);
		$customerInbox = $this->getDoctrine()->getRepository('DomainUserBundle:CustomerInbox')->sendCustomerMessage($customer,$data,'email');

		if( $customer->getGlobalOption()->isSmsIntegration() == 1 AND $customer->getGlobalOption()->getMobile() !="" ) {
			$dispatcher = $this->container->get('event_dispatcher');
			$dispatcher->dispatch('setting_tool.post.sms_receive', new ReceiveSmsEvent($customer->getGlobalOption(), $customerInbox));

		}
		return new Response('success');


	}

	public function paginate($entities,$limit = 20)
	{

		$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
			$entities,
			$this->get('request')->query->get('page', 1)/*page number*/,
			$limit  /*limit per page*/
		);
		return $pagination;
	}


	public function applicationAction()
	{
		$slug = $_REQUEST['slug'];
		$app =$this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->findOneBy(array('slug'=>$slug));
		$applications =$this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->findBy(array('status'=>1));
		$entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findByApplicationDomain($slug);
		$testimonials =$this->getDoctrine()->getRepository('SettingToolBundle:ApplicationTestimonial')->findToTestimonials($app->getId());
		if(!empty($entities)){
			$pagination = $this->paginate($entities,12);
		}else{
			$pagination = '';
		}
		/* Device Detection code desktop or mobile */
		$detect = new MobileDetect();
		if( $detect->isMobile() OR  $detect->isTablet() ) {
			$theme = 'Frontend/Mobile';
		}else{
			$theme = 'Frontend/Desktop';
		}
		return $this->render('BinduBundle:'.$theme.':application.html.twig', array(
			'app' => $app,
			'applications' => $applications,
			'testimonials' => $testimonials,
			'entities' => $pagination,
		));


	}




}
