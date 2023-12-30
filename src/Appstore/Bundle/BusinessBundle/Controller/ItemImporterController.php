<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\ItemImport;
use Appstore\Bundle\BusinessBundle\Form\ItemImporterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



/**
 * ItemImporter controller.
 *
 */
class ItemImporterController extends Controller
{

    /**
     * Lists all ItemImporter entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:ItemImport')->findBy(array('businessConfig'=>$config),array('updated'=>'DESC'));
        return $this->render('BusinessBundle:ItemImporter:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ItemImporter entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ItemImport();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
            $entity->setBusinessConfig($config);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('business_itemimporter'));
        }
        return $this->render('BusinessBundle:ItemImporter:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ItemImporter entity.
     *
     * @param ItemImport $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ItemImport $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new ItemImporterType($globalOption), $entity, array(
            'action' => $this->generateUrl('business_itemimporter_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
                'enctype' => 'multipart/form-data',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ItemImporter entity.
     *
     */
    public function newAction()
    {
        $entity = new ItemImport();
        $form   = $this->createCreateForm($entity);

        return $this->render('BusinessBundle:ItemImporter:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function excelDataImportAction(ItemImport $ItemImporter)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $importer = $this->get('appstore_business_item_import');
        $importer->setItemImport($ItemImporter);
        $reader = $this->get('appstore.business.item.import.event.listener');
        $file =  realpath($ItemImporter->getAbsolutePath());
        $data = $reader->getData($file);
        $importer->import($data);
        $ItemImporter->setProgress('migrated');
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been migration successfully"
        );
        $em->flush();
        return $this->redirect($this->generateUrl('business_itemimporter'));
    }

    /**
     * Displays a form to edit an existing ItemImporter entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusinessBundle:ItemImport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemImporter entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('BusinessBundle:ItemImporter:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a ItemImporter entity.
    *
    * @param ItemImport $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ItemImport $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new ItemImporterType($globalOption), $entity, array(
            'action' => $this->generateUrl('business_itemimporter_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing ItemImporter entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusinessBundle:ItemImport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemImporter entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('business_itemimporter_edit', array('id' => $id)));
        }

        return $this->render('BusinessBundle:ItemImporter:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),

        ));
    }
    /**
     * Deletes a ItemImporter entity.
     *
     */
    public function deleteAction(ItemImport $ItemImporter)
    {
        $em = $this->getDoctrine()->getManager();
        if ($ItemImporter) {
            $ItemImporter->removeUpload();
            $em->remove($ItemImporter);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('business_itemimporter'));
    }

    public function updatedAction(ItemImport $ItemImporter)
    {



    }
    public function resetedAction(ItemImport $en)
    {

    }

}
