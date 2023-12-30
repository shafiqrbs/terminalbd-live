<?php

namespace Bindu\BinduBundle\Controller;

use Core\UserBundle\Entity\User;
use Desktop\Bundle\Service\MobileDetect;
use Setting\Bundle\ContentBundle\Entity\Page;
use Core\UserBundle\Form\SignupType;
use Core\UserBundle\Form\UserConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SettingBuilderController extends Controller
{


    public function buildAction()
    {

        $entity = $this->getUser()->getGlobalOption();

        $themes = $this->getDoctrine()->getRepository('SettingToolBundle:Theme')->findBy(array('status'=>1));
        $modules = $this->getDoctrine()->getRepository('SettingToolBundle:Module')->findBy(array('status'=>1));
        $syndicateModules = $this->getDoctrine()->getRepository('SettingToolBundle:SyndicateModule')->getSpecificModule($entity);
        $locations = $this->getDoctrine()->getRepository('SettingLocationBundle:Location')->findBy(array('level'=>2));

        $district = $entity->getContactPage()->getDistrict();
        if(!empty($district)){
            $thana = $entity->getContactPage()->getThana()->getId();
            $child = $this->getDoctrine()->getRepository('SettingLocationBundle:Location')->getUnderChild($district,$thana);
        }else{
            $child ='';
        }

        $appModules = $this->getDoctrine()->getRepository('SettingToolBundle:AppModule')->findBy(array('status'=>1));

        if($entity->getStatus() == 1){
            return $this->redirect($this->generateUrl('homepage'));
        }
        $detect = new \Frontend\FrontentBundle\Service\MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':builder.html.twig', array(
            'entity'            => $entity,
            'locations'         => $locations,
            'child'             => $child,
            'themes'            => $themes,
            'modules'           =>$modules,
            'syndicateModules'  => $syndicateModules,
            'appModules'        => $appModules

        ));
    }

    public function finishAction(Request $request)
    {
        //echo $subDomain = $request->request->all('status');

        $user = $this->getUser();
        $entity = $user->getGlobalOption();
        $entity->setStatus(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        exit;

    }

    /**
     * Updatess a Site setting entity.
     *
     */

    public function checkingSubdomainAction(Request $request)
    {
        $subDomain = $request->request->all('subDomain');
        $subDomain = $subDomain['subDomain'];
        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subDomain));;
        if($entity){
            echo 'false';
        }else{
            echo 'true';
        }
        exit;
    }
    /**
     * Updatess a Site setting entity.
     *
     */

    public function templateAction(Request $request)
    {

        $data= $request->request->all();
        $user = $this->getUser();
        $entity = $user->getSiteSetting();
        $theme = $this->getDoctrine()->getRepository('SettingToolBundle:Theme')->find($data['themeId']);
        $entity->setTheme($theme);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        exit;


    }

    /**
     * Update home entity.
     *
     */

    public function homepageAction(Request $request)
    {

        $data= $request->request->all();
        $user = $this->getUser();
        $entity = $user->getGlobalOption()->getHomePage();
        $entity->setName($data['name']);
        $entity->setContent($data['content']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        exit;

    }

    /**
     * Update Template Customize entity.
     *
     */

    public function customizeAction(Request $request)
    {

        $data= $request->request->all();
        $file = $request->files->all();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:TemplateCustomize')->findOneBy(array('globalOption'=>$globalOption));
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:TemplateCustomize')->updateTemplateCustomize($entity, $data, $file);
        exit;

    }

    /**
     * Update Template Footer Setting  entity.
     *
     */

    public function footerAction(Request $request)
    {

        $data= $request->request->all();
        $user = $this->getUser();
        $globalOption = $user->getGlobalOption()->getId();

        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:FooterSetting')->findOneBy(array('globalOption'=>$globalOption));
        $this->getDoctrine()->getRepository('SettingToolBundle:FooterSetting')->updateFooterSetting($entity, $data);
        exit;

    }


    /**
     * Updatess a Global Option entity.
     *
     */

    public function optionAction(Request $request)
    {

        $data= $request->request->all();
        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();


        if(isset($data['mobile']) and $data['mobile'] != '') {
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
        }else{
                $mobile = '';
        }
        $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->optionUpdate($globalOption,$data,$mobile);
        exit;
    }

    public function findChildAction(Request $request)
    {

        $parent = $request->request->get('district');
        $child = $this->getDoctrine()->getRepository('SettingLocationBundle:Location')->getUnderChild($parent,'');
        return new Response($child);
    }

    public function contactAction(Request $request){

        $data= $request->request->all();
        $user = $this->getUser();
        $globalOption = $user->getGlobalOption()->getId();

        $this->getDoctrine()->getRepository('SettingContentBundle:ContactPage')->contactUpdate($globalOption,$data);
        exit;
    }

    public function sitesettingAction(Request $request){

        $data= $request->request->all();
        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();
        $this->getDoctrine()->getRepository('SettingToolBundle:SiteSetting')->moduleUpdate($globalOption,$data);
        exit;
    }

    public function aboutAction(Request $request){

        $data= $request->request->all();
        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();
        $entity = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'menu'=>'About us'));
        if(empty($entity)){
            $entity = new Page();
        }
        $entity->setUser($user);
        $entity->setName($data['name']);
        $entity->setMenu($data['name']);
        $entity->setContent($data['content']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        exit;
    }

    public function adsToolAction(Request $request){

        $data= $request->request->all();
        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();
        $this->getDoctrine()->getRepository('SettingToolBundle:AdsTool')->adsToolUpdate($globalOption,$data);
        exit;
    }




    public function getUploadRootDir()
    {
        return WEB_PATH . DIRECTORY_SEPARATOR . $this->getUploadDir();
    }

    public function getUploadDir()
    {
        return 'uploads/domain/';
    }


}
