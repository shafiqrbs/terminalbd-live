<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicineStockHouse;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStockHouseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Entity\MedicineParticular;
use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Appstore\Bundle\MedicineBundle\Form\HouseStockInType;
use Appstore\Bundle\MedicineBundle\Form\HouseStockOutType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * StockHouse controller.
 *
 */
class StockHouseController extends Controller
{

    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }


    /**
     * Lists all Vendor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStockHouse')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        $stockHouses = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('particularType' => 10));
        return $this->render('MedicineBundle:StockHouse:index.html.twig', array(
            'entities' => $pagination,
            'stockHouses' => $stockHouses,
            'searchForm' => $data,
        ));
    }

    /**
     * Lists all Vendor entities.
     *
     */
    public function stockDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStockHouse')->findStockDetails($config,$data);
        $pagination = $this->paginate($entities);
        $stockHouses = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('particularType' => 10));
        return $this->render('MedicineBundle:StockHouse:stockDetails.html.twig', array(
            'entities' => $pagination,
            'stockHouses' => $stockHouses,
            'searchForm' => $data,
        ));
    }

    /**
     * Creates a new Vendor entity.
     *
     */
    public function createInAction(Request $request) {

        $entity = new MedicineStockHouse();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig' => $config),array('particularType'=>'ASC'));
        $form = $this->createStockInForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $entity->setMedicineConfig($config);
            $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($entity->getStockName());
            $entity->setMedicineStock($stock);
            $entity->setStockName($stock->getName());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('medicine_stockhouse'));
        }
        return $this->render('MedicineBundle:StockHouse:stockIn.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function stockInAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = new MedicineStockHouse();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $stockItemForm = $this->createStockInForm( $entity);
        return $this->render('MedicineBundle:StockHouse:stockIn.html.twig', array(
            'entity' => $entity,
            'form' => $stockItemForm->createView(),
        ));
    }

    private function createStockInForm(MedicineStockHouse $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $form = $this->createForm(new HouseStockInType($config), $entity, array(
            'action' => $this->generateUrl('medicine_stockhouse_in_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    public function stockOutAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = new MedicineStockHouse();
        $stockItemForm = $this->createStockOutForm($entity);
        return $this->render('MedicineBundle:StockHouse:stockOut.html.twig', array(
            'entity' => $entity,
            'form' => $stockItemForm->createView(),
        ));
    }

    /**
     * Creates a new Vendor entity.
     *
     */
    public function createOutAction(Request $request) {

        $entity = new MedicineStockHouse();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $form = $this->createStockOutForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findOneBy(array('medicineConfig' => $config,'name' => $entity->getStockName()));
            $remainingQnt = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStockHouse')->getStockRemainingQnt($stock,$entity->getWearHouse());
            if($remainingQnt >= $entity->getStockOut()){
                $em = $this->getDoctrine()->getManager();
                $entity->setMedicineConfig($config);
                $entity->setMedicineStock($stock);
                $entity->setStockName($stock->getName());
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',"Data has been inserted successfully"
                );
                return $this->redirect($this->generateUrl('medicine_stockhouse'));
            }else{
                $this->get('session')->getFlashBag()->add(
                    'error',"Quantity must be less or equal the remaining quantity "
                );
                return $this->render('MedicineBundle:StockHouse:stockOut.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
            }
        }
        return $this->render('MedicineBundle:StockHouse:stockOut.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    private function createStockOutForm(MedicineStockHouse $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $form = $this->createForm(new HouseStockOutType($config), $entity, array(
            'action' => $this->generateUrl('medicine_stockhouse_out_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $em->getRepository('MedicineBundle:MedicineStockHouse')->findOneBy(array('medicineConfig' => $config , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('MedicineBundle:StockHouse:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction(MedicineStockHouse $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $remainingQnt = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStockHouse')->getStockRemainingQnt($entity->getMedicineStock(),$entity->getWearHouse());
        if($config == $entity->getMedicineConfig()->getId() and $remainingQnt >= $entity->getStockIn()){
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been deleted successfully"
            );
        }
        return $this->redirect($this->generateUrl('medicine_stockhouse_details'));
    }

    public function autoSearchAction()
    {
       $item = trim($_REQUEST['q']);
       if ($item) {
           $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStockHouse')->searchAutoStock($item,$config);
        }
        return new JsonResponse($item);
    }

}
