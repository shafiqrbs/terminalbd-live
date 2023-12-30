<?php

namespace Frontend\FrontentBundle\Controller;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\Branding;
use Product\Bundle\ProductBundle\Entity\Product;
use Setting\Bundle\ToolBundle\Entity\SubscribeEmail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Syndicate\Bundle\ComponentBundle\Entity\Education;
use Syndicate\Bundle\ComponentBundle\Entity\Vendor;

class DefaultController extends Controller
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

    private function getOrderLimit($searchKey,$searchValue,$orderKey,$orderValue,$limit)
    {

    }


    public function indexAction()
    {

        $entities                   = $this->getDoctrine()->getRepository('SyndicateComponentBundle:Education')->getVendorList();
        $sliders                    = $this->getDoctrine()->getRepository('SettingContentBundle:SiteSlider')->findBy(array('status'=>1),array('id'=>'DESC'));
        $siteContent                = $this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findBy(array('status'=>'1'),array('created'=>'desc'));
        $recentAdmission            = $this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findOneBy(array('slug'=>'recent-admission','status'=>'1'));
        $brands                     = $this->getDoctrine()->getRepository('SettingToolBundle:Branding')->getBrandingSponsor();
        $news                       = $this->getDoctrine()->getRepository('SettingContentBundle:News')->findAll();
        $notice                     = $this->getDoctrine()->getRepository('SettingContentBundle:NoticeBoard')->findAll();
        $admission                  = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->findAll();
        $testimonial                = $this->getDoctrine()->getRepository('SettingContentBundle:Testimonial')->getRandomTestimonial();
        $syndicates                 = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->getSyndicateBaseVendor();
        $institutes                 = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel')->findBy(array('parent'=>NULL),array('name'=>'asc'));

        return $this->render('FrontendBundle:Default:index.html.twig', array(
            'sliders'           => $sliders,
            'entities'          => $entities,
            'siteContent'       => $siteContent,
            'recentAdmission'   => $recentAdmission,
            'brands'            => $brands,
            'newses'            => $news,
            'notices'           => $notice,
            'admissions'        => $admission,
            'testimonial'       => $testimonial,
            'syndicates'        => $syndicates,
            'institutes'        => $institutes,
        ));
    }

    public function educationAction(Request $request)
    {

        $level = $request -> query->get('level');
        $entities                   = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getInstituteList( $syndicate = 1 , $level = NULL);
        $promotion                  = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('syndicate'=>1,'promotion'=>1));
        $institutes                 = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel')->findBy(array('parent'=>NULL),array('name'=>'asc'));
        $siteContent                = $this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findBy(array('status'=>'1'),array('created'=>'desc'));

        $pagination = $this->paginate($entities);
        return $this->render('FrontendBundle:Institute:listing.html.twig', array(
            'pagination'        => $pagination,
            'promotions'         => $promotion,
            'siteContent'       => $siteContent,
            'institutes'        => $institutes,
        ));
    }

    public function instituteDetailAction($subdomain)
    {

        $entity                   = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug'=>$subdomain));
        $institutes               = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel')->findBy(array('parent'=>NULL),array('name'=>'asc'));


        $homeModule = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getHomeModuleListing($entity);
        $admissionModule = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getSyndicateAdmissionListing($entity);
        $testimonial = $this->getDoctrine()->getRepository('SettingContentBundle:Testimonial')->findOneBy(array('isFeature'=>1),array('created'=>'desc'));
        $next = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getNext($entity);
        $previous = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getPrevious($entity);
        $relatedLocationVendors = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getRelatedLocationVendor($entity);

        return $this->render('FrontendBundle:Institute:instituteDetails.html.twig', array(
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

    public function tutorAction(Request $request)
    {

        $level = $request -> query->get('level');

        $entities                   = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getInstituteList( $syndicate = 2 , $level = NULL);
        $promotions                  = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('syndicate'=>2,'promotion'=>1));

        $pagination = $this->paginate($entities);
        return $this->render('FrontendBundle:Tutor:listing.html.twig', array(
            'pagination'        => $pagination,
            'promotions'        => $promotions,
        ));
    }


    public function createNewsletter(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em -> getRepository('SettingToolBundle:SubscribeEmail')->findOneBy($data);

        return $this->render('FrontendBundle:Default:content.html.twig' , array(
            'entity' => $entity,
        ));

    }


    public function scholarshipAction()
    {

        $entities       = $this->getDoctrine()->getRepository('SyndicateComponentBundle:Scholarship')->findBy(array('status'=>1));
        $promotion      = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('syndicate'=>1,'promotion'=>1));

        $pagination     = $this->paginate($entities);
        return $this->render('FrontendBundle:Education:scholarship.html.twig', array(
            'pagination'        => $pagination,
            'promotions'        => $promotion,
        ));
    }

    public function studyabroadAction()
    {

        $entities       = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('syndicate'=>1));
        $pagination     = $this->paginate($entities);
        return $this->render('FrontendBundle:Default:studyabroad.html.twig', array(
            'pagination'        => $pagination,
        ));
    }

    public function vendorAction()
    {

        $entities       = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('syndicate'=>1));
        $pagination     = $this->paginate($entities);
        return $this->render('FrontendBundle:Default:vendor.html.twig', array(
            'pagination'        => $pagination,
        ));
    }



    public function vendorListsAction()
    {

        $entities                   = $this->getDoctrine()->getRepository('SyndicateComponentBundle:Education')->getVendorList();
        $institutes                 = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel')->findBy(array('parent'=>NULL),array('name'=>'asc'));

        $pagination = $this->paginate($entities);
        return $this->render('FrontendBundle:Default:listing.html.twig', array(
            'pagination' => $pagination,
            'institutes' => $institutes,
        ));
    }




    public function categoryAction(Category $category)
    {

        $categoryRepository = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $entities = $this->getDoctrine()->getRepository('ProductProductBundle:Product')->getCategoryProducts($category->getSlug());
        $pagination = $this->paginate($entities);
        $cat = $categoryRepository->getParentCategoryByLevel($category);

        return $this->render('FrontendBundle:Default:category.html.twig', array(
            'pagination' => $pagination,
            'breadcrumb' => $this->get('bbpl.frontend.utility.breadcrumb')->getBreadcrumb($category->getSlug()),
            'brands'=> $category->getBranding(),
            'cat'   => $cat
        ));
    }


    public function brandAction(Branding $branding)
    {
        return new Response("under construction page for : " . $branding->getName());
    }

    public function vendorDetailsAction($vendor)
    {
        $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(  array('username'=>$vendor));
        $userID = $user->getId();
        $brands                     = $this->getDoctrine()->getRepository('SettingToolBundle:Branding')->getBrandingSponsor();
        $entities = $this->getDoctrine()->getRepository('ProductProductBundle:Product')->findBy(array('user'=> $userID));

        $pagination = $this->paginate($entities);
        return $this->render('FrontendBundle:Default:vendor.html.twig' , array(
            'pagination' => $pagination,
            'vendor'    => $user,
            'brands'    => $brands

        ));

    }

    public function productDetailsAction(Product $product)
    {
        $categoryRepository = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $cat = $categoryRepository->getParentCategoryByLevel($product->getCategories()->get(0));
        $brands                     = $this->getDoctrine()->getRepository('SettingToolBundle:Branding')->getBrandingSponsor();
        $features                   = $this->getDoctrine()->getRepository('ProductProductBundle:Product')->getRelatedProduct($cat);

        return $this->render('FrontendBundle:Default:productDetails.html.twig' , array(
            'entity'            =>  $product,
            'brands'            =>  $brands,
            'cat'               =>  $cat,
            'features'           =>  $features,



        ));
    }

    public function productReviewAction(Product $product, Request $request){

        $data = $request->request->all();
        $this->getDoctrine()->getRepository('ProductProductBundle:ProductReview')->addReview($data,$product);
        exit;

    }


    public function searchAction()
    {

        return $this->render('FrontendBundle:Default:search.html.twig');
    }


    public function getCaptcha()
    {
       // session_start();
        $text = rand(10000,99999);
        $_SESSION["vercode"] = $text;
        $height = 25;
        $width = 65;

        $image_p = imagecreate($width, $height);
        $black = imagecolorallocate($image_p, 0, 107, 143);
        $white = imagecolorallocate($image_p, 255, 255, 255);
        $font_size = 14;

        imagestring($image_p, $font_size, 5, 5, $text, $white);
        imagejpeg($image_p, null, 80);
    }

}
