<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Setting\Bundle\ToolBundle\Entity\MedicineImport;
use Setting\Bundle\ToolBundle\Form\MedicineImportType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\ItemColor;
use Setting\Bundle\ToolBundle\Form\ColorType;

/**
 * ItemColor controller.
 *
 */
class MedicineImportController extends Controller
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
	 * Lists all MedicineImport entities.
	 *
	 */
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository('SettingToolBundle:MedicineImport')->findBy(array(),array('updated'=>'DESC'));
		return $this->render('SettingToolBundle:MedicineImport:index.html.twig', array(
			'entities' => $entities,
		));
	}



	/**
	 * Creates a new MedicineImport entity.
	 *
	 */
	public function createAction(Request $request)
	{
		$entity = new MedicineImport();
		$form = $this->createCreateForm($entity);
		$form->handleRequest($request);
		$data = $request->request->all();
		if ($form->isValid()) {

			$em = $this->getDoctrine()->getManager();

			/** @var $file UploadedFile */

			$file = $entity->getFile();
			$fileName = $file->getClientOriginalName();
			$imgName =  uniqid(). '.' .$fileName;
			// moves the file to the directory where brochures are stored
			$file->move(
				$entity->getUploadDir(),
				$imgName
			);
			$entity->setPath($imgName);
			$em->persist($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been added successfully"
			);
		//	$this->getDoctrine()->getRepository('SettingToolBundle:MedicineImport')->fileUpload($entity,$data);
			return $this->redirect($this->generateUrl('medicine_import'));
		}

		return $this->render('SettingToolBundle:MedicineImport:new.html.twig', array(
			'entity' => $entity,
			'form'   => $form->createView(),
		));
	}

	/**
	 * Creates a form to create a MedicineImport entity.
	 *
	 * @param MedicineImport $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createCreateForm(MedicineImport $entity)
	{
		$form = $this->createForm(new MedicineImportType(), $entity, array(
			'action' => $this->generateUrl('medicine_import_create'),
			'method' => 'POST',
		));
		return $form;
	}

	/**
	 * Displays a form to create a new MedicineImport entity.
	 *
	 */
	public function newAction()
	{
		$entity = new MedicineImport();
		$form   = $this->createCreateForm($entity);

		return $this->render('SettingToolBundle:MedicineImport:new.html.twig', array(
			'entity' => $entity,
			'form'   => $form->createView(),
		));
	}





	public function excelDataImportAction(MedicineImport $excelImporter)
	{

		//set_time_limit(0);
		ini_set('max_execution_time', 0);
		ignore_user_abort(true);
		$em = $this->getDoctrine()->getManager();
		$importer = $this->get('appstore_medicine_importer_excel');
		$reader = $this->get('appstore_medicine.importer.excel_data_reader');
		$file =  realpath($excelImporter->getAbsolutePath());
		$data = $reader->getData($file);
		if($importer->isValid($data)) {
			$importer->import($data);
			$excelImporter->setProgress('migrated');
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been migration successfully"
			);
			$em->flush();
		}else{
			$this->get('session')->getFlashBag()->add(
				'error',"Excel File memo no null or exist in your system"
			);
			$em->flush();
		}
		return $this->redirect($this->generateUrl('medicine_import'));
	}


	public function deleteAction(MedicineImport $excelImporter)
	{
		$em = $this->getDoctrine()->getManager();
		if ($excelImporter) {
			$excelImporter->removeUpload();
			$em->remove($excelImporter);
			$em->flush();
		}
		return $this->redirect($this->generateUrl('medicine_import'));
	}



    /**
     * Creates a new Vendor entity.
     *
     */
    public function uploadAction(Request $request) {

        $data = explode( ',', $request->cookies->get( 'barcodes' ) );
        $em = $this->getDoctrine()->getManager();
        if ( is_null( $data ) ) {
            return $this->redirect( $this->generateUrl( 'medicine_stock_short_item' ) );
        }
        $medicines = array();

        foreach ($data as $key => $value ){
            $medicine = $em->getRepository('MedicineBundle:MedicineBrand')->find($value);
            if($medicine){
                $medicines[$value] = $medicine;
            }
        }
        return $this->render('SettingToolBundle:MedicineImport:upload.html.twig', array(
            'medicines' => $medicines,
        ));
    }

    /**
     * Creates a new Vendor entity.
     *
     */
    public function uploadCreateAction(Request $request) {

        $data = explode( ',', $request->cookies->get( 'barcodes' ) );
        $em = $this->getDoctrine()->getManager();
        if ( is_null( $data ) ) {
            return $this->redirect( $this->generateUrl( 'medicinebrand'));
        }
        $file = $request->files->get('file');
        if($file){
            foreach ($data as $key => $value ){
                $entity = $em->getRepository('MedicineBundle:MedicineBrand')->find($value);
                if($entity){
                    if($entity->getWebPath()){
                        $entity->removeUpload();
                    }
                    $img = $file;
                    $fileName = $img->getClientOriginalName();
                    $imgName = uniqid() . '.' . $fileName;
                    $path = $entity->getUploadDir() . $imgName;
                    if (!file_exists($entity->getUploadDir())) {
                        mkdir($entity->getUploadDir(), 0777, true);
                    }
                    $this->get('helper.imageresizer')->resizeImage(480, $path, $img);
                    $entity->setPath($imgName);
                    $em->persist($entity);
                    $em->flush();
                }
            }
        }
        return $this->redirect( $this->generateUrl( 'medicinebrand'));
    }

}
