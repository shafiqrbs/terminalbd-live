<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineMinimumStock;
use Appstore\Bundle\MedicineBundle\Form\MedicineMinimumStockType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * MedicineMinimumStock controller.
 *
 */
class MedicineMinimumStockController extends Controller
{

    /**
     * Lists all MedicineMinimumStock entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new MedicineMinimumStock();
        $form = $this->createCreateForm($entity);
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineMinimumStock')->findBy(array('medicineConfig' => $config));
        return $this->render('MedicineBundle:MedicineMinimumStock:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Creates a new MedicineMinimumStock entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MedicineMinimumStock();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineMinimumStock')->findBy(array('medicineConfig' => $config));
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $entity->setMedicineConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('medicine_minimum', array('id' => $entity->getId())));
        }

        return $this->render('MedicineBundle:MedicineMinimumStock:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MedicineMinimumStock entity.
     *
     * @param MedicineMinimumStock $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineMinimumStock $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $em = $this->getDoctrine()->getRepository('MedicineBundle:MedicineMinimumStock');
        $form = $this->createForm(new MedicineMinimumStockType($em,$config), $entity, array(
            'action' => $this->generateUrl('medicine_minimum_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing MedicineMinimumStock entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineMinimumStock')->findBy(array('medicineConfig' => $config));

        $entity = $em->getRepository('MedicineBundle:MedicineMinimumStock')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineMinimumStock entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('MedicineBundle:MedicineMinimumStock:index.html.twig', array(
            'entities'      => $entities,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a MedicineMinimumStock entity.
    *
    * @param MedicineMinimumStock $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MedicineMinimumStock $entity)
    {
        $form = $this->createForm(new MedicineMinimumStockType(), $entity, array(
            'action' => $this->generateUrl('medicine_minimum_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing MedicineMinimumStock entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineMinimumStock')->findBy(array('medicineConfig' => $config));

        $entity = $em->getRepository('MedicineBundle:MedicineMinimumStock')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineMinimumStock entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            return $this->redirect($this->generateUrl('medicine_minimum'));
        }

        return $this->render('MedicineBundle:MedicineMinimumStock:index.html.twig', array(
            'entities'      => $entities,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a MedicineMinimumStock entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineMinimumStock')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineMinimumStock entity.');
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

        return $this->redirect($this->generateUrl('medicine_minimum'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineMinimumStock')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('medicine_minimum'));
    }

    public function processAction(MedicineMinimumStock $stock)
    {

	    set_time_limit(0);
	    ignore_user_abort(true);
	    $em = $this->getDoctrine()->getManager();
	    $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
	    $items = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findBy(array('medicineConfig'=>$config,'unit'=> $stock->getUnit()));
		foreach ($items as $item){
			$item->setMinQuantity($stock->getMinQuantity());
			$em->flush();
		}
	    return $this->redirect($this->generateUrl('medicine_minimum'));


    }



}
