<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\ItemGallery;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Appstore\Bundle\InventoryBundle\Form\EditItemType;
use Appstore\Bundle\InventoryBundle\Form\ItemSearchType;
use Appstore\Bundle\InventoryBundle\Form\ItemType;
use Appstore\Bundle\InventoryBundle\Form\ItemWebType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Item controller.
 *
 */
class ItemController extends Controller
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
     * Lists all Item entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Item')->findWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);

        //$formSearch = $this->searchCreateForm(new Item());
        return $this->render('InventoryBundle:Item:index.html.twig', array(
            'entities' => $pagination,
            'config' => $inventory,
            'searchForm' => $data
            //'search' => $formSearch->createView(),
        ));
    }

    /**
     * Creates a form to create a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function searchCreateForm(Item $entity)
    {
        $em = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping');
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new ItemSearchType($inventoryConfig,$em), $entity, array(
            'action' => $this->generateUrl('item'),
            'method' => 'GET'
        ));
        return $form;
    }

    /**
     * Creates a new Item entity.
     *
     */
    public function createAction(Request $request)
    {
        /* @var $inventory InventoryConfig  */

        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getManager();
        $entity = new Item();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        $name = $data['item']['name'];
        $categoryName = $data['item']['category'];
        $brandName = (isset($data['item']['brand']) and $data['item']['brand']) ? $data['item']['brand'] :'';
        $vendorName = (isset($data['item']['vendor']) and $data['item']['vendor']) ? $data['item']['vendor'] :'';
        if ($form->isValid()) {
            $checkData = $this->getDoctrine()->getRepository('InventoryBundle:Item')->checkDuplicateSKU($inventory,$data);
            $barcode = (isset($data['item']['barcode']) and $data['item']['barcode']) ? $data['item']['barcode']:'';
            $barcodeCount = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getExistBarcode($inventory->getId(),$barcode);
            if($checkData['count'] != 0 ) {
                $this->get('session')->getFlashBag()->add(
                    'notice',"Item already exist, Please change add another item name"
                );
            }elseif($entity->getBarcode() and $barcodeCount > 0){
                $this->get('session')->getFlashBag()->add(
                    'notice',"This barcode already exist, Please change add another item name"
                );
            }else{
                $entity->setInventoryConfig($inventory);
                $entity->setMasterItem($checkData['masterItem']);
                if($inventory->getIsVendor() == 1){
                    $vendor = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getExistVendor($inventory,$vendorName);
                    $entity->setVendor($vendor);
                }
                if($inventory->getIsBrand() == 1){
                    $brand = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getExistBrand($inventory,$brandName);
                    $entity->setBrand($brand);
                }
                $category = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getExistCategory($inventory,$categoryName);
                $entity->setCategory($category);
                $masterItem = $this->getDoctrine()->getRepository('InventoryBundle:Item')->getExistMasterItem($inventory,$category,$name);
                $entity->setMasterItem($masterItem);
                if(empty($entity->getItemUnit())){
                    $unit = $this->getDoctrine()->getRepository('SettingToolBundle:ProductUnit')->find(4);
                    $entity->setItemUnit($unit);
                    $entity->getMasterItem()->setProductUnit($unit);
                }
                if(empty($entity->getBarcode()) and empty($entity->getModel())){
                    $time = new \DateTime();
                    $bar = (int) $time->getTimestamp();
                    $entity->setBarcode($bar);
                }elseif(empty($entity->getBarcode()) and !empty($entity->getModel())){
                    $entity->setBarcode($entity->getModel());
                }
                // $entity->upload();
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', "Item has been added successfully"
                );
                return $this->redirect($this->generateUrl('item_new', array('id' => $entity->getId())));
            }
        }
        $items = $this->getDoctrine()->getRepository("InventoryBundle:Product")->getMasterItems($inventory);
        $brands = $this->getDoctrine()->getRepository("InventoryBundle:Product")->getMasterItems($inventory);
        $vendors = $this->getDoctrine()->getRepository("InventoryBundle:Product")->getMasterItems($inventory);
        $categories = $this->getDoctrine()->getRepository("ProductProductBundle:Category")->categoryInventoryTree($inventory);
        return $this->render('InventoryBundle:Item:new.html.twig', array(
            'entity' => $entity,
            'items' => $items,
            'categories' => $categories,
            'vendors' => $vendors,
            'brands' => $brands,
            'inventory' => $inventory,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Creates a form to create a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Item $entity)
    {
        $groupRep = $this->getDoctrine()->getRepository('ProductProductBundle:ItemGroup');
        $catRep = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new ItemType($inventoryConfig,$groupRep,$catRep), $entity, array(
            'action' => $this->generateUrl('item_create'),
            'method' => 'POST',
            'attr' => array(
                'id' => 'item',
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to create a new Item entity.
     *
     */
    public function newAction()
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getManager();
        $entity = new Item();
        $form   = $this->createCreateForm($entity);
        $items = $this->getDoctrine()->getRepository("InventoryBundle:Product")->getMasterItems($inventory);
        $categories = $this->getDoctrine()->getRepository("ProductProductBundle:Category")->categoryInventoryTree($inventory);
        $brands = $this->getDoctrine()->getRepository("InventoryBundle:ItemBrand")->brandInventoryTree($inventory);
        $vendors = $this->getDoctrine()->getRepository("InventoryBundle:Vendor")->vendorInventoryTree($inventory);
        return $this->render('InventoryBundle:Item:new.html.twig', array(
            'entity' => $entity,
            'items' => $items,
            'categories' => $categories,
            'vendors' => $vendors,
            'brands' => $brands,
            'inventory' => $inventory,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Item entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

       return $this->render('InventoryBundle:Item:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Item entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $em->getRepository('InventoryBundle:Item')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:Item:edit.html.twig', array(
            'entity'        => $entity,
            'inventory'     => $inventory,
            'form'          => $editForm->createView(),

        ));
    }


    /**
     * Creates a form to edit a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Item $entity)
    {
        $groupRep = $this->getDoctrine()->getRepository('ProductProductBundle:ItemGroup');
        $catRep = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new EditItemType($inventoryConfig,$groupRep,$catRep), $entity, array(
            'action' => $this->generateUrl('item_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'id' => 'item',
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing Item entity.
     *
     */
    public function editWebAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Item')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $editForm = $this->createWebForm($entity);
        return $this->render('InventoryBundle:Item:web.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
     * Creates a form to edit a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createWebForm(Item $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new ItemWebType($inventory), $entity, array(
            'action' => $this->generateUrl('item_web_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Edits an existing Item entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('InventoryBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $erors = $editForm->getErrors();
        if ($editForm->isValid()) {
            if(empty($entity->getItemUnit())){
                $unit = $this->getDoctrine()->getRepository('SettingToolBundle:ProductUnit')->find(4);
                $entity->setItemUnit($unit);
                $entity->getMasterItem()->setProductUnit($unit);
            }
            if($entity->upload() && !empty($entity->getFile())){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            $this->getDoctrine()->getRepository('InventoryBundle:ItemGallery')->insertProductGallery($entity,$data);
            return $this->redirect($this->generateUrl('item'));
        }
        return $this->render('InventoryBundle:Item:edit.html.twig', array(
            'entity'      => $entity,
            'inventory'     => $inventory,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Item entity.
     *
     */
    public function updateWebAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('InventoryBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $editForm = $this->createWebForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            $this->getDoctrine()->getRepository('InventoryBundle:ItemGallery')->insertProductGallery($entity,$data);
            return $this->redirect($this->generateUrl('item_edit_web', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:Item:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Item entity.
     *
     */
    public function deleteAction(Item $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find item entity.');
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
        }
        return $this->redirect($this->generateUrl('item'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }
        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('item'));
    }

    public function barcodeAction(Request $request)
    {
        $data = $request->request->get('check');
        $em = $this->getDoctrine()->getManager();
        if(!empty($data)) {
            foreach ($data as $row) {
                $entities[] = $em->getRepository('InventoryBundle:Item')->find($row);
            }
        }
        return $this->render('InventoryBundle:Item:pre-barcode.html.twig', array(
            'entities'      => $entities
        ));

    }

    public function uploadItemImageAction(PurchaseVendorItem $item)
    {
        $entity = new ItemGallery();
        $option = $this->getUser()->getGlobalOption();
        $entity ->upload($option->getId(),$item->getId());
    }

    public function skuUpdateAction(Item $item)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $this->getDoctrine()->getRepository('InventoryBundle:Item')->skuUpdate($inventory,$item);
        return new JsonResponse($item->getName());
    }

    public function autoSearchAction(Request $request)
    {
        $item = trim($_REQUEST['q']);
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function autoBarcodeSearchAction(Request $request)
    {
        $item = trim($_REQUEST['q']);
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->searchBarcodeItem($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function autoSearchItemAllAction(Request $request)
    {
        $item = trim($_REQUEST['q']);
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->searchAutoCompleteAllItem($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function priceAction(Request $request)
    {
        $item = $request->request->get('item');
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Item')->find($item);
        return new JsonResponse(array('salesPrice' => $entity->getSalesPrice(),'webPrice'=>$entity->getWebPrice()));
    }

    public function updateWebPriceAction(Request $request)
    {
	    $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
    	$data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:Item')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

	    if($data['name'] == 'Barcode') {
            $barcode = trim($data['value']);
		    $existBarcode = $this->getDoctrine()->getRepository( 'InventoryBundle:Item' )->findBy( array('inventoryConfig'=>$config, 'barcode' => $barcode) );
		    if ( empty( $existBarcode ) ) {
			    $entity->setBarcode($barcode);
			    $em->flush();
		    }else{
                $this->get('session')->getFlashBag()->add(
                    'notice'," $barcode This barcode already used another item"
                );
            }

	    }elseif($data['name'] == 'Sku'){
		    $existSku = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findBy(array('inventoryConfig'=>$config,'sku' => $data['value']));
		    if(empty($existSku)){
			    $entity->setSku($data['value']);
			    $em->flush();
		    }
	    }else{
		    $process = 'set'.$data['name'];
		    $entity->$process($data['value']);
		    $em->flush();
	    }
        exit;
    }

    /**
     * Status a Page entity.
     *
     */
    public function webStatusAction(Request $request,Item $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findOneBy(array('inventoryConfig'=> $config,'id' => $entity->getId()));
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }
        $status = $entity->getIsWeb();
        if($status == 1){
            $entity->setIsWeb(0);
        } else{
            $entity->setIsWeb(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }


    public function vendorItemAction($vendor)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findBy(
            array(
                'inventoryConfig'=>$inventory,
                'vendor'=>$vendor)
        );
        $items = array();
        foreach ($entities as $entity):
            $item =$entity->getName();
            $items[] = array('value' => $entity->getId(),'text'=> $item);
        endforeach;
        return new JsonResponse($items);

    }

    public function updatePurchaseQuantityAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $items = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->findItemWithPurchaseQuantity($inventory);
        foreach ($items as $row){
           $item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->find($row['itemId']);
           $item->setPurchaseQuantity($row['quantity']);
           $em->flush();
        }
        return $this->redirect($this->generateUrl('item'));
    }

	public function updateStockQuantityAction()
	{
		set_time_limit(0);
		ignore_user_abort(true);
		$em = $this->getDoctrine()->getManager();
		$inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
		$items = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->findItemWithPurchaseQuantity($inventory);
		foreach ($items as $row){
			$item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->find($row['itemId']);
			$salesQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'sales');
			$salesReturnQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'salesReturn');
			$purchaseReturnQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'purchaseReturn');
			$damageQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'damage');
			$item->setPurchaseQuantity($row['quantity']);
			$item->setSalesQuantity($salesQnt);
			$item->setSalesQuantityReturn($salesReturnQnt);
			$item->setPurchaseQuantityReturn($purchaseReturnQnt);
			$item->setPurchaseQuantityReturn($damageQnt);
			$remainingQnt = ($item->getPurchaseQuantity() + $item->getSalesQuantityReturn()) - ($item->getSalesQuantity() + $item->getPurchaseQuantityReturn()+$item->getDamageQuantity());
			$item->setRemainingQnt($remainingQnt);
			$em->flush();
		}
		return $this->redirect($this->generateUrl('item'));
	}

	public function inlineUpdateAction(Request $request)
	{
		$data = $request->request->all();
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('InventoryBundle:PurchaseItem')->find($data['pk']);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
		}

		if($data['name'] == 'SalesPrice' and 0 < (float)$data['value']){
			$process = 'set'.$data['name'];
			$entity->$process((float)$data['value']);
			$entity->setSalesSubTotal((float)$data['value'] * $entity->getQuantity());
			$em->flush();
		}

		if($data['name'] == 'PurchasePrice' and 0 < (float)$data['value']){
			$entity->setPurchasePrice((float)$data['value']);
			$entity->setPurchaseSubTotal((float)$data['value'] * $entity->getQuantity());
			$em->flush();
			$em->getRepository('InventoryBundle:Purchase')->purchaseSimpleUpdate($entity->getPurchase());
		}
		$salesQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getPurchaseItemQuantity($entity,array('sales','damage','purchaseReturn'));
		if($data['name'] == 'Quantity' and $salesQnt <= (int)$data['value']){
			$entity->setQuantity((int)$data['value']);
			$entity->setPurchaseSubTotal((int)$data['value'] * $entity->getPurchasePrice());
			$entity->setSalesSubTotal((int)$data['value'] * $entity->getSalesPrice());
			$em->flush();
			$em->getRepository('InventoryBundle:Purchase')->purchaseSimpleUpdate($entity->getPurchase());
		}

		if($data['name'] == 'Barcode'){
			$existBarcode = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->findOneBy(array('inventoryConfig'=>$config,'barcode' => $data['value']));
			if(empty($existBarcode)){
				$process = 'set'.$data['name'];
				$entity->$process($data['value']);
				$em->flush();
			}
		}
		exit;

	}

	public function itemExportToCsvAction()
	{

		set_time_limit(0);
		ignore_user_abort(true);
		$em = $this->getDoctrine()->getManager();
		$global = $this->getUser()->getGlobalOption();
		$data = $_REQUEST;
		$array = array();

		$inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
		$entities = $em->getRepository('InventoryBundle:Item')->findWithSearch($inventory,$data);
		$entities = $entities->getQuery()->getResult();
		$array[] = 'Barcode,Model,Name,Category Name,Brand,Unit,Purchase,Purchase Return,Sales,Sales Return,Damage,Reaming,Avg Purchase,Avg Sales,Sales Price';
		/* @var $entity Item */

		foreach ($entities as $key => $entity){

			$unit = !empty($entity->getMasterItem()->getProductUnit()) ? $entity->getMasterItem()->getProductUnit()->getName():'';
			$category = !empty($entity->getMasterItem()->getCategory()) ? $entity->getMasterItem()->getCategory()->getName():'';
			$brand = !empty($entity->getBrand()) ? $entity->getBrand()->getName():'';
			$rows = array(
				$entity->getBarcode(),
                $entity->getModel(),
				$entity->getName(),
				$category,
                $brand,
				$unit,
				$entity->getPurchaseQuantity(),
				abs($entity->getPurchaseQuantityReturn()),
				abs($entity->getSalesQuantity()),
				abs($entity->getSalesQuantityReturn()),
				$entity->getDamageQuantity(),
				$entity->getRemainingQuantity(),
				$entity->getAvgPurchasePrice(),
				$entity->getAvgSalesPrice(),
				$entity->getSalesPrice(),
			);
			$array[] = implode(',', $rows);
		}
		$startDate = isset($data['startDate'])  ? $data['startDate'] : '';
		$compareStart = new \DateTime($startDate);
		$start =  $compareStart->format('d-m-Y');
		$fileName = $start.'-stock-item.csv';
		$content = implode("\n", $array);
		$response = new Response($content);
		$response->headers->set('Content-Type', 'text/csv');
		$response->headers->set('Content-Type', 'application/octet-stream');
		$response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
		return $response;
		exit;

	}

	public function itemExportToExcelAction()
	{

		set_time_limit(0);
		ignore_user_abort(true);
		$em = $this->getDoctrine()->getManager();
		$global = $this->getUser()->getGlobalOption();
		$data = $_REQUEST;
		$entities = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->gpProductWiseSales($global,$data);
		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

		$phpExcelObject->setActiveSheetIndex(0)
		               ->setCellValue('A1', 'Created')
		               ->setCellValue('B1', 'Order Date')
		               ->setCellValue('C1', 'Time of Order')
		               ->setCellValue('D1', 'Category')
		               ->setCellValue('E1', 'Item Sku')
		               ->setCellValue('F1', 'Item Name')
		               ->setCellValue('G1', 'GP Center')
		               ->setCellValue('H1', 'Circle')
		               ->setCellValue('I1', 'Region')
		               ->setCellValue('J1', 'District')
		               ->setCellValue('K1', 'Thana')
		               ->setCellValue('L1', 'Quantity')
		               ->setCellValue('M1', 'MRP')
		               ->setCellValue('N1', 'Vat')
		               ->setCellValue('O1', 'Commission')
		               ->setCellValue('P1', 'Method');
		$rowNo =2;
		foreach ($entities as $key => $entity){

			//  $com = round(($entity['dpPrice']*8)/100);

			$phpExcelObject->setActiveSheetIndex(0)
			               ->setCellValue("A{$rowNo}", $entity['createdBy'])
			               ->setCellValue("B{$rowNo}", $entity['created']->format('d-m-Y') )
			               ->setCellValue("C{$rowNo}", $entity['created']->format('h:i A') )
			               ->setCellValue("D{$rowNo}", $entity['categoryName'])
			               ->setCellValue("E{$rowNo}", $entity['itemSku'])
			               ->setCellValue("F{$rowNo}", $entity['itemName'])
			               ->setCellValue("G{$rowNo}", $entity['gpCenterName'])
			               ->setCellValue("H{$rowNo}", $entity['circle'])
			               ->setCellValue("I{$rowNo}", $entity['region'])
			               ->setCellValue("J{$rowNo}", $entity['district'])
			               ->setCellValue("K{$rowNo}", $entity['thana'])
			               ->setCellValue("L{$rowNo}", $entity['quantity'])
			               ->setCellValue("M{$rowNo}", $entity['subTotal'])
			               ->setCellValue("N{$rowNo}", $entity['vat'])
			               ->setCellValue("O{$rowNo}", $entity['gpCommission'])
			               ->setCellValue("P{$rowNo}", $entity['methodName']);
			$rowNo++;
		}
		$phpExcelObject->getActiveSheet()->setTitle('GP-Center Product Details');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$phpExcelObject->setActiveSheetIndex(0);

		// create the writer
		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		// create the response
		$response = $this->get('phpexcel')->createStreamedResponse($writer);
		// adding headers
		$dispositionHeader = $response->headers->makeDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT,
			'GP-ExpressDetails.xlsx'
		);
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		$response->headers->set('Content-Disposition', $dispositionHeader);

		return $response;

	}


}
