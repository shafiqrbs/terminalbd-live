<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Form\AccessoriesType;
use Appstore\Bundle\RestaurantBundle\Form\StockType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * StockController controller.
 *
 */
class StockController extends Controller
{

    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_DOMAIN_RESTAURANT_MANAGER,ROLE_DOMAIN")
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $data = $_REQUEST;
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getAccessoriesParticular($config,$data);
        $pagination = $this->paginate($entities);
        $categories = $this->getDoctrine()->getRepository('RestaurantBundle:Category')->findBy(array('restaurantConfig'=>$config,'status'=>1),array('name'=>"ASC"));
        $entity = new Particular();
        $form = $this->createCreateForm($entity);
        return $this->render('RestaurantBundle:Stock:index.html.twig', array(
            'pagination' => $pagination,
            'categories' => $categories,
            'entity' => $entity,
            'formShow' => 'hide',
            'searchForm' => $data,
            'form'   => $form->createView(),
        ));

    }

    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getAccessoriesParticular($config);
        $pagination = $this->paginate($entities);
        $entity = new Particular();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setRestaurantConfig($config);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('restaurant_stock'));
        }
        $this->get('session')->getFlashBag()->add(
            'error',"Required field does not input"
        );
        $categories = $this->getDoctrine()->getRepository('RestaurantBundle:Category')->findBy(array('restaurantConfig' => $config,'status' => 1),array('name'=>"ASC"));
        return $this->render('RestaurantBundle:Stock:index.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'categories' => $categories,
            'formShow'   => 'show',
            'form'       => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Particular $entity)
    {
        $global = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new StockType($global), $entity, array(
            'action' => $this->generateUrl('restaurant_stock_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;

    }


    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_DOMAIN_RESTAURANT_MANAGER,ROLE_DOMAIN")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Particular')->findOneBy(array('restaurantConfig'=>$config,'id'=>$id));
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getAccessoriesParticular($config);
        $pagination = $this->paginate($entities);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity);
        $categories = $this->getDoctrine()->getRepository('RestaurantBundle:Category')->findBy(array('restaurantConfig'=> $config,'status' => 1),array('name' => "ASC"));

        return $this->render('RestaurantBundle:Stock:index.html.twig', array(
            'pagination'        => $pagination,
            'entity'            => $entity,
            'categories'        => $categories,
            'formShow'          => 'show',
            'form'              => $editForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Particular $entity)
    {
        $global = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new StockType($global), $entity, array(
            'action' => $this->generateUrl('restaurant_stock_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_DOMAIN_RESTAURANT_MANAGER,ROLE_DOMAIN")
     */
    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getAccessoriesParticular($config);
        $pagination = $this->paginate($entities);
        $entity = $em->getRepository('RestaurantBundle:Particular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload() && !empty($entity->getFile())){
                $entity->removeUpload();
            }
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->updateProductionElementPrice($entity,$entity->getPurchasePrice());
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->updateProductionPrice($config->getId());
            return $this->redirect($this->generateUrl('restaurant_stock'));
        }
        $categories = $this->getDoctrine()->getRepository('RestaurantBundle:Category')->findBy(array('restaurantConfig'=>$config,'status'=>1),array('name'=>"ASC"));
        return $this->render('RestaurantBundle:Stock:index.html.twig', array(
            'pagination'      => $pagination,
            'entity'      => $entity,
            'categories'      => $categories,
            'formShow'            => 'show',
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Particular entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Particular')->findOneBy(array('restaurantConfig' => $config,'id'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return $this->redirect($this->generateUrl('restaurant_stock'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Particular $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('restaurant_stock'));
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('RestaurantBundle:Particular')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find particular entity.');
        }
        $setField = 'set'.$data['name'];
        if($setField == "Price"){
            $price = abs($data['value']);
            $entity->setPrice($price);
        }elseif($data['name'] == "PurchasePrice") {
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->updateProductionElementPrice($entity, $price);
            $config = $entity->getRestaurantConfig()->getId();
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->updateProductionPrice($config);
        }else{
            $price = $data['value'];
            $entity->$setField($price);
        }
        $em->flush();
        return new Response('success');

    }


    public function productionUpdateAction()
    {
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig()->getId();
        $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->updateProductionPrice($config);
        return new Response('success');

    }

    /**
     * Displays a form to edit an existing Particular entity.
     *
     */
    public function copyAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $entity Particular */
        $entity = $em->getRepository('RestaurantBundle:Particular')->find($id);
        $newEntity = new Particular();
        $em = $this->getDoctrine()->getManager();
        $newEntity->setRestaurantConfig($entity->getRestaurantConfig());
        $newEntity->setName($entity->getName());
        $newEntity->setCategory($entity->getCategory());
        $newEntity->setProductionType($entity->getProductionType());
        $newEntity->setService($entity->getService());
        $newEntity->setUnit($entity->getUnit());
        $newEntity->setPrice($entity->getPrice());
        $newEntity->setPurchasePrice($entity->getPurchasePrice());
        $em->persist($newEntity);
        $em->flush();
        $this->getDoctrine()->getRepository('RestaurantBundle:ProductionElement')->copyElement($newEntity,$entity);
        return $this->redirect($this->generateUrl('restaurant_production_edit',array('id'=>$newEntity->getId())));
    }


}
