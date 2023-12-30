<?php

namespace Frontend\FrontentBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends Controller
{

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

    public function admissionAction(Request $request)
    {

        $level = $request -> query->get('level');

        $pagination = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->getCourseUnderAdmission($level);
        $promotions = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->getPromotionAdmission();
        $entities = $this->paginate( $pagination,$limit= 2 );
        $admissions= array();
        foreach ($entities as $course){
            $admissions[$course->getId()] = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->getAdmissionGroupList($course->getCourse()->getId());
        }

        return $this->render('FrontendBundle:Module:admissionListing.html.twig',array(
            'entities' => $entities,
            'admissions' => $admissions,
            'promotions' => $promotions,
        ));

    }

    public function moduleAction($module)
    {

        exit;
        //$entities = $this->getDoctrine()->getRepository('SettingToolBundle:Course')->findBy(array('status'=>1),array('name'=>'DESC'));
        $pagination = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->getCourseUnderAdmission();
        $promotions = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->getPromotionAdmission();
        $entities = $this->paginate($pagination);
        $admissions= array();
        foreach ($entities as $course){
            $admissions[$course->getId()] = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->getAdmissionGroupList($course->getCourse()->getId());

        }

        return $this->render('FrontendBundle:Module:admission.html.twig' , array(
            'entities' => $entities,
            'admissions' => $admissions,
            'promotions' => $promotions,
        ));

    }


    public function subdomainModuleAction($subdomain,$module)
    {


        $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug'=>$subdomain));
        $moduleEntity = $this->getDoctrine()->getRepository('SettingToolBundle:Module')->findOneBy(array('menuSlug'=>$module));
        $syndicate = 'get'.$globalOption->getSyndicate()->getEntityName();
        $domain = $globalOption->getUser()->$syndicate();
        if(empty($moduleEntity)){
            $moduleEntity = $this->getDoctrine()->getRepository('SettingToolBundle:SyndicateModule')->findOneBy(array('menuSlug'=>$module));
        }
        $modules = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getModuleListing($globalOption,$moduleEntity->getModuleClass());
        $syndicateModules = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getSyndicateModuleListing($globalOption,$moduleEntity->getModuleClass());
        $entities = $this->getDoctrine()->getRepository('SettingContentBundle:'.$moduleEntity->getModuleClass())->findBy(array('user'=>$globalOption->getUser()));
        $promotions = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->getPromotionAdmission();

        return $this->render('FrontendBundle:Module:'.$module.'.html.twig' , array(
            'entities' => $entities,
            'module' => $module,
            'slug' => '',
            'promotions' => $promotions,
            'domain' => $domain,
            'modules' => $modules,
            'syndicateModules'=>$syndicateModules
        ));

    }

    public function subdomainContentAction($subdomain,$slug)
    {


        $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug'=>$subdomain));
        $syndicate = 'get'.$globalOption->getSyndicate()->getEntityName();
        $domain = $globalOption->getUser()->$syndicate();
        $modules = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getModuleListing($globalOption);
        $syndicateModules = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getSyndicateModuleListing($globalOption);
        $entity = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findBy(array('user'=>$globalOption->getUser(),'menuSlug'=>$slug));
        $promotions = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->getPromotionAdmission();

        return $this->render('FrontendBundle:Module:page.html.twig' , array(
            'entity' => $entity,
            'slug' => '',
            'promotions' => $promotions,
            'domain' => $domain,
            'modules' => $modules,
            'syndicateModules'=>$syndicateModules
        ));

    }



    public function subdomainModuleContentAction($subdomain,$module,$slug)
    {

        $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug'=>$subdomain));
        $syndicate = 'get'.$globalOption->getSyndicate()->getEntityName();
        $domain = $globalOption->getUser()->$syndicate();
        $moduleEntity = $this->getDoctrine()->getRepository('SettingToolBundle:Module')->findOneBy(array('menuSlug'=>$module));
        if(!$moduleEntity){
            $moduleEntity = $this->getDoctrine()->getRepository('SettingToolBundle:SyndicateModule')->findOneBy(array('menuSlug'=>$module));
        }

        $entities = $this->getDoctrine()->getRepository('SettingContentBundle:'.$moduleEntity->getModuleClass())->findBy(array('user'=>$globalOption->getUser()));
        $modules = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getModuleListing($globalOption,$moduleEntity->getModuleClass());
        $syndicateModules = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getSyndicateModuleListing($globalOption,$moduleEntity->getModuleClass());
        $promotions = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->getPromotionAdmission();
        return $this->render('FrontendBundle:Module:'.$module.'.html.twig' , array(
            'entities' => $entities,
            'module' => $module,
            'slug' => $slug,
            'promotions' => $promotions,
            'domain' => $domain,
            'modules' => $modules,
            'syndicateModules'=>$syndicateModules
        ));
    }

    public function subdomainModuleFileDownloadAction($subdomain,$module,$slug)
    {

        $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug'=>$subdomain));
        $moduleEntity = $this->getDoctrine()->getRepository('SettingToolBundle:Module')->findOneBy(array('menuSlug'=>$module));
        if(!$moduleEntity){
            $moduleEntity = $this->getDoctrine()->getRepository('SettingToolBundle:SyndicateModule')->findOneBy(array('menuSlug'=>$module));
        }
        $entities = $this->getDoctrine()->getRepository('SettingContentBundle:'.$moduleEntity->getModuleClass())->findOneBy(array('user'=>$globalOption->getUser(),'slug'=>$slug));

    }

    public function moduleLoading()
    {
        $entity  = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->getAdmissionLists();

        $em = $this->getDoctrine()->getManager();
        $entity = $em -> getRepository('SettingContentBundle:SiteContent')->findOneBy(array('id' =>3 ));
        return $this->render('FrontendBundle:Module:admission.html.twig' , array(
            'entity' => $entity,
        ));
    }

    public function modulePageAction($subdomain,$module,$slug)
    {

        $entity                   = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug'=>$subdomain));
        $institutes               = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel')->findBy(array('parent'=>NULL),array('name'=>'asc'));


        $homeModule = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getModuleListing($entity);
        $admissionModule = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getSyndicateModuleListing($entity);
        $user = $entity->getUser();
        $testimonial = $this->getDoctrine()->getRepository('SettingContentBundle:Testimonial')->findOneBy(array('user' => $user ,'isFeature'=>1),array('created'=>'desc'));
        $next = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getNext($entity);
        $previous = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getPrevious($entity);
        $relatedLocationVendors = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getRelatedLocationVendor($entity);
        return $this->render('FrontendBundle:Module:admissionDetails.html.twig', array(
            'entity' => $entity,
            'admissionModule' => $admissionModule,
            'testimonial' => $testimonial,
            'homeModule' => $homeModule,
            'next' => $next,
            'previous' => $previous,
            'relatedLocationVendors' => $relatedLocationVendors,
            'institutes' => $institutes,
        ));
    }
}
