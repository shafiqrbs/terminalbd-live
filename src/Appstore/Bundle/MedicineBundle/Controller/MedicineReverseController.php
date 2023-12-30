<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\MedicineStockItemType;
use Appstore\Bundle\MedicineBundle\Form\PurchaseItemType;
use Appstore\Bundle\MedicineBundle\Form\PurchaseType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * Vendor controller.
 *
 */
class MedicineReverseController extends Controller
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
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineReverse')->findAll();
        $pagination = $this->paginate($entities);
        return $this->render('MedicineBundle:MedicineReverse:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function reversePurchaseAction($id)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineReverse')->findOneBy(array('medicineConfig' => $config, 'medicinePurchase' => $id));
        return $this->render("MedicineBundle:MedicineReverse:purchase.html.twig", array(
            'entity' => $entity,
        ));
    }

	public function reverseSalesAction($id)
	{
		$config = $this->getUser()->getGlobalOption()->getMedicineConfig();
		$entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineReverse')->findOneBy(array('medicineConfig' => $config, 'medicineSales' => $id));
		return $this->render("MedicineBundle:MedicineReverse:sales.html.twig", array(
			'entity' => $entity,
		));
	}
}
