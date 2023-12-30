<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\UserBundle\Controller;

use Frontend\FrontentBundle\Service\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SecurityController extends Controller
{

    public function loginAction(Request $request)
    {

        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            return $this->redirect($this->generateUrl('homepage'));
        }
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        if (class_exists('\Symfony\Component\Security\Core\Security')) {
            $authErrorKey = Security::AUTHENTICATION_ERROR;
            $lastUsernameKey = Security::LAST_USERNAME;
        } else {
            // BC for SF < 2.6
            $authErrorKey = SecurityContextInterface::AUTHENTICATION_ERROR;
            $lastUsernameKey = SecurityContextInterface::LAST_USERNAME;
        }

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }
        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }
        if ($error) {
            $error = $error->getMessage();
        }
	    $slides = $this->getDoctrine()->getRepository('SettingContentBundle:SiteSlider')->findBy(array(),array('id'=>'DESC'));
	    $apps =$this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->findBy(array('status'=>1));
	    $clients =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBySubdomain();
	    $testimonials =$this->getDoctrine()->getRepository('SettingContentBundle:Testimonial')->findBy(array('status'=>1));
	    return $this->renderLogin(array(
            'last_username'         => $lastUsername,
            'error'                 => $error,
            'slides'                => $slides,
            'apps'                  => $apps,
            'testimonials'          => $testimonials,
            'clients'               => $clients,
            'csrf_token'            => $csrfToken,
        ));
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {

        $detect = new MobileDetect();
        $currentDomain = $_SERVER['SERVER_NAME'];
	    if( $detect->isMobile() OR  $detect->isTablet() ) {
            if($currentDomain == 'www.poskeeper.com') {
                return $this->render('BinduBundle:Frontend/Mobile:index.html.twig', $data);
            }else{
                return $this->render('UserBundle:Security/Mobile:login.html.twig', $data);
            }
	    }else{
	        if($currentDomain == 'www.poskeeper.com'){
                return $this->render('BinduBundle:Frontend/Desktop:index.html.twig', $data);
            }else{
                return $this->render('UserBundle:Security:login.html.twig', $data);
            }
        }

    }

    public function domainLoginAction($subdomain,Request $request)
    {

        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        if (class_exists('\Symfony\Component\Security\Core\Security')) {
            $authErrorKey = Security::AUTHENTICATION_ERROR;
            $lastUsernameKey = Security::LAST_USERNAME;
        } else {
            // BC for SF < 2.6
            $authErrorKey = SecurityContextInterface::AUTHENTICATION_ERROR;
            $lastUsernameKey = SecurityContextInterface::LAST_USERNAME;
        }
        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }
        if ($error) {
            $error = $error->getMessage();
        }
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();

        $authChecker = $this->container->get('security.authorization_checker');
        $router = $this->container->get('router');

        if ($authChecker->isGranted('ROLE_CUSTOMER')) {
            return new RedirectResponse($router->generate('webservice_customer_home',array('subdomain'=> $subdomain )), 307);
        }elseif (!empty($authChecker)) {
            return $this->redirect($this->generateUrl('landing_page'));
        }elseif (!$authChecker->isGranted('ROLE_CUSTOMER')) {
            return new RedirectResponse($router->generate('homepage'), 307);
            $referer = $this->getRequest()->headers->get('referer');
            return $this->redirect($referer);
        }/*else{
            return $this->renderDomainLogin(array(
                'last_username' => $lastUsername,
                'error' => $error,
                'csrf_token' => $csrfToken,
                'globalOption'  => $globalOption,
            ),$themeName);
        }*/
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderDomainLogin(array $data,$themeName)
    {
        $detect = new MobileDetect();
        if( ! $detect->isMobile() && ! $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':login.html.twig', $data);
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction()
    {

        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
       // return $this->render('FrontendBundle:'.$theme.':login.html.twig', $data);
       // return $this->render('UserBundle:Security:login.html.twig', $data);

    }
}
