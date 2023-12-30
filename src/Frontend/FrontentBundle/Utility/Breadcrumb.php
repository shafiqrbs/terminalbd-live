<?php

namespace Frontend\FrontentBundle\Utility;


use Doctrine\ORM\EntityManager;
use Product\Bundle\ProductBundle\Entity\Category;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class Breadcrumb {
    /** @var Router */
    protected $router;

    /** @var CategoryRepository */
    protected $repo;

    public function __construct(Router $router, EntityManager $em)
    {
        $this->router = $router;
        $this->repo = $em->getRepository('ProductProductBundle:Category');
    }

    public function getBreadcrumb($slug)
    {
        $category = $this->repo->findOneBy(array('slug'=>$slug));

        $breadcrumb = array();

        $categories = array();

        $this->buildCategoryTree($category, $categories);

        foreach ($categories as $cat) {
            $breadcrumb[] = array(
                'title' => $cat->getName(),
                'path'  => $this->router->generate('frontend_category', array('slug' => $cat->getSlug()))
            );
        }

        return $breadcrumb;
    }

    public function buildCategoryTree(Category $category = null, &$tree = array()) {

        if($category !== null) {
            $this->buildCategoryTree($category->getParent(), $tree);
            $tree[] = $category;
        }
    }

    public function getProductBreadcrumb($product)
    {
        $breadcrumb[] = array(
            'title' => $product->getUser(),
            'path'  => $this->router->generate('frontend_vendor', array('vendor' => $product->getUser()))
        );

    }


}