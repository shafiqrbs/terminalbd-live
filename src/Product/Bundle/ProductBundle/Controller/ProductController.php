<?php

namespace Product\Bundle\ProductBundle\Controller;

use Product\Bundle\ProductBundle\Entity\ProductGallery;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Product\Bundle\ProductBundle\Entity\Product;
use Product\Bundle\ProductBundle\Form\ProductType;

/**
 * Product controller.
 *
 */
class ProductController extends Controller
{

    /**
     * Lists all Product entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $search = $this->getRequest()->query->all();
        $user = $this->get('security.context')->getToken()->getUser();
        $entities = $em->getRepository('ProductProductBundle:Product')->getProducts($user->getId(),$search);
        $pagination = $this->paginate($entities);
        $collections = $em->getRepository('ProductProductBundle:Collection')->findBy(array('parent'=>NULL,'status'=>1),array('name'=>'asc'));

        $grouping = $em->getRepository('ProductProductBundle:CategoryGrouping')->getParentCategory($user->getId());
        $categoryArr = array();

        if($grouping){
            foreach($grouping as $row ){
               $categoryArr[] = $row['category_id'];
            }
        }

        $brands = $em->getRepository('SettingToolBundle:Branding')->getCategoryBrand($categoryArr);
        //\Doctrine\Common\Util\Debug::dump($brands);

        return $this->render('ProductProductBundle:Product:index.html.twig', array(
            'pagination' => $pagination,
            'collections' => $collections,
            'categories' => $grouping,
            'brands' => $brands,
        ));
    }

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            30  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Creates a new Product entity.
     *
     */
    public function createAction(Request $request)
    {
        $data = $request->request->all();

        $user = $this->get('security.context')->getToken()->getUser();

        $this->getDoctrine()->getRepository('ProductProductBundle:Product')->insertProduct($data,$user);
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been inserted successfully"
        );
        return $this->redirect($this->generateUrl('product_insert'));
    }



    public function uploadProductAction()
    {
        $entity = new Product();

        $user = $this->get('security.context')->getToken()->getUser();
        $this->get('settong.toolManageRepo')->createDirectory($user,'products');
        $entity ->upload($user);
    }

    public function uploadProductGalleryAction()
    {
        $entity = new ProductGallery();
        $user = $this->get('security.context')->getToken()->getUser();
        $entity ->upload($user);
    }

    /**
     * Lists all Product entities.
     *
     */
    public function insertAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entities = $em->getRepository('ProductProductBundle:Product')->findBy(array('user'=>$user,'status'=>2),array('created'=>'desc'));
        // \Doctrine\Common\Util\Debug::dump($entities);

        $productCategories = $em->getRepository('ProductProductBundle:Category')->findBy(array('status'=>1,'parent'=>NULL),array('name'=>'asc'));
        if($productCategories)
            $grouping = $em->getRepository('ProductProductBundle:CategoryGrouping')->findOneBy(array('user'=>$user));
        $array = array();
        if($grouping){

            $groups = $grouping->getCategories();
            foreach($groups as $row ){
                $array[] = $row->getId();
            }
        }
        $productCategories = $em->getRepository('ProductProductBundle:Category')->findBy(array('status'=>1,'parent'=>NULL),array('name'=>'asc'));
        $categories = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getSelectdDropdownCategories($productCategories,$array);
        $itemUnits = $this->getDoctrine()->getRepository('ProductProductBundle:Product')->getItemUnit();


        return $this->render('ProductProductBundle:Product:insert.html.twig', array(
            'entities' => $entities,
            'itemUnits' =>  $itemUnits,
            'categories' =>  $categories

        ));
    }

    /**
     * Lists all Product entities.
     *
     */
    public function multiinsertAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = $request->request->all();
        if(!empty($data['name'])){
            $this->getDoctrine()->getRepository('ProductProductBundle:Product')->multiInsert($data);
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('product'));
        }else{
            return $this->redirect($this->generateUrl('product_insert'));

        }
    }

    /**
     * Creates a form to create a Product entity.
     *
     * @param Product $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Product $entity)
    {
        $form = $this->createForm(new ProductType(), $entity, array(
            'action' => $this->generateUrl('product_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Product entity.
     *
     */
    public function newAction()
    {
        $entity = new Product();
        return $this->render('ProductProductBundle:Product:new.html.twig', array(
            'entity' => $entity

        ));
    }

    /**
     * Finds and displays a Product entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProductProductBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProductProductBundle:Product:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();


        $entity = $em->getRepository('ProductProductBundle:Product')->findOneBY(array('id'=>$id,'user'=>$user));

        $parentId = $entity->getParentCategory()->getId();

        $productCategories = $em->getRepository('ProductProductBundle:Category')->findBy(array('status'=>1,'parent' => $parentId),array('name'=>'asc'));

        $grouping = $em->getRepository('ProductProductBundle:CategoryGrouping')->findOneBy(array('user'=>$user));

        $categories = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getSelectedCategories($productCategories,$entity);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        $collections = $em->getRepository('ProductProductBundle:Collection')->findBy(array('parent'=>NULL,'status'=>1),array('name'=>'asc'));

        return $this->render('ProductProductBundle:Product:edit.html.twig', array(
            'entity'      => $entity,
            'collections'      => $collections,
            'categories'      => $categories,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    public function duplicateAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProductProductBundle:Product')->find($id);
        $newEntity = $this->getDoctrine()->getRepository('ProductProductBundle:Product')->insertDuplicate($entity);
        $this->getDoctrine()->getRepository('ProductProductBundle:Product')->updateDuplicateProduct($newEntity);
        $this->getDoctrine()->getRepository('ProductProductBundle:ProductCustomAttribute')->insertDuplicateProductAttribute($newEntity,$entity);
        $this->getDoctrine()->getRepository('ProductProductBundle:ProductGallery')->insertDuplicateProductGallery($newEntity,$entity);
        return $this->redirect($this->generateUrl('product'));



    }

    /**
     * Creates a form to edit a Product entity.
     *
     * @param Product $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Product $entity)
    {

        $form = $this->createForm(new ProductType(), $entity, array(
            'action' => $this->generateUrl('product_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing Product entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $data = $request->request->all();
        $entity = $em->getRepository('ProductProductBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $cat = array();
        if ($editForm->isValid()) {

            $categories     = $data['categories'];
            foreach($categories as $cid ){
                $cat[] = $em->getRepository('ProductProductBundle:Category')->findOneBy(array('id'=>$cid));
            }
            if (!empty($cat)) {
                $entity->setCategories($cat);
            }

            $entity->editUpload($entity->getUser()->getUserName());
            $em->flush();
            $this->getDoctrine()->getRepository('ProductProductBundle:Product')->updateCollection($entity,$data);
            $this->getDoctrine()->getRepository('ProductProductBundle:ProductCustomAttribute')->insertProductAttribute($entity,$data);
            $this->getDoctrine()->getRepository('ProductProductBundle:ProductGallery')->insertProductGallery($entity,$data);

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );

            return $this->redirect($this->generateUrl('product_edit', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('product_edit', array('id' => $id)));

        /*return $this->render('ProductProductBundle:Product:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));*/
    }



    /**
     * Deletes a Product entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProductProductBundle:Product')->find($id);

            if (!$entity) {

                throw $this->createNotFoundException('Unable to find Product entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product'));
    }

    /**
     * Creates a form to delete a Product entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    public function discountAction(Request $request )
    {

        $data = $request ->request->all();
        $this->getDoctrine()->getRepository('ProductProductBundle:Product')->insertProductCollection($data);
        exit;


    }

}
