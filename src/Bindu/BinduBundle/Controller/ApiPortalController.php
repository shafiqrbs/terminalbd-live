<?php

namespace Bindu\BinduBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Setting\Bundle\AppearanceBundle\Entity\TemplateCustomize;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiPortalController extends Controller
{


    public function portalInformationAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data = array(
                'name' => 'Right Brain Solution Ltd.',
                'mobile' => '01828148148',
                'email' => 'info@rightbrainsolution.com',
                'website' => 'www.rightbrainsolution.com',
                'address' => "Pul Tower, Plot no 29, Gausul Azam Avenue, Sector-14, Uttara, Dhaka-1230",
                'lat' => "23.869141",
                'lon' => "90.389381",

            );
        }else{
            return new Response('Unauthorized access.', 401);

        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function domainListAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->apiDomains();
        }else{
            return new Response('Unauthorized access.', 401);

        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function appsAction(Request $request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data =$this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->getAppModules();
        }else{
            return new Response('Unauthorized access.', 401);

        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function appDetailsAction(Request $request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $id = $_REQUEST['id'];
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data =$this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->getAppModuleDetails($id);
        }else{
            return new Response('Unauthorized access.', 401);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }
    public function appWiseCustomerAction(Request $request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $id = $_REQUEST['id'];
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            $data =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->apiAppDomain($id);
        }else{
            return new Response('Unauthorized access.', 401);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function userRegisterAction(Request $request)
    {
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        if ($request->headers->get('X-API-KEY') != $key and $request->headers->get('X-API-VALUE') != $value) {
            return new Response('Unauthorized access.', 401);
        }else{

            $data = $request->request->all();

            $name = $data['name'];
            $mobile = $data['mobile'];
            $email = $data['email'];
            $address = $data['address'];
            $em = $this->getDoctrine()->getManager();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($mobile);
            $user = $em->getRepository('UserBundle:User')->findOneBy(array('username'=> $mobile,'userGroup'=> 'customer','enabled'=> 1));

            /* @var $user User */

            if(empty($user)){

                $user = new User();
                $a = mt_rand(1000,9999);
                $user->setPlainPassword($a);
                $user->setEnabled(true);
                $user->setUsername($mobile);
                if(empty($data['email'])){
                    $user->setEmail($mobile.'@gmail.com');
                }else{
                    $user->setEmail($email);
                }
                $user->setRoles(array('ROLE_CUSTOMER'));
                $user->setUserGroup('customer');
               $user->setEnabled(1);
                $em->persist($user);
                $em->flush();

                $profile = new Profile();
                $profile->setUser($user);
                $profile->setName($name);
                $profile->setMobile($mobile);
                $profile->setAddress($address);
                $em->persist($profile);
                $em->flush();

            }else{

                $a = mt_rand(1000,9999);
                $user->setPlainPassword($a);
                $this->get('fos_user.user_manager')->updateUser($user);
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($user,$a));

            }
            $returnData['user_id'] = (int) $user->getId();
            $returnData['username'] = $user->getUsername();
            $returnData['password'] = $a;
            $returnData['msg'] = "valid";
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }


}
