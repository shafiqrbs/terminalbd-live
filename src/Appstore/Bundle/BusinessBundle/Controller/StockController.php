<?php

namespace Appstore\Bundle\BusinessBundle\Controller;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessStockHistory;
use Appstore\Bundle\BusinessBundle\Entity\WearHouse;
use Appstore\Bundle\BusinessBundle\Form\StockType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
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
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        $type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
        $category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('businessConfig' => $config ,'status'=>1));
        $selected = explode(',', $request->cookies->get('barcodes', ''));
        $wearHouseStocks = $this->getDoctrine()->getRepository(BusinessStockHistory::class)->getWearHouseStocks($pagination);
        $wearhouses = $this->getDoctrine()->getRepository(WearHouse::class)->findBy(array('businessConfig' => $config),array('name'=>'ASC'));

        return $this->render('BusinessBundle:Stock:index.html.twig', array(
            'pagination' => $pagination,
            'wearhouses' => $wearhouses,
            'wearHouseStock' => $wearHouseStocks,
            'types' => $type,
            'selected' => $selected,
            'categories' => $category,
            'config' => $config,
            'searchForm' => $data,
        ));

    }



    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */
    public function shortListAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        $type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
        $category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('businessConfig' => $config ,'status'=>1));
        return $this->render('BusinessBundle:Stock:shortlist.html.twig', array(
            'pagination' => $pagination,
            'types' => $type,
            'categories' => $category,
            'config' => $config,
            'searchForm' => $data,
        ));

    }


    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */

    public function stockLedgerAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if(empty($data)){
            $compare = new \DateTime();
            $end =  $compare->format('j');
            $data['monthYear'] = $compare->format('Y-m-d');
            $data['month'] =  $compare->format('F');
            $data['year'] = $compare->format('Y');
        }else{
            $month = $data['month'];
            $year = $data['year'];
            $compare = new \DateTime("{$year}-{$month}-01");
            $end =  $compare->format('t');
            $data['monthYear'] = $compare->format('Y-m-d');
        }
        $product = "";
        $openingBalance = array();
        $daily = '';
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getProducts($config->getId());
        if(isset($data['name']) and !empty($data['name'])){
            for ($i = 1; $end >= $i ; $i++ ){
                $no = sprintf("%s", str_pad($i,2, '0', STR_PAD_LEFT));
                $start =  $compare->format("Y-m-{$no}");
                $day =  $compare->format("{$no}-m-Y");
                $data['startDate'] = $start;
                $openingBalance[$day] = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->openingDailyQuantity($config,$data);
            }
            $daily = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStockHistory')->monthlyStockLedger($config,$data);
        }
        if(empty($data['pdf'])){
            return $this->render('BusinessBundle:Stock:productLedger.html.twig', array(
                'globalOption'                  => $this->getUser()->getGlobalOption(),
                'openingBalance'                => $openingBalance,
                'particulars'                   => $particulars,
                'entity'                        => $daily,
                'searchForm'                    => $data,
            ));
        }else{
            $html = $this->renderView(
                'BusinessBundle:Stock:productLedgerPdf.html.twig', array(
                    'option'                  => $this->getUser()->getGlobalOption(),
                    'openingBalance'                => $openingBalance,
                    'particulars'                => $particulars,
                    'entity'                        => $daily,
                    'searchForm'                    => $data,
                )
            );
            $this->downloadPdf($html,'monthlyCashPdf.pdf');
        }
    }


    /**
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */


    public function stockItemHistoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $pagination = '';
        $entity = '';
        if(isset($data['name']) and !empty($data['name'])){
            $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->findOneBy(array('businessConfig' => $config,'name'=> $data['name']));
        }
        if(!empty($data['name']) and $data['report'] == 'Purchase'){

            $entities = $em->getRepository('BusinessBundle:BusinessParticular')->getPurchaseDetails($config,$entity);
            $pagination = $this->paginate($entities);
        }
        if(!empty($data['name']) and $data['report'] == 'Purchase-Return'){
            $entities = $em->getRepository('BusinessBundle:BusinessParticular')->getPurchaseReturnDetails($config,$entity);
            $pagination = $this->paginate($entities);
        }
        if(!empty($data['name']) and $data['report'] == 'Sales'){
            $entities = $em->getRepository('BusinessBundle:BusinessParticular')->getSalesDetails($config,$entity);
            $pagination = $this->paginate($entities);
        }
        if(!empty($data['name']) and $data['report'] == 'Sales-Return'){
            $entities = $em->getRepository('BusinessBundle:BusinessParticular')->getSalesReturnDetails($config,$entity);
            $pagination = $this->paginate($entities);
        }
        if(!empty($data['name']) and $data['report'] == 'Damage'){
            $entities = $em->getRepository('BusinessBundle:BusinessParticular')->getDamageDetails($config,$entity);
            $pagination = $this->paginate($entities);
        }

        return $this->render('BusinessBundle:Stock:itemDetails.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'searchForm' => $data,
        ));
    }

    /**
     * Displays a form to create a new Vendor entity.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */

    public function newAction()
    {
        $entity = new BusinessParticular();
	    $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $stockFormat = $config->getStockFormat();
        $form   = $this->createCreateForm($entity);
        return $this->render('BusinessBundle:Stock:new.html.twig', array(
            'entity' => $entity,
            'stockFormat' => $stockFormat,
            'form'   => $form->createView()
        ));
    }


    /**
     * Creates a new BusinessParticular entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $config BusinessConfig */

        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = new BusinessParticular();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
	    if(!empty($entity->getWearHouse()) and !empty($entity->getWearHouse()->getWearHouseCode())) {
		    $name = $entity->getWearHouse()->getShortCode() . '-' . $entity->getName();
		    $checkName = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessParticular' )->findOneBy( array(
			    'businessConfig' => $config,
			    'name'           => $name
		    ) );
	    }else{
		    $name = $entity->getName();
		    $checkName = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessParticular' )->findOneBy( array(
			    'businessConfig' => $config,
			    'name'           => $name
		    ) );
	    }
        if ($form->isValid() and empty($checkName)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setBusinessConfig($config);
            if(empty($entity->getUnit())){
                $unit = $this->getDoctrine()->getRepository('SettingToolBundle:ProductUnit')->find(4);
                $entity->setUnit($unit);
            }
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('business_stock_new'));
        }
	    $stockFormat = $config->getStockFormat();
        $this->get('session')->getFlashBag()->add(
            'error',"Required field does not input"
        );
        return $this->render('BusinessBundle:Stock:new.html.twig', array(
            'entity'        => $entity,
            'stockFormat'   => $stockFormat,
            'form'          => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param BusinessParticular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessParticular $entity)
    {

        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new StockType($option), $entity, array(
            'action' => $this->generateUrl('business_stock_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to edit an existing Particular entity.
     *
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
	    $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $stockFormat = $config->getStockFormat();
        $editForm = $this->createEditForm($entity);
        return $this->render('BusinessBundle:Stock:new.html.twig', array(
            'entity'            => $entity,
            'stockFormat'       => $stockFormat,
            'form'              => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Particular entity.
     *
     * @param BusinessParticular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessParticular $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new StockType($option), $entity, array(
            'action' => $this->generateUrl('business_stock_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Particular entity.
     *
     */
    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($id);
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
            return $this->redirect($this->generateUrl('business_stock'));
        }
        return $this->render('BusinessBundle:Stock:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Particular entity.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */

    public function deleteAction(BusinessParticular $entity)
    {
        $em = $this->getDoctrine()->getManager();

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
        return $this->redirect($this->generateUrl('business_stock'));
    }

   
    /**
     * Status a Page entity.
     *
     */

    public function statusAction(BusinessParticular $entity)
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
        return $this->redirect($this->generateUrl('business_stock'));
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find particular entity.');
        }
	    if('openingQuantity' == $data['name']) {
		    $setField = 'set' . $data['name'];
		    $quantity = abs($data['value']);
		    $entity->$setField($quantity);
		    $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->remainingQnt($entity);
		    $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularHistory')->processStockQuantity($entity,"opening");
	    }elseif('name' == $data['name']) {
		    $entity->setName($data['value']);
	    }else{
		    $setField = 'set' . $data['name'];
		    $entity->$setField(abs($data['value']));
	    }
        $em->flush();
        exit;

    }

    public function production(BusinessParticular $particular)
    {

    }

    public function transfer(BusinessParticular $particular)
    {

    }

    public function allStockDeleteAction()
    {
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig()->getId();
        $em = $this->getDoctrine()->getManager();
        $account = $em->createQuery('DELETE BusinessBundle:BusinessParticular e WHERE e.businessConfig = '.$config);
        if(!empty($account)){
            $account->execute();
        }
        return $this->redirect($this->generateUrl('business_stock'));
    }

	public function autoSearchAction(Request $request)
	{
		$item = $_REQUEST['q'];
		if ($item) {
			$inventory = $this->getUser()->getGlobalOption()->getBusinessConfig();
			$item = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->searchAutoComplete($inventory,$item);
		}
		return new JsonResponse($item);
	}

	public function searchNameAction($stock)
	{
		return new JsonResponse(array(
			'id'=> $stock,
			'text'=> $stock
		));
	}

	public function stockQuantityUpdateAction()
	{
		set_time_limit(0);
		ignore_user_abort(true);
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getBussinessConfig();
		$items = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->findBy(array('businessConfig'=>$config));

		/* @var BusinessParticular $item */

		foreach ($items as $item){
			$this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->updateRemovePurchaseQuantity($item,'');
			$this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->updateRemovePurchaseQuantity($item,'sales');
			$this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->updateRemovePurchaseQuantity($item,'sales-return');
			$this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->updateRemovePurchaseQuantity($item,'purchase-return');
			$this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->updateRemovePurchaseQuantity($item,'damage');
		}
		return $this->redirect($this->generateUrl('medicine_stock'));
	}

    public function stockExcelAction()
    {

        set_time_limit(0);
        ignore_user_abort(true);
        $array = array();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->findWithSearch($config,$data);
        $entities = $entities->getQuery()->getResult();
        $array[] = 'SKU,Name,Quantity,Purchase Price,Total Purchase ,Sales Price,Total Sales';

        /* @var $entity BusinessParticular */

        foreach ($entities as $key => $entity){

            $category = empty($entity->getCategory()) ? '': $entity->getCategory()->getName();
            $totalPurchase = ( $entity->getPurchasePrice() * $entity->getRemainingQuantity());
            $totalSales = ( $entity->getSalesPrice() * $entity->getRemainingQuantity());
            $rows = array(
                $entity->getParticularCode(),
                $entity->getName(),
                $entity->getRemainingQuantity(),
                $entity->getPurchasePrice(),
                $totalPurchase,
                $entity->getSalesPrice(),
                $totalSales

            );
            $array[] = implode(',', $rows);
        }
        $compareStart = new \DateTime("now");
        $start =  $compareStart->format('d-m-Y');
        $fileName = $start.'stock.csv';
        $content = implode("\n", $array);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
        return $response;
    }

    public function downloadPdf($html,$fileName = '')
    {
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename={$fileName}");
        echo $pdf;
        return new Response('');
    }



}
