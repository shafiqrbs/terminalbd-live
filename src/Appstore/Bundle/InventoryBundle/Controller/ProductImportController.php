<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\SalesImport;
use Appstore\Bundle\InventoryBundle\Form\SalesImportType;

/**
 * SalesImport controller.
 *
 */
class ProductImportController extends Controller
{

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:SalesImport')->findBy(array('inventoryConfig'=>$inventory),array('updated'=>'DESC'));
        return $this->render('InventoryBundle:SalesImport:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function createAction(Request $request)
    {
        $entity = new SalesImport();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($inventory);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('inventory_salesimport'));
        }

        return $this->render('InventoryBundle:SalesImport:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a SalesImport entity.
     *
     * @param SalesImport $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SalesImport $entity)
    {
        $form = $this->createForm(new SalesImportType(), $entity, array(
            'action' => $this->generateUrl('inventory_salesimport_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function newAction()
    {
        $entity = new SalesImport();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:SalesImport:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function excelDataImportAction(SalesImport $SalesImport)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $importer = $this->get('appstore_inventory_sales_import');
        $importer->setSalesImport($SalesImport);
        $reader = $this->get('appstore_inventory.importer.sales_excel_data_reader');
        $file =  realpath($SalesImport->getAbsolutePath());
        $importer->import($reader->getData($file));

        $SalesImport->setProgress('migrated');
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',"Data has been migration successfully"
        );
        return $this->redirect($this->generateUrl('inventory_salesimport'));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Sales')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalesImport entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:SalesImport:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a SalesImport entity.
    *
    * @param SalesImport $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SalesImport $entity)
    {
        $form = $this->createForm(new SalesImportType(), $entity, array(
            'action' => $this->generateUrl('inventory_salesimport_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:SalesImport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalesImport entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_salesimport_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:SalesImport:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),

        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function deleteAction(SalesImport $salesImport)
    {
        $em = $this->getDoctrine()->getManager();
        if ($salesImport) {
            $em->remove($salesImport);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('inventory_salesimport'));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function updatedAction(SalesImport $salesImport)
    {

        $em = $this->getDoctrine()->getManager();
        $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($salesImport->getSales(),'import');
        $salesImport->setProgress('updated');
        $em->persist($salesImport);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been updated successfully"
        );

        return $this->render('InventoryBundle:SalesImport:show.html.twig', array(
            'entity'      => $salesImport->getSales(),

        ));

    }

}
